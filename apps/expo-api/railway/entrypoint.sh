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
    env | grep -E '^(APP_|DB_|REDIS_|QUEUE_|CACHE_|LOG_|AUTH_SERVICE_|RATE_LIMIT_|MAIL_|SESSION_|ONESIGNAL_|BCRYPT_|FILESYSTEM_|SERVICE_)' | while IFS='=' read -r key value; do
        echo "${key}=\"${value}\""
    done > .env 2>/dev/null || true
    grep -q "^APP_KEY=" .env 2>/dev/null || echo 'APP_KEY=""' >> .env
fi

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Replace {{PORT}} placeholder in nginx config with actual Railway PORT
sed "s/{{PORT}}/$RAILWAY_PORT/g" /etc/nginx/nginx-template.conf > /etc/nginx/sites-available/default

# Remove default nginx config if exists
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

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

# Seed only if needed (check if categories table has data)
CATEGORY_COUNT=$(php artisan tinker --execute="echo \App\Models\Category::count();" 2>/dev/null || echo "0")
if [ "$CATEGORY_COUNT" = "0" ] || [ -z "$CATEGORY_COUNT" ]; then
    echo ">> Running seeders (first time)..."
    php artisan db:seed --force --no-interaction 2>&1 || echo ">> Seeding warning"
else
    echo ">> Seeders skipped (data already exists: $CATEGORY_COUNT categories)"
fi

# Production cache optimization
# DO NOT cache config - we rely on environment variables at runtime
echo ">> Caching routes and views..."
php artisan config:clear --no-interaction 2>&1 || true
php artisan route:cache --no-interaction 2>&1 || true
php artisan view:cache --no-interaction 2>&1 || true
php artisan event:cache --no-interaction 2>&1 || true
echo ">> Config cache SKIPPED (using env vars at runtime)"

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Expo API Ready! (Railway)"
echo "  Listening on port: $RAILWAY_PORT"
echo "========================================="

# Hand off to CMD (supervisord manages php-fpm + nginx + queue + scheduler)
exec "$@"
