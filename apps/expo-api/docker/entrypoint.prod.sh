#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Production Starting..."
echo "========================================="

cd /var/www/html

# Create .env file from environment variables if not exists
if [ ! -f .env ]; then
    echo ">> Creating .env file from environment variables..."
    env | grep -E '^(APP_|DB_|REDIS_|QUEUE_|CACHE_|LOG_|AUTH_SERVICE_|RATE_LIMIT_|MAIL_|SESSION_)' > .env 2>/dev/null || true
    grep -q "^APP_KEY=" .env 2>/dev/null || echo "APP_KEY=" >> .env
fi

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Wait for MySQL
echo ">> Waiting for MySQL at ${DB_HOST:-expo-mysql}:${DB_PORT:-3306}..."
max_retries=60
count=0
while ! mysqladmin ping -h"${DB_HOST:-expo-mysql}" -P"${DB_PORT:-3306}" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo ">> ERROR: MySQL not available after ${max_retries} attempts"
        echo ">> Continuing anyway..."
        break
    fi
    echo ">> Waiting for MySQL... ($count/$max_retries)"
    sleep 2
done
echo ">> MySQL check completed!"

# Wait for Auth Service to be ready
if [ -n "$AUTH_SERVICE_URL" ]; then
    echo ">> Waiting for Auth Service at ${AUTH_SERVICE_URL}..."
    auth_retries=30
    auth_count=0
    while ! php -r "echo @file_get_contents('${AUTH_SERVICE_URL}/api/health') ? 'ok' : 'fail';" 2>/dev/null | grep -q 'ok'; do
        auth_count=$((auth_count + 1))
        if [ $auth_count -ge $auth_retries ]; then
            echo ">> WARNING: Auth Service not ready after ${auth_retries} attempts"
            echo ">> Continuing anyway..."
            break
        fi
        echo ">> Waiting for Auth Service... ($auth_count/$auth_retries)"
        sleep 3
    done
    echo ">> Auth Service check completed!"
fi

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Clear old cache
echo ">> Clearing old cache..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo ">> Migration warning (might already be up to date)"

# Seed
echo ">> Running seeders..."
php artisan db:seed --force --no-interaction 2>/dev/null || echo ">> Seeding skipped (might already be seeded)"

# DO NOT cache config - we rely on environment variables at runtime
# Only cache routes and views for performance
echo ">> Caching routes and views..."
php artisan route:cache --no-interaction 2>&1 || echo ">> Route cache warning"
php artisan view:cache --no-interaction 2>&1 || echo ">> View cache warning"
echo ">> Config cache SKIPPED (using env vars at runtime)"

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Expo API Ready! (Production)"
echo "========================================="

exec "$@"
