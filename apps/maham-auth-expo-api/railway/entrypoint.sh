#!/bin/bash
set -e

echo "========================================="
echo "  Auth Service - Railway Starting..."
echo "========================================="

# Railway provides PORT dynamically
RAILWAY_PORT="${PORT:-8080}"
echo ">> Railway PORT: $RAILWAY_PORT"

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
    php artisan key:generate --force
fi

# Generate JWT_SECRET if missing
if [ -z "$JWT_SECRET" ] || [ "$JWT_SECRET" = "" ]; then
    echo ">> Generating JWT_SECRET..."
    php artisan jwt:secret --force
fi

# Run migrations
echo ">> Running migrations..."
php artisan migrate --force 2>&1 || echo ">> Migration warning (may already be up to date)"

# Seed only if needed (check if roles table has data)
ROLE_COUNT=$(php artisan tinker --execute="echo \App\Models\Role::count();" 2>/dev/null || echo "0")
if [ "$ROLE_COUNT" = "0" ]; then
    echo ">> Running seeders (first time)..."
    php artisan db:seed --force 2>&1 || echo ">> Seeding warning"
fi

# Production cache optimization
echo ">> Caching configuration..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true
php artisan event:cache 2>&1 || true

# Storage link
php artisan storage:link --force 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "========================================="
echo "  Auth Service Ready! (Railway)"
echo "  Listening on port: $RAILWAY_PORT"
echo "========================================="

exec "$@"
