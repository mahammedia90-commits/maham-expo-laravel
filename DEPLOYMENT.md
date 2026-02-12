# Maham Expo - Deployment Guide (Hostinger VPS + Coolify)

---

## المتطلبات

| Item | الحد الأدنى |
|------|-----------|
| VPS | Hostinger KVM 2 (4GB RAM, 2 vCPU, 100GB SSD) |
| OS | Ubuntu 22.04+ |
| Domain | نطاقين فرعيين (auth-api.yourdomain.com + api.yourdomain.com) |
| Git | GitHub / GitLab repo |

---

## الخطوة 1: إعداد Hostinger VPS

### 1.1 الدخول على السيرفر
```bash
ssh root@YOUR_VPS_IP
```

### 1.2 تحديث النظام
```bash
apt update && apt upgrade -y
```

### 1.3 تثبيت Coolify
```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

بعد التثبيت، افتح في المتصفح:
```
http://YOUR_VPS_IP:8000
```
- أنشئ حساب Admin
- اختر كلمة مرور قوية

---

## الخطوة 2: إعداد DNS

في لوحة تحكم Hostinger DNS أو مزود النطاق:

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | auth-api | YOUR_VPS_IP | 3600 |
| A | api | YOUR_VPS_IP | 3600 |

انتظر 5-10 دقائق حتى ينتشر DNS.

---

## الخطوة 3: رفع المشروع على GitHub

### 3.1 إنشاء Repository جديد على GitHub
```bash
# من مجلد المشروع
cd /Users/atif/Desktop/maham-expo

# تأكد إنك على الـ branch الصحيح
git add .
git commit -m "Production deployment setup"
git remote add origin https://github.com/YOUR_USERNAME/maham-expo.git
git push -u origin main
```

### 3.2 تأكد من وجود هذي الملفات:
```
maham-expo/
├── docker-compose.prod.yml
├── .env.production          (template فقط - لا ترفعه بالسيكرتس)
├── apps/
│   ├── expo-api/
│   │   ├── Dockerfile.prod
│   │   └── docker/
│   │       ├── nginx.conf
│   │       ├── supervisord.conf
│   │       └── entrypoint.prod.sh
│   └── maham-auth-expo-api/
│       ├── Dockerfile.prod
│       └── docker/
│           ├── nginx.conf
│           ├── supervisord.conf
│           └── entrypoint.prod.sh
```

---

## الخطوة 4: إعداد Coolify

### 4.1 ربط GitHub
1. Coolify Dashboard > **Sources** > **Add GitHub App**
2. اتبع الخطوات لربط حسابك
3. اختر الـ Repository: `maham-expo`

### 4.2 إنشاء المشروع
1. **Projects** > **New Project** > سمّه `Maham Expo`
2. **New Environment** > سمّه `production`

### 4.3 نشر الخدمة (Docker Compose)
1. داخل المشروع > **New Resource**
2. اختر **Docker Compose**
3. اختر GitHub repo: `maham-expo`
4. **Docker Compose Location**: `docker-compose.prod.yml`

### 4.4 إضافة Environment Variables
في Coolify، اذهب لإعدادات الـ Resource > **Environment Variables**:

```env
# الدومينات
AUTH_APP_URL=https://auth-api.yourdomain.com
EXPO_APP_URL=https://api.yourdomain.com

# Auth Database
AUTH_DB_USER=auth_user
AUTH_DB_PASSWORD=          # اضغط Generate
AUTH_MYSQL_ROOT_PASSWORD=  # اضغط Generate

# Expo Database
EXPO_DB_USER=expo_user
EXPO_DB_PASSWORD=          # اضغط Generate
EXPO_MYSQL_ROOT_PASSWORD=  # اضغط Generate

