#!/bin/bash
# ═══════════════════════════════════════════════════
# MAHAM EXPO — VPS DEPLOYMENT SCRIPT
# Run this on your VPS: bash DEPLOY_NOW.sh
# ═══════════════════════════════════════════════════

set -e
echo "🚀 Starting Maham Expo deployment..."

# 1. Install Docker if not present
if ! command -v docker &> /dev/null; then
    echo "Installing Docker..."
    curl -fsSL https://get.docker.com | sh
    systemctl enable docker
    systemctl start docker
fi

# 2. Install Docker Compose
if ! command -v docker-compose &> /dev/null; then
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# 3. Clone Laravel backend
cd /opt
rm -rf maham-expo-laravel
git clone https://github.com/mahammedia90-commits/maham-expo-laravel.git
cd maham-expo-laravel

# 4. Create production .env
cp .env.production .env

# 5. Start all services
docker-compose up -d --build

# 6. Wait for services
echo "Waiting for services to start..."
sleep 30

# 7. Run migrations
docker exec expo-api php artisan migrate --force
docker exec auth-service php artisan migrate --force

# 8. Seed data
docker exec expo-api php artisan db:seed --force

# 9. Install SSL
apt-get update && apt-get install -y certbot
certbot certonly --standalone -d mahamexpo.sa -d admin.mahamexpo.sa -d merchant.mahamexpo.sa -d investor.mahamexpo.sa -d sponsor.mahamexpo.sa -d api.mahamexpo.sa -d auth-service-api.mahamexpo.sa -d expo-service-api.mahamexpo.sa --agree-tos --non-interactive --email admin@mahamexpo.sa

# 10. Restart nginx with SSL
docker-compose restart nginx

echo ""
echo "═══════════════════════════════════════"
echo "  ✅ DEPLOYMENT COMPLETE!"
echo "═══════════════════════════════════════"
echo ""
echo "  🌐 mahamexpo.sa"
echo "  🏢 admin.mahamexpo.sa"
echo "  🏪 merchant.mahamexpo.sa"
echo "  📈 investor.mahamexpo.sa"
echo "  🤝 sponsor.mahamexpo.sa"
echo "  🔌 api.mahamexpo.sa"
echo ""
