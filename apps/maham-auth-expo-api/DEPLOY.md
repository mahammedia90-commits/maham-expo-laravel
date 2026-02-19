# Deploying Maham Auth API on Hostinger VPS with Coolify

## Prerequisites

1. **Hostinger VPS** with at least:
   - 2 GB RAM
   - 2 vCPU
   - 40 GB SSD
   - Ubuntu 22.04 LTS

2. **Domain** pointed to your VPS IP

3. **Coolify** installed on your VPS

---

## Step 1: Install Coolify on Hostinger VPS

SSH into your VPS and run:

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

After installation, access Coolify at: `http://YOUR_VPS_IP:8000`

---

## Step 2: Create New Project in Coolify

1. Go to Coolify Dashboard
2. Click **"New Project"**
3. Name it: `Maham Auth API`

---

## Step 3: Add GitHub Repository

### Option A: Public Repository
1. Click **"New Resource"** → **"Docker Compose"**
2. Select **"GitHub (Public Repository)"**
3. Enter your repository URL
4. Set branch: `main`

### Option B: Private Repository
1. Go to **Settings** → **SSH Keys**
2. Add your SSH key to GitHub
3. Click **"New Resource"** → **"Docker Compose"**
4. Select **"GitHub (Private Repository)"**
5. Enter your repository URL

---

## Step 4: Configure Docker Compose

1. Set **Docker Compose Location**: `docker-compose.prod.yml`
2. Set **Build Context**: `.` (root directory)

---

## Step 5: Set Environment Variables

In Coolify, go to **Environment Variables** and add:

### Required Variables:

```env
# App
APP_NAME=Maham Auth API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://auth.yourdomain.com
APP_TIMEZONE=Asia/Riyadh

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=auth_service
DB_USERNAME=auth_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=YOUR_ROOT_PASSWORD_HERE

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# JWT (will be auto-generated)
JWT_TTL=60

# Mail (use your Hostinger email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Maham Auth

# Service Token (generate with: openssl rand -hex 32)
SERVICE_TOKEN=YOUR_SERVICE_TOKEN

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Generate Secure Passwords:

```bash
# Generate DB Password
openssl rand -base64 24

# Generate Service Token
openssl rand -hex 32
```

---

## Step 6: Configure Domain & SSL

1. In Coolify, go to **"Domains"**
2. Add your domain: `auth.yourdomain.com`
3. Enable **"Let's Encrypt"** for free SSL
4. Set **Port**: `80` (nginx inside container)

---

## Step 7: Deploy

1. Click **"Deploy"**
2. Wait for build to complete (first build takes 3-5 minutes)
3. Check logs for any errors

---

## Step 8: Verify Deployment

After deployment, verify:

```bash
# Health Check
curl https://auth.yourdomain.com/api/health

# Expected response:
{
  "status": "ok",
  "service": "auth-service",
  "version": "1.0.0",
  "timestamp": "2024-..."
}
```

---

## Troubleshooting

### View Logs
In Coolify Dashboard → Your Service → **Logs**

Or via SSH:
```bash
docker logs maham-auth-api -f
```

### Database Connection Issues
```bash
# Check if MySQL is running
docker ps | grep mysql

# Connect to MySQL
docker exec -it maham-auth-mysql mysql -u root -p
```

### Permission Issues
```bash
# Fix storage permissions
docker exec -it maham-auth-api chown -R www-data:www-data /var/www/html/storage
```

### Rebuild
In Coolify: **Redeploy** → **Force Rebuild**

---

## Useful Commands

```bash
# Run migrations manually
docker exec -it maham-auth-api php artisan migrate --force

# Clear cache
docker exec -it maham-auth-api php artisan cache:clear
docker exec -it maham-auth-api php artisan config:clear

# Generate new APP_KEY
docker exec -it maham-auth-api php artisan key:generate

# Generate JWT secret
docker exec -it maham-auth-api php artisan jwt:secret

# Run seeders
docker exec -it maham-auth-api php artisan db:seed --force

# View queue status
docker exec -it maham-auth-api php artisan queue:monitor
```

---

## Backup Strategy

### Database Backup
```bash
# Create backup
docker exec maham-auth-mysql mysqldump -u root -p auth_service > backup_$(date +%Y%m%d).sql

# Restore backup
docker exec -i maham-auth-mysql mysql -u root -p auth_service < backup.sql
```

### Automated Backups (Coolify)
1. Go to **Settings** → **Backups**
2. Enable **S3 Backups** with your storage provider
3. Set schedule (daily recommended)

---

## Performance Optimization

### For High Traffic
Edit `docker-compose.prod.yml`:

```yaml
services:
  auth-api:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G
```

### Redis Memory
```yaml
redis:
  command: redis-server --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru
```

---

## Security Checklist

- [ ] Strong passwords (min 16 characters)
- [ ] APP_DEBUG=false
- [ ] SSL enabled (Let's Encrypt)
- [ ] Firewall configured (only ports 80, 443, 22)
- [ ] Regular backups enabled
- [ ] LOG_LEVEL=error (not debug)
- [ ] SERVICE_TOKEN generated securely

---

## Support

If you encounter issues:
1. Check Coolify logs
2. Check container logs: `docker logs maham-auth-api`
3. Check MySQL logs: `docker logs maham-auth-mysql`