# Security (ولّد من الطرفية أو Generate في Coolify)
JWT_SECRET=                # openssl rand -hex 32
SERVICE_TOKEN=             # openssl rand -hex 32
TRUSTED_SERVICE_IPS=172.0.0.0/8
```

> **مهم:** استخدم زر **Generate** في Coolify أو ولّد بنفسك:
> ```bash
> openssl rand -hex 32
> ```

### 4.5 إعداد الدومينات و SSL
1. في Coolify، اذهب لكل service
2. **auth-service**: Domain = `auth-api.yourdomain.com`
3. **expo-api**: Domain = `api.yourdomain.com`
4. فعّل **Let's Encrypt SSL** لكل دومين (تلقائي)

### 4.6 إعداد Proxy في Coolify
1. Settings > **Proxy**
2. تأكد إن **Traefik** أو **Caddy** شغّال
3. تأكد من تفعيل **HTTPS redirect**

Port Mapping في Coolify:
- `auth-service`: Port `80` (Nginx داخل الكنتينر)
- `expo-api`: Port `80` (Nginx داخل الكنتينر)

---

## الخطوة 5: النشر (Deploy)

### 5.1 أول نشر
1. اضغط **Deploy** في Coolify
2. راقب الـ Logs حتى ترى:
   ```
   Auth Service Ready! (Production)
   Expo API Ready! (Production)
   ```

### 5.2 التحقق
```bash
# من جهازك أو السيرفر
curl https://auth-api.yourdomain.com/api/health
curl https://api.yourdomain.com/api/health
```

يجب تحصل:
```json
{"status":"ok","service":"Maham Auth Service","version":"1.0.0"}
{"status":"ok","service":"Maham Expo API","version":"1.0.0"}
```

---

## الخطوة 6: النشر التلقائي (Auto Deploy)

### 6.1 في Coolify
1. Resource Settings > **Webhooks**
2. فعّل **Auto Deploy on Push**
3. اختر branch: `main`

### 6.2 سير العمل
```
git push origin main  →  Coolify يكتشف التغيير  →  يبني و ينشر تلقائياً
```

---

## الأوامر المفيدة

### الدخول على الكنتينرات (من السيرفر)
```bash
# Auth Service
docker exec -it auth-service bash
docker exec -it auth-service php artisan tinker

# Expo API
docker exec -it expo-api bash
docker exec -it expo-api php artisan tinker
```

### إدارة قاعدة البيانات
```bash
# Auth DB
docker exec -it auth-mysql mysql -u auth_user -p auth_service

# Expo DB
docker exec -it expo-mysql mysql -u expo_user -p maham_expo_api
```

### إعادة تشغيل الـ Queue
```bash
docker exec -it auth-service php artisan queue:restart
docker exec -it expo-api php artisan queue:restart
```

### مسح الـ Cache
```bash
docker exec -it auth-service php artisan optimize:clear
docker exec -it expo-api php artisan optimize:clear
```

### عرض الـ Logs
```bash
# Docker logs
docker logs auth-service --tail 100 -f
docker logs expo-api --tail 100 -f

# Laravel logs
docker exec -it auth-service tail -f storage/logs/laravel.log
docker exec -it expo-api tail -f storage/logs/laravel.log
```

### Migrations
```bash
docker exec -it auth-service php artisan migrate --force
docker exec -it expo-api php artisan migrate --force
```

---

## Backup

### نسخ احتياطي لقواعد البيانات
```bash
# Auth DB
docker exec auth-mysql mysqldump -u root -p auth_service > auth_backup_$(date +%Y%m%d).sql

# Expo DB
docker exec expo-mysql mysqldump -u root -p maham_expo_api > expo_backup_$(date +%Y%m%d).sql
```

### نسخ احتياطي تلقائي (Cron Job على السيرفر)
```bash
crontab -e
# أضف هذا السطر (يومياً الساعة 3 صباحاً)
0 3 * * * docker exec auth-mysql mysqldump -u root -pYOUR_ROOT_PASSWORD auth_service | gzip > /root/backups/auth_$(date +\%Y\%m\%d).sql.gz
0 3 * * * docker exec expo-mysql mysqldump -u root -pYOUR_ROOT_PASSWORD maham_expo_api | gzip > /root/backups/expo_$(date +\%Y\%m\%d).sql.gz
```

---

## البنية النهائية

```
Internet
    │
    ├─ auth-api.yourdomain.com (HTTPS)
    │   └── Coolify Proxy (Traefik/Caddy)
    │       └── auth-service (Nginx+PHP-FPM+Queue+Scheduler)
    │           ├── auth-mysql
    │           └── auth-redis
    │
    └─ api.yourdomain.com (HTTPS)
        └── Coolify Proxy (Traefik/Caddy)
            └── expo-api (Nginx+PHP-FPM+Queue+Scheduler)
                ├── expo-mysql
                └── expo-redis

الشبكة الداخلية: maham_network
├── auth-service ↔ auth-mysql, auth-redis
├── expo-api ↔ expo-mysql, expo-redis
└── expo-api → auth-service (http://auth-service)
```

---

## مراجعة الأمان (Production Checklist)

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] كلمات مرور قوية لكل قواعد البيانات
- [ ] JWT_SECRET قوي (64 حرف)
- [ ] SERVICE_TOKEN قوي (64 حرف)
- [ ] SSL/HTTPS مفعّل على كل الدومينات
- [ ] LOG_LEVEL=warning (مو debug)
- [ ] لا توجد ports مكشوفة لقواعد البيانات
- [ ] Backup يومي مفعّل
- [ ] Firewall مفعّل (UFW)

### إعداد الجدار الناري
```bash
# على السيرفر
ufw allow 22/tcp     # SSH
ufw allow 80/tcp     # HTTP
ufw allow 443/tcp    # HTTPS
ufw allow 8000/tcp   # Coolify Dashboard
ufw enable
```
