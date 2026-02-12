# 🚂 نشر Maham Expo على Railway

## 📋 المتطلبات
- حساب [Railway](https://railway.app)
- حساب GitHub مع المشروع مرفوع
- Railway CLI (اختياري): `npm i -g @railway/cli`

---

## 🏗️ هيكل المشروع على Railway

```
Railway Project: maham-expo
├── auth-service        (Laravel - Auth API)
│   ├── MySQL Plugin    (قاعدة بيانات)
│   └── Redis Plugin    (كاش + طوابير)
├── expo-api            (Laravel - Expo API)
│   ├── MySQL Plugin    (قاعدة بيانات)
│   └── Redis Plugin    (كاش + طوابير)
```

---

## 📝 الخطوات

### الخطوة 1: إنشاء المشروع على Railway

1. اذهب إلى [railway.app/new](https://railway.app/new)
2. اضغط **"New Project"** → **"Empty Project"**
3. سمّي المشروع: `maham-expo`

---

### الخطوة 2: ربط GitHub

1. اذهب إلى Settings → GitHub
2. اربط حساب GitHub
3. أعطِ Railway صلاحية الوصول للريبو

---

### الخطوة 3: إنشاء خدمة Auth Service

#### 3.1 إضافة MySQL Plugin
1. في المشروع اضغط **"+ New"** → **"Database"** → **"Add MySQL"**
2. سمّيه: `auth-mysql`
3. Railway يعطيك تلقائياً:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

#### 3.2 إضافة Redis Plugin
1. اضغط **"+ New"** → **"Database"** → **"Add Redis"**
2. سمّيه: `auth-redis`
3. Railway يعطيك تلقائياً:
   - `REDISHOST`
   - `REDISPORT`
   - `REDISPASSWORD`

#### 3.3 إنشاء خدمة Auth
1. اضغط **"+ New"** → **"GitHub Repo"**
2. اختر الريبو: `maham-expo`
3. **مهم** - في الإعدادات:
   - **Root Directory:** `apps/maham-auth-expo-api`
   - **Builder:** `Dockerfile`
   - **Dockerfile Path:** `Dockerfile.railway`

#### 3.4 إضافة المتغيرات (Environment Variables)
اذهب إلى **Variables** وأضف:

```env
# App
APP_NAME=Maham Auth API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

# Keys (ستُولّد تلقائياً عند أول تشغيل)
APP_KEY=
JWT_SECRET=

# Database - ربط مع MySQL Plugin
DB_CONNECTION=mysql
DB_HOST=${{auth-mysql.MYSQLHOST}}
DB_PORT=${{auth-mysql.MYSQLPORT}}
DB_DATABASE=${{auth-mysql.MYSQLDATABASE}}
DB_USERNAME=${{auth-mysql.MYSQLUSER}}
DB_PASSWORD=${{auth-mysql.MYSQLPASSWORD}}

# Redis - ربط مع Redis Plugin
REDIS_CLIENT=phpredis
REDIS_HOST=${{auth-redis.REDISHOST}}
REDIS_PORT=${{auth-redis.REDISPORT}}
REDIS_PASSWORD=${{auth-redis.REDISPASSWORD}}

# Cache & Queue
CACHE_STORE=redis
CACHE_PREFIX=auth_
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Logging (Railway يعرض stdout/stderr في Logs)
LOG_CHANNEL=stderr
LOG_LEVEL=warning

# Service Token (ولّد توكن عشوائي آمن)
SERVICE_TOKEN=your-secure-random-token-here
TRUSTED_SERVICE_IPS=0.0.0.0/0

# Other
BCRYPT_ROUNDS=12
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@mahamexpo.com
MAIL_FROM_NAME=Maham Expo
```

#### 3.5 إعداد النطاق (Domain)
1. اذهب **Settings** → **Networking**
2. اضغط **"Generate Domain"** → ستحصل على: `auth-service-xxxx.up.railway.app`
3. أو أضف نطاقك: **"Custom Domain"** → `auth-api.yourdomain.com`
   - أضف CNAME record في DNS يشير إلى Railway domain

---

### الخطوة 4: إنشاء خدمة Expo API

#### 4.1 إضافة MySQL Plugin
1. اضغط **"+ New"** → **"Database"** → **"Add MySQL"**
2. سمّيه: `expo-mysql`

#### 4.2 إضافة Redis Plugin
1. اضغط **"+ New"** → **"Database"** → **"Add Redis"**
2. سمّيه: `expo-redis`

#### 4.3 إنشاء خدمة Expo
1. اضغط **"+ New"** → **"GitHub Repo"**
2. اختر نفس الريبو: `maham-expo`
3. **مهم** - في الإعدادات:
   - **Root Directory:** `apps/expo-api`
   - **Builder:** `Dockerfile`
   - **Dockerfile Path:** `Dockerfile.railway`

#### 4.4 إضافة المتغيرات (Environment Variables)

```env
# App
APP_NAME=Maham Expo API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

# Key (يُولّد تلقائياً)
APP_KEY=

# Database - ربط مع MySQL Plugin
DB_CONNECTION=mysql
DB_HOST=${{expo-mysql.MYSQLHOST}}
DB_PORT=${{expo-mysql.MYSQLPORT}}
DB_DATABASE=${{expo-mysql.MYSQLDATABASE}}
DB_USERNAME=${{expo-mysql.MYSQLUSER}}
DB_PASSWORD=${{expo-mysql.MYSQLPASSWORD}}

# Redis - ربط مع Redis Plugin
REDIS_CLIENT=phpredis
REDIS_HOST=${{expo-redis.REDISHOST}}
REDIS_PORT=${{expo-redis.REDISPORT}}
REDIS_PASSWORD=${{expo-redis.REDISPASSWORD}}

# Cache & Queue
CACHE_STORE=redis
CACHE_PREFIX=expo_
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=warning

# Auth Service - استخدم Railway Internal Networking
AUTH_SERVICE_URL=http://${{auth-service.RAILWAY_PRIVATE_DOMAIN}}:8080
AUTH_SERVICE_TOKEN=same-token-as-SERVICE_TOKEN-above
AUTH_SERVICE_TIMEOUT=10
AUTH_SERVICE_CACHE_TTL=300

# Service Info
SERVICE_NAME=expo-api
SERVICE_VERSION=1.0.0

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60

# Storage
FILESYSTEM_DISK=public

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@mahamexpo.com
MAIL_FROM_NAME=Maham Expo
```

#### 4.5 إعداد النطاق
1. **Settings** → **Networking** → **"Generate Domain"**
2. ستحصل على: `expo-api-xxxx.up.railway.app`
3. أو: `api.yourdomain.com` (Custom Domain)

---

### الخطوة 5: النشر

1. بعد إضافة كل المتغيرات، اضغط **"Deploy"** في كل خدمة
2. أو اعمل `git push` وRailway ينشر تلقائياً
3. تابع الـ Logs في Dashboard لكل خدمة

---

### الخطوة 6: التحقق

```bash
# فحص Auth Service
curl https://your-auth-service.up.railway.app/api/health

# فحص Expo API
curl https://your-expo-api.up.railway.app/api/health

# تسجيل دخول أدمن
curl -X POST https://your-auth-service.up.railway.app/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"identifier":"admin@maham-expo.com","password":"Admin@123456"}'
```

---

## 🔧 الأوامر المفيدة (Railway CLI)

```bash
# تثبيت CLI
npm i -g @railway/cli

# تسجيل دخول
railway login

# ربط المشروع
railway link

# عرض اللوقات
railway logs

# تشغيل أوامر على الخدمة
railway shell

# داخل الشل:
php artisan migrate:status
php artisan tinker
php artisan queue:restart
php artisan cache:clear

# فتح لوحة التحكم
railway open
```

---

## 🔄 التحديث التلقائي (Auto-Deploy)

Railway ينشر تلقائياً عند كل `git push` إلى الفرع المحدد (عادة `main`).

### تغيير فرع النشر:
1. **Settings** → **Source** → **Branch**
2. غيّر من `main` إلى أي فرع تريده

### إيقاف النشر التلقائي:
1. **Settings** → **Source** → أطفئ **"Auto Deploy"**

---

## 📊 المراقبة

### Logs:
- اضغط على أي خدمة → **"Logs"** tab
- الإخراج يظهر في real-time (stdout/stderr)

### Metrics:
- **Usage** tab يعرض CPU, Memory, Network
- Railway يفوتر حسب الاستخدام الفعلي

### Health Checks:
- مُعدّ تلقائياً على `/api/health`
- إذا فشل، Railway يعيد تشغيل الحاوية

---

## 💰 التكلفة (Railway Pricing)

### خطة Hobby ($5/شهر):
- $5 رصيد مجاني
- 8GB RAM per service
- 8 vCPU per service
- 100GB bandwidth

### تقدير لهذا المشروع:
| الخدمة | RAM | التكلفة التقريبية |
|--------|-----|-------------------|
| Auth Service | ~256MB | ~$3/شهر |
| Expo API | ~256MB | ~$3/شهر |
| MySQL x2 | ~256MB each | ~$5/شهر |
| Redis x2 | ~64MB each | ~$2/شهر |
| **المجموع** | | **~$13/شهر** |

---

## 🛡️ الأمان

### متغيرات حساسة:
- **APP_KEY**: يُولّد تلقائياً (لا تشاركه)
- **JWT_SECRET**: يُولّد تلقائياً (لا تشاركه)
- **SERVICE_TOKEN**: ولّده بـ: `openssl rand -hex 32`
- **DB_PASSWORD**: Railway يولّده تلقائياً

### النصائح:
1. لا تضع متغيرات حساسة في الكود
2. استخدم Railway Variables referencing: `${{service.VAR}}`
3. فعّل 2FA على حسابك
4. استخدم Custom Domain مع SSL (مجاني من Railway)

---

## 🔥 استكشاف الأخطاء

### الخطأ: "Build failed"
```bash
# تحقق من الـ Logs في Build tab
# تأكد أن Root Directory صحيح
# تأكد أن Dockerfile.railway موجود
```

### الخطأ: "Health check failed"
```bash
# تأكد أن /api/health يعمل
# تحقق من متغيرات قاعدة البيانات
# شاهد Logs لمعرفة السبب
```

### الخطأ: "Cannot connect to MySQL"
```bash
# تأكد من ربط المتغيرات بشكل صحيح:
# DB_HOST=${{auth-mysql.MYSQLHOST}}
# وليس DB_HOST=localhost
```

### الخطأ: "Auth service unreachable" (من Expo API)
```bash
# استخدم Internal Networking:
# AUTH_SERVICE_URL=http://${{auth-service.RAILWAY_PRIVATE_DOMAIN}}:8080
# لا تستخدم URL الخارجي
```

### إعادة النشر:
1. اذهب للخدمة → **Deployments** tab
2. اضغط **"Redeploy"** على آخر deployment
3. أو اعمل `git push` فارغ:
   ```bash
   git commit --allow-empty -m "trigger redeploy"
   git push
   ```

---

## 📁 ملفات Railway المُنشأة

```
apps/maham-auth-expo-api/
├── Dockerfile.railway          # Dockerfile خاص بـ Railway
├── railway.toml                # إعدادات Railway
├── .env.railway                # قالب المتغيرات
└── railway/
    ├── nginx.conf              # Nginx (PORT ديناميكي)
    ├── supervisord.conf        # Supervisor (logs → stdout)
    └── entrypoint.sh           # نقطة الدخول

apps/expo-api/
├── Dockerfile.railway          # Dockerfile خاص بـ Railway
├── railway.toml                # إعدادات Railway
├── .env.railway                # قالب المتغيرات
└── railway/
    ├── nginx.conf              # Nginx (PORT ديناميكي)
    ├── supervisord.conf        # Supervisor (logs → stdout)
    └── entrypoint.sh           # نقطة الدخول
```

### الفرق عن ملفات Coolify/VPS:
| الميزة | Coolify (VPS) | Railway |
|--------|---------------|---------|
| المنفذ | ثابت (80) | ديناميكي ($PORT) |
| اللوقات | ملفات | stdout/stderr |
| الشبكة | docker-compose | Railway Internal |
| SSL | Traefik/Caddy | تلقائي |
| قاعدة البيانات | Docker containers | Railway Plugins |
| التكلفة | VPS ثابت | حسب الاستخدام |
