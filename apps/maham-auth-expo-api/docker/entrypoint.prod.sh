#!/bin/bash
set -e

echo "========================================="
echo "  Auth Service - Production Starting..."
echo "========================================="

cd /var/www/html

# Wait for MySQL with better error handling
echo ">> Waiting for MySQL at ${DB_HOST:-auth-mysql}:${DB_PORT:-3306}..."
max_retries=60
count=0
while ! mysqladmin ping -h"${DB_HOST:-auth-mysql}" -P"${DB_PORT:-3306}" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo ">> ERROR: MySQL not available after ${max_retries} attempts"
        echo ">> Continuing anyway - migrations might fail..."
        break
    fi
    echo ">> Waiting for MySQL... ($count/$max_retries)"
    sleep 2
done
echo ">> MySQL check completed!"

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Generate JWT_SECRET if missing
if [ -z "$JWT_SECRET" ] || [ "$JWT_SECRET" = "" ]; then
    echo ">> Generating JWT_SECRET..."
    php artisan jwt:secret --force --no-interaction 2>/dev/null || echo ">> JWT secret generation skipped"
fi

# Clear old cache before caching
echo ">> Clearing old cache..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo ">> Migration warning (might already be up to date)"

# Seed only if needed (check if roles table is empty)
echo ">> Checking if seeding is needed..."
ROLE_COUNT=$(php artisan tinker --execute="echo \App\Models\Role::count();" 2>/dev/null || echo "0")
if [ "$ROLE_COUNT" = "0" ] || [ -z "$ROLE_COUNT" ]; then
    echo ">> Running seeders..."
    php artisan db:seed --force --no-interaction 2>/dev/null || echo ">> Seeding skipped"
fi

# Production cache optimization
echo ">> Caching configuration..."
php artisan config:cache --no-interaction 2>&1 || echo ">> Config cache warning"
php artisan route:cache --no-interaction 2>&1 || echo ">> Route cache warning"
php artisan view:cache --no-interaction 2>&1 || echo ">> View cache warning"
php artisan event:cache --no-interaction 2>&1 || echo ">> Event cache warning"

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Auth Service Ready! (Production)"
echo "========================================="

exec "$@"
