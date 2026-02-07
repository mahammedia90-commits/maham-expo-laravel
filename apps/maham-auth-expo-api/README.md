<div align="center">

# Maham Auth Service

**Centralized Authentication & Authorization Microservice**

Built with Laravel 12 + JWT

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white)](https://jwt.io)

[English](#english) | [العربية](#arabic)

</div>

---

<a name="english"></a>

## Overview

Maham Auth Service is a centralized authentication and authorization microservice that provides JWT-based authentication, Role-Based Access Control (RBAC), and service-to-service communication for your microservices architecture.

### Key Features

- **JWT Authentication** - Secure token-based auth with refresh & blacklist support
- **RBAC** - Roles and permissions with hierarchical permission system
- **Multi-language** - Full Arabic & English support via `Accept-Language` header
- **Service-to-Service** - Secure inter-service communication via `X-Service-Token`
- **Audit Logging** - Track login, logout, and permission changes
- **Caching** - Built-in caching for permissions, roles, and services

---

## Quick Start

### 1. Installation

```bash
git clone <repo-url> maham-auth-service
cd maham-auth-service
composer install
cp .env.example .env
```

### 2. Configuration

```env
# App
APP_URL=http://localhost:8000
APP_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maham_auth
DB_USERNAME=root
DB_PASSWORD=secret

# JWT
JWT_SECRET=     # Run: php artisan jwt:secret
JWT_TTL=60      # Token lifetime in minutes
```

### 3. Setup

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

---

## Multi-Language Support

All API responses support Arabic and English. Send the `Accept-Language` header with your requests:

| Header | Language |
|--------|----------|
| `Accept-Language: ar` | Arabic |
| `Accept-Language: en` | English |
| *(not sent)* | Default (English) |

**Example:**

```bash
# Arabic response
curl -H "Accept-Language: ar" http://localhost:8000/api/auth/me

# English response
curl -H "Accept-Language: en" http://localhost:8000/api/auth/me
```

---

## API Reference

### Base URL

```
http://localhost:8000/api
```

### Common Headers

| Header | Value | Required |
|--------|-------|----------|
| `Content-Type` | `application/json` | Always |
| `Accept` | `application/json` | Always |
| `Accept-Language` | `ar` or `en` | Optional |
| `Authorization` | `Bearer {token}` | Protected routes |
| `X-Service-Token` | `{service_token}` | Service routes |

---

### Health Check

```
GET /health
```

**Response:**

```json
{
  "status": "ok",
  "service": "auth-service",
  "version": "1.0.0",
  "timestamp": "2025-01-01T00:00:00.000000Z"
}
```

---

### Authentication

#### Register

```
POST /auth/register
```

| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max:255 |
| `email` | string | Yes | valid email, unique |
| `password` | string | Yes | min:8, mixed case, numbers |
| `password_confirmation` | string | Yes | must match password |
| `phone` | string | No | max:20 |

**Response** `201`

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1Qi..."
  }
}
```

---

#### Login

```
POST /auth/login
```

| Field | Type | Required |
|-------|------|----------|
| `email` | string | Yes |
| `password` | string | Yes |

**Response** `200`

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Error** `401`

```json
{
  "success": false,
  "code": "invalid_login_credentials",
  "message": "Invalid credentials"
}
```

---

#### Logout

```
POST /auth/logout
```

> Requires `Authorization: Bearer {token}`

**Response** `200`

```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

#### Refresh Token

```
POST /auth/refresh
```

> Requires `Authorization: Bearer {token}`

**Response** `200`

```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

#### Current User

```
GET /auth/me
```

> Requires `Authorization: Bearer {token}`

**Response** `200`

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": ["user"],
    "permissions": ["users.view"]
  }
}
```

---

### Token Verification

#### Verify Token

```
POST /verify-token
```

> Requires `Authorization: Bearer {token}`

| Field | Type | Required | Note |
|-------|------|----------|------|
| `token` | string | No | Uses Bearer token if not provided |

**Response** `200`

```json
{
  "success": true,
  "data": {
    "valid": true,
    "user": { ... },
    "expires_at": "2025-01-01T01:00:00Z"
  }
}
```

---

#### Check Permission

```
POST /check-permission
```

