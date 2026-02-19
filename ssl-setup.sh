#!/bin/bash
###############################################
# Maham Expo - SSL Certificate Setup
# Run after DNS is pointing to VPS
# Usage: sudo bash ssl-setup.sh
###############################################
set -e

APP_DIR="/opt/maham-expo"
cd "$APP_DIR"

SERVER_IP=$(curl -s ifconfig.me)

echo "========================================="
echo "  SSL Certificate Setup"
echo "========================================="
echo ""
echo "  Server IP: $SERVER_IP"
echo ""
echo "  Make sure these DNS records exist:"
echo "  auth-service-api.mahamexpo.sa → $SERVER_IP"
echo "  expo-service-api.mahamexpo.sa → $SERVER_IP"
echo ""

read -p "DNS records are ready? (y/n): " ready
if [ "$ready" != "y" ]; then
    echo "Setup DNS first, then run this script again."
    exit 0
fi

# Generate certificates
echo ">> Generating SSL certificates..."
docker compose -f docker-compose.prod.yml run --rm certbot certonly \
    --webroot -w /var/www/certbot \
    -d auth-service-api.mahamexpo.sa \
    -d expo-service-api.mahamexpo.sa \
    --email admin@mahamexpo.sa \
    --agree-tos --no-eff-email

# Switch to SSL nginx config
echo ">> Switching to SSL config..."
cat > nginx/proxy.conf <<'NGINXEOF'
# Certbot ACME + HTTP to HTTPS redirect
server {
    listen 80;
    server_name auth-service-api.mahamexpo.sa expo-service-api.mahamexpo.sa;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    location / {
        return 301 https://$host$request_uri;
    }
}

# Auth Service - HTTPS
server {
    listen 443 ssl;
    server_name auth-service-api.mahamexpo.sa;

    ssl_certificate /etc/letsencrypt/live/auth-service-api.mahamexpo.sa/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/auth-service-api.mahamexpo.sa/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    client_max_body_size 25M;

    location / {
        proxy_pass http://auth-service:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
    }
}

# Expo API - HTTPS
server {
    listen 443 ssl;
    server_name expo-service-api.mahamexpo.sa;

    ssl_certificate /etc/letsencrypt/live/expo-service-api.mahamexpo.sa/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/expo-service-api.mahamexpo.sa/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    client_max_body_size 25M;

    location / {
        proxy_pass http://expo-api:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
    }
}
NGINXEOF

# Restart nginx
echo ">> Restarting nginx with SSL..."
docker compose -f docker-compose.prod.yml restart nginx-proxy

echo ""
echo "========================================="
echo "  SSL Setup Complete! ✅"
echo "========================================="
echo ""
echo "  https://auth-service-api.mahamexpo.sa"
echo "  https://expo-service-api.mahamexpo.sa"
echo ""
