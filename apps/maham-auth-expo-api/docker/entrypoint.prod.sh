#!/bin/sh
set -e

echo "========================================="
echo "  Auth Service - Production Starting..."
echo "========================================="

cd /var/www/html

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Set permissions early
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for MySQL
echo ">> Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
max_retries=30
count=0
while ! nc -z "${DB_HOST:-mysql}" "${DB_PORT:-3306}" 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo ">> WARNING: MySQL not available after ${max_retries} attempts"
        echo ">> Continuing anyway..."
        break
    fi
    echo ">> Waiting for MySQL... ($count/$max_retries)"
    sleep 2
done
echo ">> MySQL check completed!"

# Clear ALL cache
echo ">> Clearing all cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan event:clear 2>/dev/null || true
rm -rf bootstrap/cache/*.php 2>/dev/null || true

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force --no-interaction 2>&1 || echo ">> Key generation skipped"
fi

# Generate JWT_SECRET if missing
if [ -z "$JWT_SECRET" ] || [ "$JWT_SECRET" = "" ]; then
    echo ">> Generating JWT_SECRET..."
    php artisan jwt:secret --force --no-interaction 2>/dev/null || echo ">> JWT secret generation skipped"
fi

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo ">> Migration warning (might already be up to date)"

# Seed only if needed (check if roles table is empty)
echo ">> Checking if seeding is needed..."
ROLE_COUNT=$(php artisan tinker --execute="echo \App\Models\Role::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$ROLE_COUNT" = "0" ] || [ -z "$ROLE_COUNT" ]; then
    echo ">> Running seeders..."
    php artisan db:seed --force --no-interaction 2>/dev/null || echo ">> Seeding skipped"
fi

# DO NOT cache config - we rely on environment variables at runtime
# Only cache routes for performance
echo ">> Caching routes..."
php artisan route:cache --no-interaction 2>&1 || echo ">> Route cache warning"
echo ">> Config cache SKIPPED (using env vars at runtime)"

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Final permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Auth Service Ready! (Production)"
echo "========================================="
echo ">> APP_DEBUG: ${APP_DEBUG:-false}"
echo ">> APP_ENV: ${APP_ENV:-production}"
echo ">> DB_HOST: ${DB_HOST:-mysql}"
echo "========================================="

exec "$@"