> Requires `Authorization: Bearer {token}`

| Field | Type | Required |
|-------|------|----------|
| `user_id` | uuid | Yes |
| `permission` | string | Yes |

**Response** `200`

```json
{
  "success": true,
  "data": {
    "has_permission": true,
    "user_id": "uuid",
    "permission": "users.view"
  }
}
```

---

#### Check Multiple Permissions

```
POST /check-permissions
```

> Requires `Authorization: Bearer {token}`

| Field | Type | Required | Default |
|-------|------|----------|---------|
| `user_id` | uuid | Yes | |
| `permissions` | array | Yes | |
| `require_all` | boolean | No | `false` |

**Response** `200`

```json
{
  "success": true,
  "data": {
    "has_access": true,
    "user_id": "uuid",
    "permissions": {
      "users.view": true,
      "users.create": false
    },
    "require_all": false
  }
}
```

---

### Users Management

> All routes require `Authorization: Bearer {token}` + specific permission

| Method | Endpoint | Permission |
|--------|----------|------------|
| `GET` | `/users` | `users.view` |
| `POST` | `/users` | `users.create` |
| `GET` | `/users/{id}` | `users.view` |
| `PUT` | `/users/{id}` | `users.update` |
| `DELETE` | `/users/{id}` | `users.delete` |
| `POST` | `/users/{id}/roles` | `roles.update` |
| `POST` | `/users/{id}/permissions` | `permissions.update` |
| `GET` | `/users/{id}/permissions` | `permissions.view` |

---

### Roles Management

| Method | Endpoint | Permission |
|--------|----------|------------|
| `GET` | `/roles` | `roles.view` |
| `POST` | `/roles` | `roles.create` |
| `GET` | `/roles/{id}` | `roles.view` |
| `PUT` | `/roles/{id}` | `roles.update` |
| `DELETE` | `/roles/{id}` | `roles.delete` |
| `POST` | `/roles/{id}/permissions` | `roles.update` |
| `POST` | `/roles/{id}/permissions/add` | `roles.update` |
| `POST` | `/roles/{id}/permissions/remove` | `roles.update` |

---

### Permissions Management

| Method | Endpoint | Permission |
|--------|----------|------------|
| `GET` | `/permissions` | `permissions.view` |
| `POST` | `/permissions` | `permissions.create` |
| `POST` | `/permissions/resource` | `permissions.create` |
| `GET` | `/permissions/{id}` | `permissions.view` |
| `PUT` | `/permissions/{id}` | `permissions.update` |
| `DELETE` | `/permissions/{id}` | `permissions.delete` |

---

### Services Management

| Method | Endpoint | Permission |
|--------|----------|------------|
| `GET` | `/services` | `services.view` |
| `POST` | `/services` | `services.create` |
| `GET` | `/services/{id}` | `services.view` |
| `PUT` | `/services/{id}` | `services.update` |
| `DELETE` | `/services/{id}` | `services.delete` |
| `POST` | `/services/{id}/regenerate-token` | `services.update` |

---

### Service-to-Service Routes

