#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Starting..."
echo "========================================="

# Copy Docker env if .env.docker exists (volume mount overrides .env)
if [ -f ".env.docker" ]; then
    echo ">> Using Docker environment configuration..."
    cp .env.docker .env
fi

# Wait for MySQL to be ready
echo ">> Waiting for MySQL..."
while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" --silent 2>/dev/null; do
    sleep 2
done
echo ">> MySQL is ready!"

# Install/update dependencies if vendor is empty (mounted volume)
if [ ! -f "vendor/autoload.php" ]; then
    echo ">> Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if not exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo ">> Generating app key..."
    php artisan key:generate --force
fi

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force

# Run seeders (categories and cities)
echo ">> Running seeders..."
php artisan db:seed --force 2>/dev/null || true

# Clear and cache
echo ">> Optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Create storage link
php artisan storage:link --force 2>/dev/null || true

echo "========================================="
echo "  Expo API Ready!"
echo "  URL: http://localhost:8002"
echo "========================================="

# Execute the main command
exec "$@"
