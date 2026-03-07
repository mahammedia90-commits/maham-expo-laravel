#!/bin/sh
set -e

echo "========================================="
echo "  Auth Service - Production Starting..."
echo "========================================="

cd /var/www/html

# Create .env file from environment variables if not exists
if [ ! -f .env ]; then
    echo ">> Creating .env file from environment variables..."
    env | grep -E '^(APP_|DB_|REDIS_|QUEUE_|CACHE_|LOG_|RATE_LIMIT_|MAIL_|SESSION_|JWT_|SERVICE_|TRUSTED_|BCRYPT_|FILESYSTEM_)' | while IFS='=' read -r key value; do
        echo "${key}=\"${value}\""
    done > .env 2>/dev/null || true
    grep -q "^APP_KEY=" .env 2>/dev/null || echo 'APP_KEY=""' >> .env
fi

# Ensure storage directories exist
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

# Set permissions early
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for MySQL (check port AND credentials)
echo ">> Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
max_retries=30
count=0
while ! nc -z "${DB_HOST:-mysql}" "${DB_PORT:-3306}" 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_retries ]; then
        echo ">> WARNING: MySQL port not available after ${max_retries} attempts"
        echo ">> Continuing anyway..."
        break
    fi
    echo ">> Waiting for MySQL port... ($count/$max_retries)"
    sleep 2
done

# Now verify MySQL credentials actually work
echo ">> Verifying MySQL credentials..."
mysql_auth_retries=10
mysql_auth_count=0
while [ $mysql_auth_count -lt $mysql_auth_retries ]; do
    if php -r "try { new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-auth_service}', '${DB_USERNAME:-auth_user}', '${DB_PASSWORD:-password123}'); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); exit(1); }" 2>/dev/null; then
        echo ">> MySQL credentials verified!"
        break
    else
        mysql_auth_count=$((mysql_auth_count + 1))
        if [ $mysql_auth_count -ge $mysql_auth_retries ]; then
            echo ">> !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
            echo ">> CRITICAL: MySQL Access Denied!"
            echo ">> DB_HOST=${DB_HOST:-mysql}"
            echo ">> DB_DATABASE=${DB_DATABASE:-auth_service}"
            echo ">> DB_USERNAME=${DB_USERNAME:-auth_user}"
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
    echo ">> Checking Redis at ${REDIS_HOST:-redis}:${REDIS_PORT:-6379}..."
    redis_retries=15
    redis_count=0
    redis_ready=false
    while [ "$redis_ready" = "false" ]; do
        if php -r "try { \$r = new Redis(); \$r->connect('${REDIS_HOST:-redis}', ${REDIS_PORT:-6379}, 2); if('${REDIS_PASSWORD:-}') \$r->auth('${REDIS_PASSWORD:-}'); \$r->ping(); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
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

# Clear ALL cache
echo ">> Clearing all cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan event:clear 2>/dev/null || true

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

# Seed only if needed (lightweight check — no artisan tinker overhead)
echo ">> Checking if seeding is needed..."
ROLE_COUNT=$(php -r "try { \$p = new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-auth_service}', '${DB_USERNAME:-auth_user}', '${DB_PASSWORD:-password123}'); echo \$p->query('SELECT COUNT(*) FROM roles')->fetchColumn(); } catch(Exception \$e) { echo '0'; }" 2>/dev/null || echo "0")
if [ "$ROLE_COUNT" = "0" ] || [ -z "$ROLE_COUNT" ]; then
    echo ">> Running seeders..."
    php artisan db:seed --force --no-interaction 2>/dev/null || echo ">> Seeding skipped"
fi

# DO NOT cache config - we rely on environment variables at runtime
# Only cache routes for performance
echo ">> Caching routes and views..."
php artisan route:cache --no-interaction 2>&1 || echo ">> Route cache warning"
php artisan view:cache --no-interaction 2>&1 || echo ">> View cache warning"
echo ">> Config cache SKIPPED (using env vars at runtime)"

# Storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Final permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "========================================="
echo "  Auth Service Ready! (Production)"
echo "========================================="
echo ">> APP_DEBUG: ${APP_DEBUG:-false}"
echo ">> APP_ENV: ${APP_ENV:-production}"
echo ">> DB_HOST: ${DB_HOST:-mysql}"
echo "========================================="

exec "$@"