> Requires `X-Service-Token` header instead of JWT

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/service/verify-token` | Verify a user's JWT token |
| `POST` | `/service/check-permission` | Check user permission |
| `POST` | `/service/user-info` | Get user information |

---

## Error Response Format

All errors follow a consistent format:

```json
{
  "success": false,
  "message": "Human-readable message",
  "error_code": "machine_readable_code"
}
```

### Error Codes

| Code | HTTP | Description |
|------|------|-------------|
| `authentication_required` | 401 | Missing or invalid token |
| `token_expired` | 401 | JWT token has expired |
| `token_invalid` | 401 | JWT token is malformed |
| `token_blacklisted` | 401 | Token was invalidated (logout) |
| `permission_denied` | 403 | Insufficient permissions |
| `validation_failed` | 400 | Request validation failed |
| `resource_not_found` | 404 | Resource does not exist |
| `rate_limit_exceeded` | 429 | Too many requests |

---

## Default Roles & Permissions

### Roles

| Role | Description |
|------|-------------|
| `super-admin` | Full system access (all permissions) |
| `admin` | Administrative access |
| `user` | Regular user access |

### Permissions

| Resource | Permissions |
|----------|-------------|
| Users | `users.view` `users.create` `users.update` `users.delete` |
| Roles | `roles.view` `roles.create` `roles.update` `roles.delete` |
| Permissions | `permissions.view` `permissions.create` `permissions.update` `permissions.delete` |
| Services | `services.view` `services.create` `services.update` `services.delete` |

---

## Project Structure

```
app/
  Http/
    Controllers/Api/
      AuthController.php        # Auth endpoints
      UserController.php        # User CRUD
      RoleController.php        # Role CRUD
      PermissionController.php  # Permission CRUD
      ServiceController.php     # Service management + S2S
    Middleware/
      SetLocale.php             # Language from Accept-Language
      CheckPermission.php       # Permission gate
      CheckRole.php             # Role gate
      ServiceTokenMiddleware.php # S2S token validation
    Requests/
      LoginRequest.php
      RegisterRequest.php
  Models/
    User.php                    # JWT Subject + RBAC
    Role.php
    Permission.php
    Service.php
  Services/
    AuthService.php             # Core auth logic
    AuditService.php            # Audit logging
    Cache/CacheService.php      # Caching layer
  Traits/
    HasRolesAndPermissions.php  # RBAC trait
  Support/
    ApiResponse.php             # Unified response helper
    ApiErrorCode.php            # Error code constants
lang/
  ar/messages.php               # Arabic translations
  en/messages.php               # English translations
config/
  auth-service.php              # Service configuration
  auth.php                      # Guards (JWT)
  jwt.php                       # JWT settings
```

---

---

<a name="arabic"></a>

<div dir="rtl">

## نظرة عامة

خدمة المصادقة والتفويض المركزية لمنظومة الخدمات المصغرة (Microservices). توفر مصادقة عبر JWT، نظام صلاحيات (RBAC)، وتواصل بين الخدمات.

### المميزات

- **مصادقة JWT** - توكنات آمنة مع دعم التجديد والحظر
- **نظام صلاحيات RBAC** - أدوار وصلاحيات مع تسلسل هرمي
- **متعدد اللغات** - عربي وإنجليزي عبر هيدر `Accept-Language`
- **تواصل بين الخدمات** - عبر `X-Service-Token`
- **سجل المراجعة** - تتبع تسجيل الدخول والخروج وتغيير الصلاحيات
- **تخزين مؤقت** - كاش للصلاحيات والأدوار والخدمات

---

## البدء السريع

### 1. التثبيت

```bash
git clone <repo-url> maham-auth-service
cd maham-auth-service
composer install
cp .env.example .env
```

### 2. الإعدادات

```env
# التطبيق
APP_URL=http://localhost:8000
APP_LOCALE=ar

# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maham_auth
DB_USERNAME=root
DB_PASSWORD=secret

# JWT
JWT_SECRET=     # شغّل: php artisan jwt:secret
JWT_TTL=60      # مدة التوكن بالدقائق
```

### 3. التشغيل

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

---

## دعم اللغات

جميع ردود الـ API تدعم العربية والإنجليزية. أرسل هيدر `Accept-Language` مع طلباتك:

| الهيدر | اللغة |
|--------|-------|
| `Accept-Language: ar` | عربي |
| `Accept-Language: en` | إنجليزي |
| *(بدون هيدر)* | الافتراضية (إنجليزي) |

**مثال:**

```bash
# رد بالعربي
curl -H "Accept-Language: ar" http://localhost:8000/api/auth/me

