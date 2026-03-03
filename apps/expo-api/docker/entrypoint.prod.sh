#!/bin/bash
set -e

echo "========================================="
echo "  Expo API - Production Starting..."
echo "========================================="

cd /var/www/html

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

# Wait for MySQL (check port AND credentials)
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

# Verify MySQL credentials actually work
echo ">> Verifying MySQL credentials..."
mysql_auth_retries=10
mysql_auth_count=0
while [ $mysql_auth_count -lt $mysql_auth_retries ]; do
    if php -r "try { new PDO('mysql:host=${DB_HOST:-expo-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-expo_service}', '${DB_USERNAME:-expo_user}', '${DB_PASSWORD:-password123}'); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); exit(1); }" 2>/dev/null; then
        echo ">> MySQL credentials verified!"
        break
    else
        mysql_auth_count=$((mysql_auth_count + 1))
        if [ $mysql_auth_count -ge $mysql_auth_retries ]; then
            echo ">> !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
            echo ">> CRITICAL: MySQL Access Denied!"
            echo ">> DB_HOST=${DB_HOST:-expo-mysql}"
            echo ">> DB_DATABASE=${DB_DATABASE:-expo_service}"
            echo ">> DB_USERNAME=${DB_USERNAME:-expo_user}"
            echo ">> DB_PASSWORD is set: $([ -n \"${DB_PASSWORD}\" ] && echo 'YES' || echo 'NO')"
            echo ">> FIX: Check DB_PASSWORD in Coolify matches MYSQL_PASSWORD"
            echo ">> !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
        fi
        echo ">> MySQL auth retry... ($mysql_auth_count/$mysql_auth_retries)"
        sleep 3
    fi
done
echo ">> MySQL check completed!"

# Check Redis connectivity (non-blocking)
if [ "${CACHE_STORE:-database}" = "redis" ] || [ "${QUEUE_CONNECTION:-database}" = "redis" ] || [ "${SESSION_DRIVER:-file}" = "redis" ]; then
    echo ">> Checking Redis at ${REDIS_HOST:-expo-redis}:${REDIS_PORT:-6379}..."
    redis_retries=15
    redis_count=0
    redis_ready=false
    while [ "$redis_ready" = "false" ]; do
        if php -r "try { \$r = new Redis(); \$r->connect('${REDIS_HOST:-expo-redis}', ${REDIS_PORT:-6379}, 2); if('${REDIS_PASSWORD:-}') \$r->auth('${REDIS_PASSWORD:-}'); \$r->ping(); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
            redis_ready=true
            echo ">> Redis is ready!"
        else
            redis_count=$((redis_count + 1))
            if [ $redis_count -ge $redis_retries ]; then
                echo ">> WARNING: Redis not available after ${redis_retries} attempts"
                echo ">> Falling back: CACHE_STORE=database, QUEUE_CONNECTION=database, SESSION_DRIVER=file"
                export CACHE_STORE=database
                export QUEUE_CONNECTION=database
                export SESSION_DRIVER=file
                # Update .env if it exists
                if [ -f .env ]; then
                    sed -i 's/^CACHE_STORE=.*/CACHE_STORE="database"/' .env 2>/dev/null || true
                    sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION="database"/' .env 2>/dev/null || true
                    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER="file"/' .env 2>/dev/null || true
                fi
                break
            fi
            echo ">> Waiting for Redis... ($redis_count/$redis_retries)"
            sleep 2
        fi
    done
fi

# Wait for Auth Service to be ready (non-blocking - don't prevent startup)
if [ -n "$AUTH_SERVICE_URL" ]; then
    echo ">> Checking Auth Service at ${AUTH_SERVICE_URL}..."
    auth_retries=10
    auth_count=0
    while ! curl -sf --max-time 3 "${AUTH_SERVICE_URL}/api/health" > /dev/null 2>&1; do
        auth_count=$((auth_count + 1))
        if [ $auth_count -ge $auth_retries ]; then
            echo ">> WARNING: Auth Service not ready yet - will retry at runtime"
            echo ">> Expo API will start anyway (auth requests will retry)"
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

# Seed only if needed (check if categories table has data)
CATEGORY_COUNT=$(php artisan tinker --execute="echo \App\Models\Category::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$CATEGORY_COUNT" = "0" ] || [ -z "$CATEGORY_COUNT" ]; then
    echo ">> Running seeders (first time)..."
    php artisan db:seed --force --no-interaction 2>&1 || echo ">> Seeding warning"
else
    echo ">> Seeders skipped (data already exists: $CATEGORY_COUNT categories)"
fi

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
