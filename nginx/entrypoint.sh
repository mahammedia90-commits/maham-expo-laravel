#!/bin/sh
set -e

echo "========================================="
echo "  Nginx Proxy - Starting..."
echo "========================================="

# Check if SSL certificates exist
AUTH_CERT="/etc/letsencrypt/live/auth-service-api.mahamexpo.sa/fullchain.pem"
EXPO_CERT="/etc/letsencrypt/live/expo-service-api.mahamexpo.sa/fullchain.pem"

if [ -f "$AUTH_CERT" ] && [ -f "$EXPO_CERT" ]; then
    echo ">> SSL certificates found - using HTTPS config"
    cp /etc/nginx/templates/proxy-ssl.conf /etc/nginx/conf.d/default.conf
else
    echo ">> SSL certificates NOT found - using HTTP-only config"
    echo ">> Run certbot to generate SSL certificates"
    cp /etc/nginx/templates/proxy-initial.conf /etc/nginx/conf.d/default.conf
fi

# Remove default nginx config if exists
rm -f /etc/nginx/conf.d/default.conf.bak

echo ">> Nginx proxy ready!"
echo "========================================="

exec "$@"