# رد بالإنجليزي
curl -H "Accept-Language: en" http://localhost:8000/api/auth/me
```

---

## مرجع الـ API

### الرابط الأساسي

```
http://localhost:8000/api
```

### الهيدرات المشتركة

| الهيدر | القيمة | مطلوب |
|--------|--------|-------|
| `Content-Type` | `application/json` | دائماً |
| `Accept` | `application/json` | دائماً |
| `Accept-Language` | `ar` أو `en` | اختياري |
| `Authorization` | `Bearer {token}` | المسارات المحمية |
| `X-Service-Token` | `{service_token}` | مسارات الخدمات |

---

### فحص الصحة

```
GET /health
```

**الرد:**

```json
{
  "status": "ok",
  "service": "auth-service",
  "version": "1.0.0",
  "timestamp": "2025-01-01T00:00:00.000000Z"
}
```

---

### المصادقة

#### التسجيل

```
POST /auth/register
```

| الحقل | النوع | مطلوب | القواعد |
|-------|-------|-------|---------|
| `name` | string | نعم | 255 حرف كحد أقصى |
| `email` | string | نعم | بريد صالح وفريد |
| `password` | string | نعم | 8 أحرف على الأقل، أحرف كبيرة وصغيرة وأرقام |
| `password_confirmation` | string | نعم | يطابق كلمة المرور |
| `phone` | string | لا | 20 حرف كحد أقصى |

**الرد** `201`

```json
{
  "success": true,
  "message": "تم التسجيل بنجاح",
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1Qi..."
  }
}
```

---

#### تسجيل الدخول

```
POST /auth/login
```

| الحقل | النوع | مطلوب |
|-------|-------|-------|
| `email` | string | نعم |
| `password` | string | نعم |

**الرد** `200`

```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**خطأ** `401`

```json
{
  "success": false,
  "code": "invalid_login_credentials",
  "message": "بيانات الدخول غير صحيحة"
}
```

---

#### تسجيل الخروج

```
POST /auth/logout
```

> يتطلب `Authorization: Bearer {token}`

**الرد** `200`

```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

---

#### تجديد التوكن

```
POST /auth/refresh
```

> يتطلب `Authorization: Bearer {token}`

**الرد** `200`

```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

#### بيانات المستخدم الحالي

```
GET /auth/me
```

> يتطلب `Authorization: Bearer {token}`

