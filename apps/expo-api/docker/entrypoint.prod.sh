#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Production Starting..."
echo "========================================="

# Wait for MySQL
echo ">> Waiting for MySQL..."
max_retries=30
count=0
while ! mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo ">> ERROR: MySQL not available after ${max_retries} attempts"
        exit 1
    fi
    echo ">> Waiting for MySQL... ($count/$max_retries)"
    sleep 3
done
echo ">> MySQL is ready!"

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force

# Seed
echo ">> Running seeders..."
php artisan db:seed --force 2>/dev/null || true

# Production cache optimization
echo ">> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage link
php artisan storage:link --force 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Expo API Ready! (Production)"
echo "========================================="

exec "$@"
