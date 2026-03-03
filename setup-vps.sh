#!/bin/bash
###############################################
# Maham Expo - VPS First-Time Setup
# Run on: Hostinger VPS (Ubuntu 24.04)
# Usage: sudo bash setup-vps.sh
###############################################
set -e

echo "========================================="
echo "  Maham Expo - VPS Setup Starting..."
echo "========================================="

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root: sudo bash setup-vps.sh${NC}"
    exit 1
fi

# ===== Step 1: Update system =====
echo -e "${GREEN}>> Step 1: Updating system...${NC}"
apt update && apt upgrade -y

# ===== Step 2: Install Docker =====
echo -e "${GREEN}>> Step 2: Installing Docker...${NC}"
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com | sh
    systemctl enable docker
    systemctl start docker
    echo -e "${GREEN}>> Docker installed!${NC}"
else
    echo -e "${YELLOW}>> Docker already installed${NC}"
fi

# ===== Step 3: Install Docker Compose =====
echo -e "${GREEN}>> Step 3: Checking Docker Compose...${NC}"
docker compose version || {
    echo -e "${RED}>> Docker Compose not found, installing...${NC}"
    apt install -y docker-compose-plugin
}

# ===== Step 4: Install Git =====
echo -e "${GREEN}>> Step 4: Installing Git...${NC}"
apt install -y git

# ===== Step 5: Setup Firewall =====
echo -e "${GREEN}>> Step 5: Setting up firewall...${NC}"
apt install -y ufw
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw --force enable
echo -e "${GREEN}>> Firewall configured${NC}"

# ===== Step 6: Clone repository =====
APP_DIR="/opt/maham-expo"
echo -e "${GREEN}>> Step 6: Setting up project...${NC}"

if [ -d "$APP_DIR" ]; then
    echo -e "${YELLOW}>> Project directory exists, pulling latest...${NC}"
    cd "$APP_DIR"
    git pull origin main
else
    echo -e "${GREEN}>> Cloning repository...${NC}"
    git clone https://github.com/atif-dev10/maham-services-.git "$APP_DIR"
    cd "$APP_DIR"
fi

# ===== Step 7: Create .env file =====
ENV_FILE="$APP_DIR/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo -e "${GREEN}>> Step 7: Creating .env file...${NC}"

    # Generate secure passwords
    AUTH_DB_PASS=$(openssl rand -hex 16)
    AUTH_ROOT_PASS=$(openssl rand -hex 16)
    EXPO_DB_PASS=$(openssl rand -hex 16)
    EXPO_ROOT_PASS=$(openssl rand -hex 16)
    JWT=$(openssl rand -hex 32)
    cat > "$ENV_FILE" <<EOF
# Auto-generated on $(date)
AUTH_APP_URL=https://auth-service-api.mahamexpo.sa
EXPO_APP_URL=https://expo-service-api.mahamexpo.sa
AUTH_APP_KEY=
EXPO_APP_KEY=
AUTH_DB_USER=auth_user
AUTH_DB_PASSWORD=$AUTH_DB_PASS
AUTH_MYSQL_ROOT_PASSWORD=$AUTH_ROOT_PASS
EXPO_DB_USER=expo_user
EXPO_DB_PASSWORD=$EXPO_DB_PASS
EXPO_MYSQL_ROOT_PASSWORD=$EXPO_ROOT_PASS
REDIS_PASSWORD=
JWT_SECRET=$JWT
TRUSTED_SERVICE_IPS=172.0.0.0/8,10.0.0.0/8,192.168.0.0/16
EOF

    echo -e "${GREEN}>> .env created with auto-generated passwords${NC}"
    echo -e "${YELLOW}>> SAVE THESE PASSWORDS:${NC}"
    echo "   AUTH_DB_PASSWORD=$AUTH_DB_PASS"
    echo "   EXPO_DB_PASSWORD=$EXPO_DB_PASS"
    echo "   JWT_SECRET=$JWT"
else
    echo -e "${YELLOW}>> .env already exists, skipping...${NC}"
fi

# ===== Step 8: Start with HTTP first (for SSL cert generation) =====
echo -e "${GREEN}>> Step 8: Starting services (HTTP mode first)...${NC}"
cp nginx/proxy-initial.conf nginx/proxy.conf.bak
cp nginx/proxy-initial.conf nginx/proxy.conf

cd "$APP_DIR"
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d

echo -e "${GREEN}>> Waiting 30 seconds for services to start...${NC}"
sleep 30

# ===== Step 9: Generate SSL Certificates =====
echo -e "${GREEN}>> Step 9: Generating SSL certificates...${NC}"
echo -e "${YELLOW}>> Make sure DNS is pointing to this server!${NC}"
echo "   auth-service-api.mahamexpo.sa → $(curl -s ifconfig.me)"
echo "   expo-service-api.mahamexpo.sa → $(curl -s ifconfig.me)"

read -p "Are DNS records pointing to this server? (y/n): " dns_ready
if [ "$dns_ready" = "y" ]; then
    docker compose -f docker-compose.prod.yml run --rm certbot certonly \
        --webroot -w /var/www/certbot \
        -d auth-service-api.mahamexpo.sa \
        -d expo-service-api.mahamexpo.sa \
        --email admin@mahamexpo.sa \
        --agree-tos --no-eff-email

    # Switch to SSL config
    cp nginx/proxy.conf.ssl nginx/proxy.conf 2>/dev/null || cp nginx/proxy.conf.bak.ssl nginx/proxy.conf 2>/dev/null || {
        echo -e "${YELLOW}>> Copy the SSL proxy.conf manually${NC}"
    }

    # Restart nginx with SSL
    docker compose -f docker-compose.prod.yml restart nginx-proxy
    echo -e "${GREEN}>> SSL certificates generated and activated!${NC}"
else
    echo -e "${YELLOW}>> Skipping SSL. Run this later:${NC}"
    echo "   cd $APP_DIR && bash ssl-setup.sh"
fi

echo ""
echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}  Maham Expo - VPS Setup Complete! ✅${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo "  Auth API: https://auth-service-api.mahamexpo.sa"
echo "  Expo API: https://expo-service-api.mahamexpo.sa"
echo ""
echo "  Useful commands:"
echo "  - View logs:    cd $APP_DIR && docker compose -f docker-compose.prod.yml logs -f"
echo "  - Restart:      cd $APP_DIR && docker compose -f docker-compose.prod.yml restart"
echo "  - Update:       cd $APP_DIR && bash deploy.sh"
echo ""