**الرد** `200`

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "roles": ["user"],
    "permissions": ["users.view"]
  }
}
```

---

### التحقق من التوكن

#### التحقق من صلاحية التوكن

```
POST /verify-token
```

> يتطلب `Authorization: Bearer {token}`

| الحقل | النوع | مطلوب | ملاحظة |
|-------|-------|-------|--------|
| `token` | string | لا | يستخدم Bearer token إذا لم يُرسل |

---

#### فحص صلاحية

```
POST /check-permission
```

> يتطلب `Authorization: Bearer {token}`

| الحقل | النوع | مطلوب |
|-------|-------|-------|
| `user_id` | uuid | نعم |
| `permission` | string | نعم |

---

#### فحص عدة صلاحيات

```
POST /check-permissions
```

> يتطلب `Authorization: Bearer {token}`

| الحقل | النوع | مطلوب | الافتراضي |
|-------|-------|-------|-----------|
| `user_id` | uuid | نعم | |
| `permissions` | array | نعم | |
| `require_all` | boolean | لا | `false` |

---

### إدارة المستخدمين

> جميع المسارات تتطلب `Authorization: Bearer {token}` + الصلاحية المحددة

| الطريقة | المسار | الصلاحية |
|---------|--------|----------|
| `GET` | `/users` | `users.view` |
| `POST` | `/users` | `users.create` |
| `GET` | `/users/{id}` | `users.view` |
| `PUT` | `/users/{id}` | `users.update` |
| `DELETE` | `/users/{id}` | `users.delete` |
| `POST` | `/users/{id}/roles` | `roles.update` |
| `POST` | `/users/{id}/permissions` | `permissions.update` |
| `GET` | `/users/{id}/permissions` | `permissions.view` |

---

### إدارة الأدوار

| الطريقة | المسار | الصلاحية |
|---------|--------|----------|
| `GET` | `/roles` | `roles.view` |
| `POST` | `/roles` | `roles.create` |
| `GET` | `/roles/{id}` | `roles.view` |
| `PUT` | `/roles/{id}` | `roles.update` |
| `DELETE` | `/roles/{id}` | `roles.delete` |
| `POST` | `/roles/{id}/permissions` | `roles.update` |
| `POST` | `/roles/{id}/permissions/add` | `roles.update` |
| `POST` | `/roles/{id}/permissions/remove` | `roles.update` |

---

### إدارة الصلاحيات

| الطريقة | المسار | الصلاحية |
|---------|--------|----------|
| `GET` | `/permissions` | `permissions.view` |
| `POST` | `/permissions` | `permissions.create` |
| `POST` | `/permissions/resource` | `permissions.create` |
| `GET` | `/permissions/{id}` | `permissions.view` |
| `PUT` | `/permissions/{id}` | `permissions.update` |
| `DELETE` | `/permissions/{id}` | `permissions.delete` |

---

### إدارة الخدمات

| الطريقة | المسار | الصلاحية |
|---------|--------|----------|
| `GET` | `/services` | `services.view` |
| `POST` | `/services` | `services.create` |
| `GET` | `/services/{id}` | `services.view` |
| `PUT` | `/services/{id}` | `services.update` |
| `DELETE` | `/services/{id}` | `services.delete` |
| `POST` | `/services/{id}/regenerate-token` | `services.update` |

---

### مسارات التواصل بين الخدمات

> يتطلب هيدر `X-Service-Token` بدلاً من JWT

| الطريقة | المسار | الوصف |
|---------|--------|-------|
| `POST` | `/service/verify-token` | التحقق من توكن مستخدم |
| `POST` | `/service/check-permission` | فحص صلاحية مستخدم |
| `POST` | `/service/user-info` | جلب بيانات مستخدم |

---

## صيغة ردود الأخطاء

جميع الأخطاء تتبع صيغة موحدة:

```json
{
  "success": false,
  "message": "رسالة مقروءة للمستخدم",
  "error_code": "رمز_الخطأ_البرمجي"
}
```

### رموز الأخطاء

| الرمز | HTTP | الوصف |
|-------|------|-------|
| `authentication_required` | 401 | التوكن مفقود أو غير صالح |
| `token_expired` | 401 | انتهت صلاحية التوكن |
| `token_invalid` | 401 | التوكن تالف أو غير صحيح |
| `token_blacklisted` | 401 | التوكن محظور (بعد تسجيل الخروج) |
| `permission_denied` | 403 | صلاحيات غير كافية |
| `validation_failed` | 400 | فشل التحقق من البيانات |
| `resource_not_found` | 404 | المورد غير موجود |
| `rate_limit_exceeded` | 429 | تم تجاوز حد الطلبات |

---

## الأدوار والصلاحيات الافتراضية

### الأدوار

| الدور | الوصف |
|-------|-------|
| `super-admin` | وصول كامل للنظام (جميع الصلاحيات) |
| `admin` | وصول إداري |
| `user` | مستخدم عادي |

### الصلاحيات

| المورد | الصلاحيات |
|--------|-----------|
| المستخدمين | `users.view` `users.create` `users.update` `users.delete` |
| الأدوار | `roles.view` `roles.create` `roles.update` `roles.delete` |
| الصلاحيات | `permissions.view` `permissions.create` `permissions.update` `permissions.delete` |
| الخدمات | `services.view` `services.create` `services.update` `services.delete` |

---

## هيكل المشروع

```
app/
  Http/
    Controllers/Api/
      AuthController.php        # نقاط المصادقة
      UserController.php        # عمليات المستخدمين
      RoleController.php        # عمليات الأدوار
      PermissionController.php  # عمليات الصلاحيات
      ServiceController.php     # إدارة الخدمات + S2S
    Middleware/
      SetLocale.php             # اللغة من Accept-Language
      CheckPermission.php       # بوابة الصلاحيات
      CheckRole.php             # بوابة الأدوار
      ServiceTokenMiddleware.php # التحقق من توكن الخدمة
    Requests/
      LoginRequest.php
      RegisterRequest.php
  Models/
    User.php                    # JWT Subject + RBAC
    Role.php
    Permission.php
    Service.php
  Services/
    AuthService.php             # منطق المصادقة الأساسي
    AuditService.php            # سجل المراجعة
    Cache/CacheService.php      # طبقة التخزين المؤقت
  Traits/
    HasRolesAndPermissions.php  # خاصية RBAC
  Support/
    ApiResponse.php             # مساعد الردود الموحدة
    ApiErrorCode.php            # ثوابت رموز الأخطاء
lang/
  ar/messages.php               # الترجمة العربية
  en/messages.php               # الترجمة الإنجليزية
config/
  auth-service.php              # إعدادات الخدمة
  auth.php                      # الحراس (JWT)
  jwt.php                       # إعدادات JWT
```

</div>
