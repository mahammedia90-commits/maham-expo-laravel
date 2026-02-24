# #!/bin/bash
# set -e

# echo "========================================="
# echo "  Expo API - Starting..."
# echo "========================================="

# # Copy Docker env if .env.docker exists (volume mount overrides .env)
# if [ -f ".env.docker" ]; then
#     echo ">> Using Docker environment configuration..."
#     cp .env.production.example .env
# fi

# # Wait for MySQL to be ready
# echo ">> Waiting for MySQL..."
# while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" --silent 2>/dev/null; do
#     sleep 2
# done
# echo ">> MySQL is ready!"

# # Install/update dependencies if vendor is empty (mounted volume)
# if [ ! -f "vendor/autoload.php" ]; then
#     echo ">> Installing Composer dependencies..."
#     composer install --no-interaction --prefer-dist --optimize-autoloader
# fi

# # Generate app key if not exists
# if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
#     echo ">> Generating app key..."
#     php artisan key:generate --force
# fi

# # Run migrations
# echo ">> Running migrations..."
# php artisan migrate --force

# # Run seeders (categories and cities)
# echo ">> Running seeders..."
# php artisan db:seed --force 2>/dev/null || true

# # Clear and cache
# echo ">> Optimizing..."
# php artisan config:clear
# php artisan route:clear
# php artisan view:clear

# # Set permissions
# chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# # Create storage link
# php artisan storage:link --force 2>/dev/null || true

# echo "========================================="
# echo "  Expo API Ready!"
# echo "  URL: http://localhost:8002"
# echo "========================================="

# # Execute the main command
# exec "$@"



#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Railway Starting..."
echo "========================================="

# Build .env from Railway environment variables
echo ">> Creating .env file from environment variables..."
cat > .env << EOF
APP_NAME=ExpoAPI
APP_ENV=production
APP_DEBUG=false
APP_URL=${EXPO_APP_URL}

DB_CONNECTION=mysql
DB_HOST=${EXPO_DB_HOST}
DB_PORT=${EXPO_DB_PORT}
DB_DATABASE=${EXPO_DB_DATABASE}
DB_USERNAME=${EXPO_DB_USER}
DB_PASSWORD=${EXPO_DB_PASSWORD}

REDIS_HOST=${REDIS_HOST}
REDIS_PASSWORD=${REDIS_PASSWORD}
REDIS_PORT=${REDIS_PORT:-6379}

JWT_SECRET=${JWT_SECRET}
SERVICE_TOKEN=${SERVICE_TOKEN}
TRUSTED_SERVICE_IPS=${TRUSTED_SERVICE_IPS}
AUTH_APP_URL=${AUTH_APP_URL}
EOF

# Generate APP_KEY
echo ">> Generating APP_KEY..."
php artisan key:generate --force

# Wait for MySQL
echo ">> Waiting for MySQL..."
while ! mysqladmin ping -h"${EXPO_DB_HOST}" -P"${EXPO_DB_PORT}" --silent 2>/dev/null; do
    sleep 2
done
echo ">> MySQL is ready!"

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force

# Run seeders
echo ">> Running seeders..."
php artisan db:seed --force 2>/dev/null || true

# Cache config
echo ">> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Permissions & storage
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true

# Set nginx port
export PORT=${PORT:-8080}
envsubst '$PORT' < /etc/nginx/nginx-template.conf > /etc/nginx/nginx.conf

echo "========================================="
echo "  Expo API Ready! (Railway)"
echo "  Listening on port: ${PORT}"
echo "========================================="

exec "$@"