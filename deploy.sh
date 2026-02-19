#!/bin/bash
###############################################
# Maham Expo - Deploy / Update
# Usage: bash deploy.sh
###############################################
set -e

APP_DIR="/opt/maham-expo"
cd "$APP_DIR"

echo "========================================="
echo "  Maham Expo - Deploying..."
echo "========================================="

# Pull latest code
echo ">> Pulling latest code..."
git pull origin main

# Rebuild and restart
echo ">> Building images..."
docker compose -f docker-compose.prod.yml build

echo ">> Restarting services..."
docker compose -f docker-compose.prod.yml up -d

echo ">> Cleaning old images..."
docker image prune -f

echo ""
echo "========================================="
echo "  Deploy Complete! ✅"
echo "========================================="
echo ""
echo "  View logs: docker compose -f docker-compose.prod.yml logs -f"
echo ""
