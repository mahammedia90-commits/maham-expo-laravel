#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Railway Starting..."
echo "========================================="

cd /var/www/html

# Railway provides PORT dynamically
RAILWAY_PORT="${PORT:-8080}"
echo ">> Railway PORT: $RAILWAY_PORT"

# Create .env file from environment variables if not exists
if [ ! -f .env ]; then
    echo ">> Creating .env file from environment variables..."
    env | grep -E '^(APP_|DB_|REDIS_|QUEUE_|CACHE_|LOG_|AUTH_SERVICE_|RATE_LIMIT_|MAIL_|SESSION_)' > .env 2>/dev/null || true
    grep -q "^APP_KEY=" .env 2>/dev/null || echo "APP_KEY=" >> .env
fi

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Wait for MySQL (Railway MySQL or external DB)
if [ -n "$DB_HOST" ]; then
    echo ">> Waiting for MySQL at $DB_HOST:${DB_PORT:-3306}..."
    max_retries=30
    count=0
    while ! mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" --silent 2>/dev/null; do
        count=$((count + 1))
        if [ $count -ge $max_retries ]; then
            echo ">> WARNING: MySQL not available after ${max_retries} attempts, continuing anyway..."
            break
        fi
        echo ">> Waiting for MySQL... ($count/$max_retries)"
        sleep 3
    done
    echo ">> MySQL check complete!"
fi

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo ">> Migration warning (may already be up to date)"

# Seed
echo ">> Running seeders..."
php artisan db:seed --force --no-interaction 2>/dev/null || echo ">> Seeding skipped (might already be seeded)"

# Production cache optimization
echo ">> Caching configuration..."
php artisan config:cache --no-interaction 2>&1 || true
php artisan route:cache --no-interaction 2>&1 || true
php artisan view:cache --no-interaction 2>&1 || true
php artisan event:cache --no-interaction 2>&1 || true

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Expo API Ready! (Railway)"
echo "  Listening on port: $RAILWAY_PORT"
echo "========================================="

# Start queue worker in background
echo ">> Starting queue worker in background..."
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 &

# Start scheduler in background
echo ">> Starting scheduler in background..."
(while true; do php artisan schedule:run --no-interaction 2>&1; sleep 60; done) &

# Start Laravel on Railway PORT (foreground - keeps container alive)
echo ">> Starting Laravel directly on port $RAILWAY_PORT..."
php artisan serve --host=0.0.0.0 --port=$RAILWAY_PORT
