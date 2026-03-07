# 🏗️ Maham Expo Platform — Frontend Integration Guide

> **Purpose**: This document is a comprehensive system prompt / developer guide for building any frontend (Web, Mobile, Dashboard) that integrates with the Maham Expo APIs. Give this to any AI assistant or developer and they will have everything needed to build and integrate.

---

## 📋 Table of Contents

1. [System Architecture](#1-system-architecture)
2. [Base URLs & Domains](#2-base-urls--domains)
3. [Authentication Flow](#3-authentication-flow)
4. [API Response Format](#4-api-response-format)
5. [Pagination](#5-pagination)
6. [Localization (i18n)](#6-localization-i18n)
7. [Error Handling](#7-error-handling)
8. [Public Endpoints (No Auth)](#8-public-endpoints-no-auth)
9. [Auth Service Endpoints](#9-auth-service-endpoints)
10. [Expo Service — Self-Service Endpoints](#10-expo-service--self-service-endpoints)
11. [Expo Service — My (Owner-Scoped) Endpoints](#11-expo-service--my-owner-scoped-endpoints)
12. [Expo Service — Management Endpoints](#12-expo-service--management-endpoints)
13. [Payment Flow (Tap Gateway)](#13-payment-flow-tap-gateway)
14. [Phone OTP Verification Flow](#14-phone-otp-verification-flow)
15. [Data Models & Enums](#15-data-models--enums)
16. [Permissions System](#16-permissions-system)
17. [Dashboard Settings API](#17-dashboard-settings-api)
18. [Error Codes Reference](#18-error-codes-reference)
19. [Implementation Checklist](#19-implementation-checklist)

---

## 1. System Architecture

The platform consists of **two microservices**:

| Service | Purpose | Handles |
|---------|---------|---------|
| **Auth Service** | Authentication & Authorization | Users, Roles, Permissions, JWT Tokens, OTP, Password Reset |
| **Expo Service** | Business Logic | Events, Spaces, Rentals, Payments, Invoices, Sponsors, CMS, Settings |

**Key Architecture Points**:
- Auth Service issues **JWT tokens** upon login/register
- Expo Service **validates tokens** by calling Auth Service internally
- All authorization is **permission-based** (not role-based) — roles are just containers for permissions
- The `super-admin` role bypasses all permission checks
- Both services share the same user IDs (UUIDs)

```
┌─────────────┐     JWT Token      ┌─────────────┐
│   Frontend   │ ─────────────────► │ Expo Service │
│  (Web/App)   │                    │   (API)      │
└──────┬───────┘                    └──────┬───────┘
       │                                   │
       │  JWT Token                        │ Internal: verify-token
       ▼                                   ▼
┌─────────────┐                    ┌─────────────┐
│ Auth Service │ ◄─────────────── │ Auth Service │
│   (API)      │   Docker Network  │  (Internal)  │
└─────────────┘                    └─────────────┘
```

---

## 2. Base URLs & Domains

| Service | Base URL |
|---------|----------|
| Auth Service | `https://auth-service-api.mahamexpo.sa/api/v1` |
| Expo Service | `https://expo-service-api.mahamexpo.sa/api/v1` |

> **Important**: All API endpoints below are prefixed with `/api/v1/` under their respective domain.

**Health Check Endpoints**:
- Auth: `GET https://auth-service-api.mahamexpo.sa/api/health`
- Expo: `GET https://expo-service-api.mahamexpo.sa/api/health`

---

## 3. Authentication Flow

### 3.1 Register

```
POST {AUTH_URL}/auth/register
```

**Request Body**:
```json
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "phone": "+966501234567"
}
```

**Validation Rules**:
| Field | Rules |
|-------|-------|
| `name` | required, string, max 255 |
| `email` | required, email, unique |
| `password` | required, confirmed, min 8, must contain uppercase + lowercase + number |
| `phone` | required, string, max 20, unique |

**Response** (201):
```json
{
  "success": true,
  "message": "تم التسجيل بنجاح",
  "data": {
    "user": {
      "id": "uuid-here",
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "avatar": null,
      "status": "active",
      "roles": ["merchant"],
      "permissions": ["spaces.view", "visit-requests.create", "..."]
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

### 3.2 Login

```
POST {AUTH_URL}/auth/login
```

**Request Body**:
```json
{
  "identifier": "ahmed@example.com",
  "password": "SecurePass123"
}
```

> **Note**: `identifier` accepts **email** or **phone number**. The system auto-detects the type.

**Response** (200):
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "user": {
      "id": "uuid",
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "phone": "+966501234567",
      "avatar": null,
      "status": "active",
      "email_verified_at": "2024-01-15T10:00:00Z",
      "phone_verified_at": null,
      "roles": ["merchant"],
      "permissions": ["spaces.view", "visit-requests.create", "..."]
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Login Error** (401):
```json
{
  "success": false,
  "code": "invalid_login_credentials",
  "message": "بيانات الدخول غير صحيحة"
}
```

### 3.3 Using the Token

Add the JWT token to ALL authenticated requests:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### 3.4 Refresh Token

```
POST {AUTH_URL}/auth/refresh
Authorization: Bearer {current_token}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "token": "new-jwt-token-here",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### 3.5 Get Current User

```
GET {AUTH_URL}/auth/me
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "phone": "+966501234567",
    "avatar": null,
    "status": "active",
    "email_verified_at": "2024-01-15T10:00:00Z",
    "phone_verified_at": "2024-01-15T10:05:00Z",
    "roles": ["merchant"],
    "permissions": ["spaces.view", "rental-requests.create", "..."]
  }
}
```

### 3.6 Logout

```
POST {AUTH_URL}/auth/logout
Authorization: Bearer {token}
```

### 3.7 Token Expiration Handling

- Tokens expire after `expires_in` seconds (default: 3600 = 1 hour)
- When you get a **401** response with `token_expired` error code, call `/auth/refresh` with the current token
- If refresh also fails, redirect user to login page
- **Recommended**: Implement an Axios/Fetch interceptor that auto-refreshes on 401

```javascript
// Example: Axios Interceptor
api.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401 && error.response?.data?.error_code === 'token_expired') {
      const newToken = await refreshToken();
      error.config.headers.Authorization = `Bearer ${newToken}`;
      return api.request(error.config);
    }
    return Promise.reject(error);
  }
);
```

---

## 4. API Response Format

### 4.1 Success Response

All successful responses follow this envelope:

```json
{
  "success": true,
  "message": "Optional success message",
  "data": { ... }
}
```

### 4.2 Error Response

```json
{
  "success": false,
  "message": "Human-readable error message (Arabic)",
  "error_code": "machine_readable_error_code",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### 4.3 Created Response (201)

```json
{
  "success": true,
  "message": "تم الإنشاء بنجاح",
  "data": { ... }
}
```

---

## 5. Pagination

All list endpoints return paginated data:

```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://...?page=1",
    "last": "https://...?page=5",
    "prev": null,
    "next": "https://...?page=2"
  }
}
```

**Query Parameters for Pagination**:
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 15 | Items per page (max 50) |
| `sort_by` | string | varies | Field to sort by |
| `sort_order` | string | `desc` | `asc` or `desc` |

---

## 6. Localization (i18n)

The API supports **Arabic** and **English**:

**Set language via header**:
```
Accept-Language: ar    // Arabic (default)
Accept-Language: en    // English
```

**Bilingual Fields**: Many models have dual-language fields:
- `name` / `name_ar`
- `description` / `description_ar`
- `title` / `title_ar`
- `address` / `address_ar`

The API may return a `localized_name` accessor that automatically picks the correct language based on the `Accept-Language` header.

---

## 7. Error Handling

### HTTP Status Codes Used

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request / Validation Error |
| 401 | Unauthorized (no/invalid/expired token) |
| 403 | Forbidden (no permission) |
| 404 | Not Found |
| 409 | Conflict (duplicate resource) |
| 422 | Unprocessable Entity (business logic error) |
| 429 | Rate Limited |
| 500 | Server Error |
| 502 | Service Unavailable |

### Validation Error Example (400)

```json
{
  "success": false,
  "message": "بيانات غير صحيحة",
  "error_code": "validation_failed",
  "errors": {
    "email": ["حقل البريد الإلكتروني مطلوب"],
    "password": ["يجب أن تكون كلمة المرور 8 أحرف على الأقل"]
  }
}
```

### Permission Denied Example (403)

```json
{
  "success": false,
  "message": "ليس لديك صلاحية لهذا الإجراء",
  "error_code": "permission_denied",
  "errors": {
    "required_permission": "events.create"
  }
}
```

---

## 8. Public Endpoints (No Auth)

These endpoints on the **Expo Service** require NO authentication:

### 8.1 Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories` | List all categories |
| GET | `/categories/{id}` | Show category details |

**Category Object**:
```json
{
  "id": "uuid",
  "name": "Technology",
  "name_ar": "تقنية",
  "icon": "icon-url",
  "description": "Tech events",
  "description_ar": "فعاليات تقنية",
  "is_active": true,
  "sort_order": 1
}
```

### 8.2 Cities

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/cities` | List all cities |
| GET | `/cities/{id}` | Show city details |

**City Object**:
```json
{
  "id": "uuid",
  "name": "Riyadh",
  "name_ar": "الرياض",
  "region": "Central",
  "region_ar": "المنطقة الوسطى",
  "latitude": 24.7136,
  "longitude": 46.6753,
  "is_active": true
}
```

### 8.3 Events

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/events` | List published events |
| GET | `/events/featured` | List featured events |
| GET | `/events/{id}` | Show event details |
| GET | `/events/{id}/spaces` | List spaces in event |
| GET | `/events/{id}/sections` | List sections in event |
| GET | `/events/{id}/sponsors` | List sponsors for event |
| GET | `/events/{id}/sponsor-packages` | List sponsor packages |

**Event Filters** (query params):
| Param | Type | Description |
|-------|------|-------------|
| `category_id` | uuid | Filter by category |
| `city_id` | uuid | Filter by city |
| `status` | string | `draft`, `published`, `ended`, `cancelled` |
| `is_featured` | boolean | Featured events only |
| `search` | string | Search by name/description |
| `min_price` | decimal | Min space price |
| `max_price` | decimal | Max space price |
| `min_area` | decimal | Min space area |
| `max_area` | decimal | Max space area |
| `rental_duration` | string | `daily`, `weekly`, `monthly`, `full_event` |

**Event Object**:
```json
{
  "id": "uuid",
  "name": "Maham Expo 2024",
  "name_ar": "معرض مهام 2024",
  "description": "Annual exhibition...",
  "description_ar": "المعرض السنوي...",
  "category": { "id": "uuid", "name": "Technology" },
  "city": { "id": "uuid", "name": "Riyadh" },
  "address": "King Fahd Road",
  "address_ar": "طريق الملك فهد",
  "latitude": 24.7136,
  "longitude": 46.6753,
  "start_date": "2024-06-01",
  "end_date": "2024-06-05",
  "opening_time": "09:00",
  "closing_time": "22:00",
  "images": ["url1", "url2"],
  "images_360": ["url1"],
  "features": ["WiFi", "Parking"],
  "features_ar": ["واي فاي", "مواقف"],
  "organizer_name": "Maham Group",
  "organizer_phone": "+966...",
  "organizer_email": "info@maham.sa",
  "website": "https://mahamexpo.sa",
  "status": "published",
  "is_featured": true,
  "views_count": 1520,
  "expected_visitors": 5000,
  "investment_opportunity_rating": 4.5,
  "promotional_video": "https://...",
  "is_ongoing": true,
  "is_upcoming": false,
  "is_ended": false,
  "available_spaces_count": 42,
  "total_spaces_count": 80,
  "min_price": 500.00
}
```

### 8.4 Spaces

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/spaces/{id}` | Show space details |

**Space Object**:
```json
{
  "id": "uuid",
  "event_id": "uuid",
  "name": "Booth A-101",
  "name_ar": "جناح أ-101",
  "description": "Corner booth with premium location",
  "description_ar": "جناح زاوية بموقع مميز",
  "location_code": "A-101",
  "area_sqm": 25.00,
  "price_per_day": 200.00,
  "price_total": 1000.00,
  "images": ["url1", "url2"],
  "images_360": ["url1"],
  "amenities": ["Electricity", "Internet"],
  "amenities_ar": ["كهرباء", "إنترنت"],
  "status": "available",
  "floor_number": 1,
  "section": { "id": "uuid", "name": "Hall A" },
  "space_type": "booth",
  "payment_system": "full",
  "rental_duration": "full_event",
  "allowed_business_type": "merchant",
  "services": [{ "id": "uuid", "name": "Electricity" }]
}
```

### 8.5 Services

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/services` | List available services |

### 8.6 Statistics (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/statistics` | General platform statistics |
| GET | `/statistics/events` | Event statistics |
| GET | `/statistics/spaces` | Space statistics |

### 8.7 Ratings (Public - Read)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/ratings` | List approved ratings |
| GET | `/ratings/summary` | Ratings summary/averages |

### 8.8 Pages (CMS)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/pages` | List active pages |
| GET | `/pages/{slug}` | Show page by slug |

### 8.9 FAQs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/faqs` | List FAQs |
| GET | `/faqs/categories` | List FAQ categories |
| GET | `/faqs/{id}` | Show FAQ |
| POST | `/faqs/{id}/helpful` | Mark FAQ as helpful |

### 8.10 Banners

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/banners` | List active banners |
| POST | `/banners/{id}/click` | Track banner click |

### 8.11 Tracking (Anonymous)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/track/view` | Track page/resource view |
| POST | `/track/action` | Track user action |

### 8.12 Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/webhooks/tap` | Tap payment webhook (system use) |

---

## 9. Auth Service Endpoints

All on `https://auth-service-api.mahamexpo.sa/api/v1`

### 9.1 Public (No Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register new user |
| POST | `/auth/login` | Login (throttled: 10/min) |
| POST | `/auth/forgot-password` | Request password reset email |
| POST | `/auth/reset-password` | Reset password with token |

**Forgot Password**:
```json
// Request
{ "email": "user@example.com" }

// Response
{ "success": true, "message": "تم إرسال رابط إعادة التعيين" }
```

**Reset Password**:
```json
// Request
{
  "email": "user@example.com",
  "token": "reset-token-from-email",
  "password": "NewSecurePass123",
  "password_confirmation": "NewSecurePass123"
}
```

### 9.2 Authenticated

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/logout` | Logout |
| POST | `/auth/refresh` | Refresh JWT token |
| GET | `/auth/me` | Get current user info |
| POST | `/auth/change-password` | Change password |
| PUT | `/auth/profile` | Update profile |
| POST | `/auth/email/send-verification` | Send email verification code |
| POST | `/auth/email/verify` | Verify email with 6-digit code |
| POST | `/auth/phone/send-otp` | Send phone OTP (SMS/WhatsApp) |
| POST | `/auth/phone/verify-otp` | Verify phone OTP |

**Change Password**:
```json
{
  "current_password": "OldPass123",
  "password": "NewPass456",
  "password_confirmation": "NewPass456"
}
```

**Update Profile**:
```json
{
  "name": "أحمد محمد",
  "email": "new@example.com",
  "phone": "+966501234567"
}
```

**Email Verification**:
```json
// Send
POST /auth/email/send-verification
// No body needed

// Verify
POST /auth/email/verify
{ "code": "123456" }
```

**Phone OTP** (see [Section 14](#14-phone-otp-verification-flow)):
```json
// Send
POST /auth/phone/send-otp
{ "phone": "+966501234567", "channel": "sms" }

// Verify
POST /auth/phone/verify-otp
{ "phone": "+966501234567", "code": "1234" }
```

### 9.3 Users Management (Admin)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/users` | `users.view` | List users |
| POST | `/users` | `users.create` | Create user |
| GET | `/users/{id}` | `users.view` | Show user |
| PUT | `/users/{id}` | `users.update` | Update user |
| DELETE | `/users/{id}` | `users.delete` | Delete user |
| POST | `/users/{id}/roles` | `roles.update` | Assign roles |
| POST | `/users/{id}/permissions` | `permissions.update` | Assign permissions |
| GET | `/users/{id}/permissions` | `permissions.view` | List user permissions |

### 9.4 Roles Management (Admin)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/roles` | `roles.view` | List roles |
| POST | `/roles` | `roles.create` | Create role |
| GET | `/roles/{id}` | `roles.view` | Show role |
| PUT | `/roles/{id}` | `roles.update` | Update role |
| DELETE | `/roles/{id}` | `roles.delete` | Delete role |
| POST | `/roles/{id}/permissions` | `roles.update` | Sync permissions |
| POST | `/roles/{id}/permissions/add` | `roles.update` | Add permissions |
| POST | `/roles/{id}/permissions/remove` | `roles.update` | Remove permissions |

### 9.5 Permissions Management (Admin)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/permissions` | `permissions.view` | List permissions |
| POST | `/permissions` | `permissions.create` | Create permission |
| POST | `/permissions/resource` | `permissions.create` | Create CRUD permissions for resource |
| GET | `/permissions/{id}` | `permissions.view` | Show permission |
| PUT | `/permissions/{id}` | `permissions.update` | Update permission |
| DELETE | `/permissions/{id}` | `permissions.delete` | Delete permission |

### 9.6 Dashboard Stats (Admin)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/admin/stats/users` | `users.view` | User statistics |

---

## 10. Expo Service — Self-Service Endpoints

All on `https://expo-service-api.mahamexpo.sa/api/v1` — require `Authorization: Bearer {token}`

### 10.1 Business Profile

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/profile` | Get own profile |
| POST | `/profile` | Create business profile |
| PUT | `/profile` | Update business profile |

**Create/Update Profile**:
```json
{
  "company_name": "شركة المعرض",
  "company_name_ar": "شركة المعرض",
  "commercial_registration_number": "1234567890",
  "commercial_registration_image": "file (upload)",
  "national_id_number": "1234567890",
  "national_id_image": "file (upload)",
  "company_logo": "file (upload)",
  "avatar": "file (upload)",
  "company_address": "Riyadh, KSA",
  "company_address_ar": "الرياض، السعودية",
  "contact_phone": "+966501234567",
  "contact_email": "info@company.sa",
  "website": "https://company.sa",
  "business_type": "merchant"
}
```

> **Note**: `business_type` is either `investor` or `merchant`

**Profile Status Flow**:
```
Created → pending → approved ✅
                  → rejected ❌ (can resubmit)
```

### 10.2 Favorites

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/favorites` | List favorites |
| POST | `/favorites` | Add favorite |
| DELETE | `/favorites/{id}` | Remove favorite |

**Add Favorite**:
```json
{
  "favoritable_type": "event",
  "favoritable_id": "uuid-of-event"
}
```
> `favoritable_type`: `event` or `space`

### 10.3 Notifications

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/notifications` | `notifications.view` | List notifications |
| GET | `/notifications/unread-count` | `notifications.view` | Unread count |
| PUT | `/notifications/{id}/read` | `notifications.update` | Mark as read |
| PUT | `/notifications/read-all` | `notifications.update` | Mark all read |
| GET | `/notifications/preferences` | `notification-preferences.view` | Get preferences |
| PUT | `/notifications/preferences` | `notification-preferences.update` | Update preferences |

**Notification Object**:
```json
{
  "id": "uuid",
  "title": "طلب زيارة جديد",
  "title_ar": "طلب زيارة جديد",
  "body": "تم تقديم طلب زيارة لموقعك",
  "body_ar": "تم تقديم طلب زيارة لموقعك",
  "type": "visit_request",
  "data": { "visit_request_id": "uuid" },
  "action_url": "/visit-requests/uuid",
  "read_at": null,
  "created_at": "2024-01-15T10:00:00Z"
}
```

**Update Preferences**:
```json
{
  "email_enabled": true,
  "push_enabled": true,
  "in_app_enabled": true,
  "notify_request_updates": true,
  "notify_payment_reminders": true,
  "notify_event_updates": true,
  "notify_contract_milestones": true,
  "notify_promotions": false,
  "notify_support_updates": true,
  "notify_ratings": true
}
```

### 10.4 Devices (Push Notifications)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/devices` | List registered devices |
| POST | `/devices` | Register device |
| DELETE | `/devices` | Unregister device |

**Register Device**:
```json
{
  "subscription_id": "onesignal-subscription-id",
  "push_token": "device-push-token",
  "type": "AndroidPush",
  "device_model": "Samsung S24",
  "device_os": "Android 14",
  "app_version": "1.0.0"
}
```

### 10.5 Ratings

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| POST | `/ratings` | `ratings.create` | Submit rating |
| PUT | `/ratings/{id}` | `ratings.update` | Update own rating |
| DELETE | `/ratings/{id}` | `ratings.delete` | Delete own rating |

**Submit Rating**:
```json
{
  "rateable_type": "space",
  "rateable_id": "uuid",
  "type": "space",
  "overall_rating": 5,
  "cleanliness_rating": 4,
  "location_rating": 5,
  "facilities_rating": 4,
  "value_rating": 3,
  "communication_rating": 5,
  "comment": "Excellent space!",
  "comment_ar": "مساحة ممتازة!",
  "rental_request_id": "uuid"
}
```

### 10.6 Support Tickets

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/support-tickets` | `support-tickets.view` | List own tickets |
| POST | `/support-tickets` | `support-tickets.create` | Create ticket |
| GET | `/support-tickets/{id}` | `support-tickets.view` | Show ticket |
| POST | `/support-tickets/{id}/reply` | `support-tickets.reply` | Reply to ticket |
| PUT | `/support-tickets/{id}/close` | `support-tickets.close` | Close ticket |
| PUT | `/support-tickets/{id}/reopen` | `support-tickets.create` | Reopen ticket |

**Create Ticket**:
```json
{
  "subject": "مشكلة في الدفع",
  "subject_ar": "مشكلة في الدفع",
  "description": "لم أستطع إتمام عملية الدفع",
  "description_ar": "لم أستطع إتمام عملية الدفع",
  "category": "billing",
  "priority": "high",
  "attachments": ["file1", "file2"]
}
```

### 10.7 Invoices (Own)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/invoices` | `invoices.view` | List own invoices |
| GET | `/invoices/{id}` | `invoices.view` | Show invoice |

**Invoice Object**:
```json
{
  "id": "uuid",
  "invoice_number": "INV-2024-001",
  "title": "رسوم استئجار جناح",
  "title_ar": "رسوم استئجار جناح",
  "subtotal": 5000.00,
  "tax_amount": 750.00,
  "discount_amount": 0.00,
  "total_amount": 5750.00,
  "paid_amount": 0.00,
  "status": "issued",
  "issue_date": "2024-01-15",
  "due_date": "2024-02-15",
  "paid_at": null,
  "items": [
    { "description": "Booth rental", "quantity": 1, "unit_price": 5000.00, "total": 5000.00 }
  ]
}
```

### 10.8 Payments

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/payments` | `invoices.view` | List own payments |
| GET | `/payments/{id}` | `invoices.view` | Show payment details |
| POST | `/payments/pay-invoice` | `invoices.view` | Pay invoice (see [Section 13](#13-payment-flow-tap-gateway)) |
| GET | `/payments/{id}/status` | `invoices.view` | Check payment status |

### 10.9 Visit Requests

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/visit-requests` | `visit-requests.view` | List own requests |
| POST | `/visit-requests` | `visit-requests.create` | Create request |
| GET | `/visit-requests/{id}` | `visit-requests.view` | Show request |
| PUT | `/visit-requests/{id}` | `visit-requests.update` | Update request |
| DELETE | `/visit-requests/{id}` | `visit-requests.delete` | Delete request |

**Create Visit Request**:
```json
{
  "event_id": "uuid",
  "visit_date": "2024-06-01",
  "visit_time": "10:00",
  "visitors_count": 3,
  "notes": "نحتاج ترجمة إنجليزية",
  "contact_phone": "+966501234567"
}
```

### 10.10 Rental Requests

> **Requires verified business profile** (approved status)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/rental-requests` | `rental-requests.view` | List own requests |
| POST | `/rental-requests` | `rental-requests.create` | Create request |
| GET | `/rental-requests/{id}` | `rental-requests.view` | Show request |
| PUT | `/rental-requests/{id}` | `rental-requests.update` | Update request |
| DELETE | `/rental-requests/{id}` | `rental-requests.delete` | Delete request |

**Create Rental Request**:
```json
{
  "space_id": "uuid",
  "start_date": "2024-06-01",
  "end_date": "2024-06-05",
  "notes": "نحتاج كهرباء إضافية"
}
```

---

## 11. Expo Service — My (Owner-Scoped) Endpoints

These are for users who OWN resources (investors who own spaces, sponsors, etc.)

All under `/my/` prefix.

### 11.1 Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/my/dashboard` | Unified dashboard for current user |

### 11.2 My Spaces (Investor)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/spaces` | `spaces.view` | List own spaces |
| POST | `/my/spaces` | `spaces.create` | Create space |
| GET | `/my/spaces/{id}` | `spaces.view` | Show space |
| PUT | `/my/spaces/{id}` | `spaces.update` | Update space |
| DELETE | `/my/spaces/{id}` | `spaces.delete` | Delete space |
| POST | `/my/spaces/{id}/services` | `spaces.update` | Add services |
| DELETE | `/my/spaces/{id}/services` | `spaces.update` | Remove services |

### 11.3 Received Visit Requests (Investor)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/received-visit-requests` | `visit-requests.approve` | List received |
| GET | `/my/received-visit-requests/pending-count` | `visit-requests.approve` | Pending count |
| GET | `/my/received-visit-requests/{id}` | `visit-requests.approve` | Show request |
| PUT | `/my/received-visit-requests/{id}/approve` | `visit-requests.approve` | Approve |
| PUT | `/my/received-visit-requests/{id}/reject` | `visit-requests.reject` | Reject |

### 11.4 Received Rental Requests (Investor)

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/received-rental-requests` | `rental-requests.approve` | List received |
| GET | `/my/received-rental-requests/pending-count` | `rental-requests.approve` | Pending count |
| GET | `/my/received-rental-requests/{id}` | `rental-requests.approve` | Show request |
| PUT | `/my/received-rental-requests/{id}/approve` | `rental-requests.approve` | Approve |
| PUT | `/my/received-rental-requests/{id}/reject` | `rental-requests.reject` | Reject |

### 11.5 My Payments

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/payments` | `payments.view` | List payments |
| GET | `/my/payments/summary` | `payments.view` | Payment summary |
| GET | `/my/payments/{id}` | `payments.view` | Show payment |

### 11.6 My Rental Contracts

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/rental-contracts` | `rental-contracts.view` | List contracts |
| GET | `/my/rental-contracts/{id}` | `rental-contracts.view` | Show contract |
| PUT | `/my/rental-contracts/{id}/sign` | `rental-contracts.sign` | Sign contract |

### 11.7 My Sponsor Contracts

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/sponsor-contracts` | `sponsor-contracts.view` | List contracts |
| GET | `/my/sponsor-contracts/{id}` | `sponsor-contracts.view` | Show contract |

### 11.8 My Sponsor Payments

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/sponsor-payments` | `sponsor-payments.view` | List payments |
| GET | `/my/sponsor-payments/{id}` | `sponsor-payments.view` | Show payment |

### 11.9 My Sponsor Assets

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/sponsor-assets` | `sponsor-assets.view` | List assets |
| POST | `/my/sponsor-assets` | `sponsor-assets.create` | Upload asset |
| GET | `/my/sponsor-assets/{id}` | `sponsor-assets.view` | Show asset |
| PUT | `/my/sponsor-assets/{id}` | `sponsor-assets.update` | Update asset |
| DELETE | `/my/sponsor-assets/{id}` | `sponsor-assets.delete` | Delete asset |

### 11.10 My Sponsor Exposure

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/my/sponsor-exposure` | `sponsor-exposure.view` | Exposure data |
| GET | `/my/sponsor-exposure/summary` | `sponsor-exposure.view` | Exposure summary |

### 11.11 My Activity

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/my/activity` | Activity history |
| GET | `/my/activity/summary` | Activity summary |

---

## 12. Expo Service — Management Endpoints

All under `/manage/` prefix. Requires appropriate permissions.

### 12.1 Dashboard & Statistics

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/manage/dashboard` | `reports.view` | Admin dashboard |
| GET | `/manage/statistics` | `reports.view` | Detailed statistics |

### 12.2 Events Management

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/manage/events` | `events.view` | List all events |
| POST | `/manage/events` | `events.create` | Create event |
| GET | `/manage/events/{id}` | `events.view` | Show event |
| PUT | `/manage/events/{id}` | `events.update` | Update event |
| DELETE | `/manage/events/{id}` | `events.delete` | Delete event |
| GET | `/manage/events/{id}/sections` | `sections.view` | List sections |
| POST | `/manage/events/{id}/sections` | `sections.create` | Create section |
| GET | `/manage/events/{id}/spaces` | `spaces.view` | List spaces |
| POST | `/manage/events/{id}/spaces` | `spaces.create` | Create space |
| GET | `/manage/events/{id}/sponsor-packages` | `sponsor-packages.view` | List packages |
| POST | `/manage/events/{id}/sponsor-packages` | `sponsor-packages.create` | Create package |

### 12.3 Sections

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sections/{id}` | `sections.view` |
| PUT | `/manage/sections/{id}` | `sections.update` |
| DELETE | `/manage/sections/{id}` | `sections.delete` |

### 12.4 Spaces

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/spaces/{id}` | `spaces.view` |
| PUT | `/manage/spaces/{id}` | `spaces.update` |
| DELETE | `/manage/spaces/{id}` | `spaces.delete` |

### 12.5 Services

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/services` | `expo-services.view` |
| POST | `/manage/services` | `expo-services.create` |
| GET | `/manage/services/{id}` | `expo-services.view` |
| PUT | `/manage/services/{id}` | `expo-services.update` |
| DELETE | `/manage/services/{id}` | `expo-services.delete` |

### 12.6 Categories

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/categories` | `categories.view` |
| POST | `/manage/categories` | `categories.create` |
| GET | `/manage/categories/{id}` | `categories.view` |
| PUT | `/manage/categories/{id}` | `categories.update` |
| DELETE | `/manage/categories/{id}` | `categories.delete` |

### 12.7 Cities

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/cities` | `cities.view` |
| POST | `/manage/cities` | `cities.create` |
| GET | `/manage/cities/{id}` | `cities.view` |
| PUT | `/manage/cities/{id}` | `cities.update` |
| DELETE | `/manage/cities/{id}` | `cities.delete` |

### 12.8 System Settings

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/manage/settings` | `settings.view` | Get all settings |
| GET | `/manage/settings/{key}` | `settings.view` | Get single setting |
| PUT | `/manage/settings` | `settings.update` | Update settings (see [Section 17](#17-dashboard-settings-api)) |

### 12.9 Users / Profiles Management

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/manage/users` | `profiles.view-all` | List users |
| GET | `/manage/users/{id}` | `profiles.view-all` | Show user |
| PUT | `/manage/users/{id}/approve` | `profiles.approve` | Approve user |
| PUT | `/manage/users/{id}/reject` | `profiles.reject` | Reject user |
| PUT | `/manage/users/{id}/suspend` | `profiles.reject` | Suspend user |

### 12.10 Business Profiles

| Method | Endpoint | Permission | Description |
|--------|----------|------------|-------------|
| GET | `/manage/profiles` | `profiles.view-all` | List profiles |
| GET | `/manage/profiles/{id}` | `profiles.view-all` | Show profile |
| PUT | `/manage/profiles/{id}/approve` | `profiles.approve` | Approve |
| PUT | `/manage/profiles/{id}/reject` | `profiles.reject` | Reject |

### 12.11 Visit Requests

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/visit-requests` | `visit-requests.view-all` |
| GET | `/manage/visit-requests/{id}` | `visit-requests.view-all` |
| PUT | `/manage/visit-requests/{id}/approve` | `visit-requests.approve` |
| PUT | `/manage/visit-requests/{id}/reject` | `visit-requests.reject` |

### 12.12 Rental Requests

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/rental-requests` | `rental-requests.view-all` |
| GET | `/manage/rental-requests/{id}` | `rental-requests.view-all` |
| PUT | `/manage/rental-requests/{id}/approve` | `rental-requests.approve` |
| PUT | `/manage/rental-requests/{id}/reject` | `rental-requests.reject` |
| POST | `/manage/rental-requests/{id}/payment` | `rental-requests.record-payment` |

### 12.13 Rental Contracts

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/rental-contracts` | `rental-contracts.view-all` |
| POST | `/manage/rental-contracts` | `rental-contracts.create` |
| GET | `/manage/rental-contracts/{id}` | `rental-contracts.view-all` |
| PUT | `/manage/rental-contracts/{id}` | `rental-contracts.update` |
| PUT | `/manage/rental-contracts/{id}/approve` | `rental-contracts.approve` |
| PUT | `/manage/rental-contracts/{id}/reject` | `rental-contracts.reject` |
| PUT | `/manage/rental-contracts/{id}/terminate` | `rental-contracts.terminate` |

### 12.14 Sponsors

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsors` | `sponsors.view-all` |
| POST | `/manage/sponsors` | `sponsors.create` |
| GET | `/manage/sponsors/{id}` | `sponsors.view-all` |
| PUT | `/manage/sponsors/{id}` | `sponsors.update` |
| DELETE | `/manage/sponsors/{id}` | `sponsors.delete` |
| PUT | `/manage/sponsors/{id}/approve` | `sponsors.approve` |
| PUT | `/manage/sponsors/{id}/activate` | `sponsors.approve` |
| PUT | `/manage/sponsors/{id}/suspend` | `sponsors.reject` |

### 12.15 Sponsor Packages

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsor-packages/{id}` | `sponsor-packages.view` |
| PUT | `/manage/sponsor-packages/{id}` | `sponsor-packages.update` |
| DELETE | `/manage/sponsor-packages/{id}` | `sponsor-packages.delete` |

### 12.16 Sponsor Contracts

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsor-contracts` | `sponsor-contracts.view-all` |
| POST | `/manage/sponsor-contracts` | `sponsor-contracts.create` |
| GET | `/manage/sponsor-contracts/{id}` | `sponsor-contracts.view-all` |
| PUT | `/manage/sponsor-contracts/{id}` | `sponsor-contracts.update` |
| PUT | `/manage/sponsor-contracts/{id}/approve` | `sponsor-contracts.approve` |
| PUT | `/manage/sponsor-contracts/{id}/reject` | `sponsor-contracts.reject` |
| PUT | `/manage/sponsor-contracts/{id}/complete` | `sponsor-contracts.approve` |

### 12.17 Sponsor Payments

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsor-payments` | `sponsor-payments.view-all` |
| POST | `/manage/sponsor-payments` | `sponsor-payments.create` |
| GET | `/manage/sponsor-payments/{id}` | `sponsor-payments.view-all` |
| PUT | `/manage/sponsor-payments/{id}` | `sponsor-payments.create` |
| PUT | `/manage/sponsor-payments/{id}/mark-paid` | `sponsor-payments.create` |

### 12.18 Sponsor Benefits

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsor-benefits` | `sponsor-benefits.view` |
| POST | `/manage/sponsor-benefits` | `sponsor-benefits.create` |
| GET | `/manage/sponsor-benefits/{id}` | `sponsor-benefits.view` |
| PUT | `/manage/sponsor-benefits/{id}` | `sponsor-benefits.update` |
| PUT | `/manage/sponsor-benefits/{id}/deliver` | `sponsor-benefits.deliver` |

### 12.19 Sponsor Assets

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/sponsor-assets` | `sponsor-assets.view` |
| GET | `/manage/sponsor-assets/{id}` | `sponsor-assets.view` |
| PUT | `/manage/sponsor-assets/{id}/approve` | `sponsor-assets.approve` |
| PUT | `/manage/sponsor-assets/{id}/reject` | `sponsor-assets.approve` |

### 12.20 Ratings

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/ratings` | `ratings.view-all` |
| GET | `/manage/ratings/{id}` | `ratings.view-all` |
| PUT | `/manage/ratings/{id}/approve` | `ratings.approve` |
| PUT | `/manage/ratings/{id}/reject` | `ratings.reject` |
| DELETE | `/manage/ratings/{id}` | `ratings.delete` |

### 12.21 Support Tickets

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/support-tickets` | `support-tickets.view-all` |
| GET | `/manage/support-tickets/{id}` | `support-tickets.view-all` |
| PUT | `/manage/support-tickets/{id}/assign` | `support-tickets.assign` |
| POST | `/manage/support-tickets/{id}/reply` | `support-tickets.reply` |
| PUT | `/manage/support-tickets/{id}/resolve` | `support-tickets.close` |
| PUT | `/manage/support-tickets/{id}/close` | `support-tickets.close` |
| DELETE | `/manage/support-tickets/{id}` | `support-tickets.delete` |

### 12.22 Invoices

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/invoices` | `invoices.view-all` |
| POST | `/manage/invoices` | `invoices.create` |
| GET | `/manage/invoices/{id}` | `invoices.view-all` |
| PUT | `/manage/invoices/{id}` | `invoices.update` |
| PUT | `/manage/invoices/{id}/issue` | `invoices.issue` |
| PUT | `/manage/invoices/{id}/mark-paid` | `invoices.mark-paid` |
| PUT | `/manage/invoices/{id}/cancel` | `invoices.cancel` |

### 12.23 Pages (CMS)

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/pages` | `pages.view` |
| POST | `/manage/pages` | `pages.create` |
| GET | `/manage/pages/{id}` | `pages.view` |
| PUT | `/manage/pages/{id}` | `pages.update` |
| DELETE | `/manage/pages/{id}` | `pages.delete` |

### 12.24 FAQs

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/faqs` | `faqs.view` |
| POST | `/manage/faqs` | `faqs.create` |
| GET | `/manage/faqs/{id}` | `faqs.view` |
| PUT | `/manage/faqs/{id}` | `faqs.update` |
| DELETE | `/manage/faqs/{id}` | `faqs.delete` |

### 12.25 Banners

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/banners` | `banners.view` |
| POST | `/manage/banners` | `banners.create` |
| GET | `/manage/banners/{id}` | `banners.view` |
| PUT | `/manage/banners/{id}` | `banners.update` |
| DELETE | `/manage/banners/{id}` | `banners.delete` |

### 12.26 Analytics

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/manage/analytics` | `reports.view` |
| GET | `/manage/analytics/views` | `reports.view` |
| GET | `/manage/analytics/actions` | `reports.view` |
| GET | `/manage/analytics/users` | `reports.view` |

---

## 13. Payment Flow (Tap Gateway)

### Flow Overview

The payment uses **direct card processing** via Tap Payment Gateway:

```
Frontend                          Expo API                       Tap API
   │                                │                               │
   │ 1. POST /payments/pay-invoice  │                               │
   │   (card details + invoice_id)  │                               │
   │ ──────────────────────────────►│                               │
   │                                │ 2. POST /v2/tokens/           │
   │                                │   (create card token)         │
   │                                │──────────────────────────────►│
   │                                │◄──────────────────────────────│
   │                                │                               │
   │                                │ 3. POST /v2/charges/          │
   │                                │   (charge with token)         │
   │                                │──────────────────────────────►│
   │                                │◄──────────────────────────────│
   │                                │                               │
   │ 4. Response                    │                               │
   │   (captured OR requires_redirect)                              │
   │◄──────────────────────────────│                               │
   │                                │                               │
   │ [If 3D Secure required]        │                               │
   │ 5. Open transaction_url        │                               │
   │    in browser/webview          │                               │
   │                                │                               │
   │ 6. GET /payments/{id}/status   │                               │
   │◄──────────────────────────────│                               │
```

### Step 1: Pay Invoice

```
POST {EXPO_URL}/payments/pay-invoice
Authorization: Bearer {token}
```

**Request**:
```json
{
  "invoice_id": "uuid",
  "card_number": "4111111111111111",
  "card_holder_name": "AHMED MOHAMMED",
  "exp_month": 12,
  "exp_year": 2025,
  "cvc": "123",
  "first_name": "Ahmed",
  "last_name": "Mohammed",
  "email": "ahmed@example.com",
  "phone_country_code": "966",
  "phone_number": "501234567"
}
```

### Response A: Direct Capture (No 3D Secure)

```json
{
  "success": true,
  "message": "تمت عملية الدفع بنجاح",
  "data": {
    "payment_id": "uuid",
    "payment_number": "PAY-2024-001",
    "charge_id": "chg_xxxx",
    "status": "CAPTURED",
    "amount": 5750.00,
    "currency": "SAR",
    "paid_at": "2024-01-15T10:05:00Z",
    "card_brand": "VISA",
    "card_last_four": "1111",
    "receipt": {
      "gateway_reference": "ref_xxx",
      "payment_method": "CARD"
    }
  }
}
```

### Response B: 3D Secure Required

```json
{
  "success": true,
  "message": "يتطلب التحقق من البطاقة عبر 3D Secure",
  "data": {
    "payment_id": "uuid",
    "payment_number": "PAY-2024-001",
    "charge_id": "chg_xxxx",
    "status": "INITIATED",
    "amount": 5750.00,
    "currency": "SAR",
    "requires_redirect": true,
    "transaction_url": "https://tap.company/3ds/xxx"
  }
}
```

**Frontend Action for 3D Secure**:
1. Open `transaction_url` in a WebView or new browser tab
2. User completes 3D Secure verification
3. After redirect back, call `GET /payments/{id}/status` to get final status

### Step 2: Check Status (After 3D Secure)

```
GET {EXPO_URL}/payments/{payment_id}/status
Authorization: Bearer {token}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "payment_id": "uuid",
    "payment_number": "PAY-2024-001",
    "status": "CAPTURED",
    "amount": 5750.00,
    "paid_at": "2024-01-15T10:07:00Z"
  }
}
```

### Payment Statuses

| Status | Meaning |
|--------|---------|
| `INITIATED` | Payment created, pending processing |
| `PENDING` | Awaiting 3D Secure or processing |
| `CAPTURED` | ✅ Payment successful |
| `FAILED` | ❌ Payment failed |
| `CANCELLED` | ❌ Payment cancelled |
| `REFUNDED` | ↩️ Payment refunded |
| `ABANDONED` | ⏰ User abandoned 3D Secure |

---

## 14. Phone OTP Verification Flow

### Step 1: Send OTP

```
POST {AUTH_URL}/auth/phone/send-otp
Authorization: Bearer {token}
```

```json
{
  "phone": "+966501234567",
  "channel": "sms"
}
```

| Field | Type | Required | Values |
|-------|------|----------|--------|
| `phone` | string | ✅ | E.164 format (+966XXXXXXXXX) |
| `channel` | string | ❌ | `sms` (default) or `whatsapp` |

**Response**:
```json
{
  "success": true,
  "message": "تم إرسال رمز التحقق عبر رسالة نصية",
  "data": { "channel": "sms" }
}
```

**Rate Limiting**: Max 5 attempts per hour per phone number.

### Step 2: Verify OTP

```
POST {AUTH_URL}/auth/phone/verify-otp
Authorization: Bearer {token}
```

```json
{
  "phone": "+966501234567",
  "code": "123456"
}
```

**Success Response**:
```json
{
  "success": true,
  "message": "تم التحقق بنجاح"
}
```

---

## 15. Data Models & Enums

### 15.1 Event Status
| Value | Label |
|-------|-------|
| `draft` | Draft (not visible) |
| `published` | Published (visible) |
| `ended` | Ended |
| `cancelled` | Cancelled |

### 15.2 Space Status
| Value | Label |
|-------|-------|
| `available` | Available for rent |
| `reserved` | Reserved (pending) |
| `rented` | Currently rented |
| `unavailable` | Not available |

### 15.3 Space Type
| Value | Label |
|-------|-------|
| `booth` | Booth |
| `shop` | Shop |
| `office` | Office |
| `hall` | Hall |
| `outdoor` | Outdoor |
| `other` | Other |

### 15.4 Payment System
| Value | Label |
|-------|-------|
| `full` | Full payment |
| `installment` | Installments |
| `daily` | Daily payment |
| `monthly` | Monthly payment |

### 15.5 Rental Duration
| Value | Label |
|-------|-------|
| `daily` | Daily |
| `weekly` | Weekly |
| `monthly` | Monthly |
| `full_event` | Full event duration |

### 15.6 Request Status (Visit & Rental)
| Value | Label |
|-------|-------|
| `pending` | Pending review |
| `approved` | Approved |
| `rejected` | Rejected |
| `cancelled` | Cancelled |
| `completed` | Completed |

### 15.7 Payment Status
| Value | Label |
|-------|-------|
| `pending` | Pending |
| `partial` | Partially paid |
| `paid` | Fully paid |
| `refunded` | Refunded |

### 15.8 Business Type
| Value | Label |
|-------|-------|
| `investor` | Investor (owns spaces) |
| `merchant` | Merchant (rents spaces) |

### 15.9 Profile Status
| Value | Label |
|-------|-------|
| `pending` | Awaiting approval |
| `approved` | Approved |
| `rejected` | Rejected |

### 15.10 Invoice Status
| Value | Label |
|-------|-------|
| `draft` | Draft |
| `issued` | Issued (payable) |
| `paid` | Fully paid |
| `partially_paid` | Partially paid |
| `overdue` | Overdue |
| `cancelled` | Cancelled |
| `refunded` | Refunded |

### 15.11 Contract Status (Rental)
| Value | Label |
|-------|-------|
| `draft` | Draft |
| `pending` | Pending approval |
| `active` | Active |
| `expired` | Expired |
| `cancelled` | Cancelled |
| `terminated` | Terminated early |

### 15.12 Sponsor Status
| Value | Label |
|-------|-------|
| `pending` | Pending approval |
| `approved` | Approved |
| `active` | Active |
| `suspended` | Suspended |
| `inactive` | Inactive |

### 15.13 Sponsor Tier
| Value | Label |
|-------|-------|
| `platinum` | Platinum |
| `gold` | Gold |
| `silver` | Silver |
| `bronze` | Bronze |
| `media_partner` | Media Partner |
| `strategic_partner` | Strategic Partner |

### 15.14 Sponsor Contract Status
| Value | Label |
|-------|-------|
| `draft` | Draft |
| `pending` | Pending approval |
| `active` | Active |
| `completed` | Completed |
| `cancelled` | Cancelled |

### 15.15 Ticket Category
| Value | Label |
|-------|-------|
| `general` | General |
| `technical` | Technical |
| `billing` | Billing |
| `space` | Space |
| `event` | Event |
| `contract` | Contract |
| `complaint` | Complaint |
| `suggestion` | Suggestion |

### 15.16 Ticket Priority
| Value | Label |
|-------|-------|
| `low` | Low |
| `medium` | Medium |
| `high` | High |
| `urgent` | Urgent |

### 15.17 Ticket Status
| Value | Label |
|-------|-------|
| `open` | Open |
| `in_progress` | In Progress |
| `waiting_reply` | Waiting Reply |
| `resolved` | Resolved |
| `closed` | Closed |

### 15.18 Page Type (CMS)
| Value | Label |
|-------|-------|
| `about` | About |
| `terms` | Terms |
| `privacy` | Privacy |
| `faq` | FAQ |
| `contact` | Contact |
| `custom` | Custom |

### 15.19 Rating Type
| Value | Label |
|-------|-------|
| `space` | Space rating |
| `event` | Event rating |
| `investor` | Investor rating |
| `merchant` | Merchant rating |

---

## 16. Permissions System

### How It Works

1. Users have **Roles** (e.g., `merchant`, `investor`, `admin`, `supervisor`, `super-admin`)
2. Roles contain **Permissions** (e.g., `spaces.view`, `rental-requests.create`)
3. The API checks permissions, NOT roles
4. `super-admin` role bypasses ALL permission checks
5. Permissions are returned in the login/me response as an array of strings

### Common Permission Patterns

Permissions follow the format: `{resource}.{action}`

| Pattern | Examples |
|---------|----------|
| `{resource}.view` | View own resources |
| `{resource}.view-all` | View all (admin) |
| `{resource}.create` | Create new |
| `{resource}.update` | Update own |
| `{resource}.delete` | Delete own |
| `{resource}.approve` | Approve/reject |

### Frontend Permission Check

```javascript
// After login, store user permissions
const userPermissions = loginResponse.data.user.permissions;

// Check permission before showing UI elements
function hasPermission(permission) {
  return userPermissions.includes(permission) ||
         loginResponse.data.user.roles.includes('super-admin');
}

// Example: Show "Create Event" button only if user has permission
if (hasPermission('events.create')) {
  showCreateEventButton();
}
```

### Profile Verification Requirement

Some endpoints require a **verified business profile** (status = `approved`). If not verified, the API returns:

```json
{
  "success": false,
  "message": "يجب تفعيل الملف التجاري أولاً",
  "error_code": "profile_required"
}
```

Or if profile exists but pending/rejected:
- `profile_pending` → Profile is under review
- `profile_rejected` → Profile was rejected

---

## 17. Dashboard Settings API

### Get All Settings

```
GET {EXPO_URL}/manage/settings
Authorization: Bearer {token}
Permission: settings.view
```

**Response**:
```json
{
  "success": true,
  "data": {
    "site_name": "Maham Expo",
    "site_name_ar": "معرض مهام",
    "contact_email": "info@mahamexpo.sa",
    "contact_phone": "+966...",
    "support_email": "support@mahamexpo.sa",
    "maintenance_mode": false,
    "allow_registration": true,
    "auto_approve_profiles": false,
    "max_visit_requests_per_day": 10,
    "max_rental_requests_per_merchant": 5,
    "default_currency": "SAR",
    "timezone": "Asia/Riyadh",
    "cors_allowed_origins": "*",
    "cors_supports_credentials": false,
    "cors_max_age": 86400,
    "payment_enabled": true,
    "payment_gateway_mode": "test",
    "payment_default_currency": "SAR",
    "payment_3d_secure": true,
    "sms_enabled": true,
    "sms_default_channel": "sms",
    "sms_max_attempts_per_hour": 5,
    "sms_code_length": 6
  }
}
```

### Update Settings

```
PUT {EXPO_URL}/manage/settings
Authorization: Bearer {token}
Permission: settings.update
```

**Request**:
```json
{
  "settings": {
    "site_name": "Maham Expo",
    "payment_enabled": true,
    "payment_gateway_mode": "live",
    "sms_enabled": true
  }
}
```

> **Note**: Only include the settings you want to change. Other settings remain unchanged.

---

## 18. Error Codes Reference

### Auth Service Error Codes

| Code | HTTP | Description |
|------|------|-------------|
| `invalid_login_credentials` | 401 | Wrong email/phone or password |
| `authentication_required` | 401 | No token provided |
| `token_expired` | 401 | JWT token expired |
| `token_invalid` | 401 | Invalid/malformed token |
| `permission_denied` | 403 | No permission for this action |
| `validation_failed` | 400 | Request validation errors |
| `user_not_found` | 404 | User not found |
| `user_already_exists` | 409 | Email/phone already taken |
| `user_suspended` | 403 | Account suspended |
| `rate_limit_exceeded` | 429 | Too many requests |
| `otp_invalid` | 401 | Wrong OTP code |
| `otp_expired` | 401 | OTP code expired |
| `otp_max_attempts_exceeded` | 429 | Too many OTP attempts |
| `password_reset_token_invalid` | 401 | Invalid reset token |

### Expo Service Error Codes

| Code | HTTP | Description |
|------|------|-------------|
| `authentication_required` | 401 | No/invalid token |
| `permission_denied` | 403 | No permission |
| `profile_not_verified` | 403 | Business profile not verified |
| `profile_required` | 403 | No business profile |
| `profile_pending` | 422 | Profile under review |
| `profile_rejected` | 422 | Profile rejected |
| `validation_failed` | 400 | Validation errors |
| `resource_not_found` | 404 | Resource not found |
| `event_not_found` | 404 | Event not found |
| `space_not_found` | 404 | Space not found |
| `space_not_available` | 422 | Space not available |
| `space_already_rented` | 409 | Space already rented |
| `invoice_not_found` | 404 | Invoice not found |
| `invoice_already_paid` | 409 | Invoice already paid |
| `visit_request_not_found` | 404 | Visit request not found |
| `rental_request_not_found` | 404 | Rental request not found |
| `auth_service_unavailable` | 502 | Auth service down |
| `internal_server_error` | 500 | Server error |

---

## 19. Implementation Checklist

### Frontend Setup

- [ ] **HTTP Client**: Set up Axios/Fetch with base URL configuration
- [ ] **Auth Interceptor**: Auto-attach `Authorization: Bearer {token}` header
- [ ] **Token Refresh**: Auto-refresh on 401 `token_expired` errors
- [ ] **Error Handler**: Global error handler for API errors
- [ ] **Localization**: Set `Accept-Language` header based on user preference
- [ ] **Loading States**: Handle loading/error/success states for all API calls

### Authentication Pages

- [ ] Login page (email/phone + password)
- [ ] Register page (name, email, phone, password)
- [ ] Forgot password page
- [ ] Reset password page (from email link)
- [ ] Email verification flow
- [ ] Phone OTP verification flow
- [ ] Profile page (view/edit)
- [ ] Change password

### Public Pages (Website)

- [ ] Home page with featured events, banners, statistics
- [ ] Events list with filters (category, city, date, price)
- [ ] Event detail page with spaces list
- [ ] Space detail page
- [ ] FAQs page
- [ ] CMS pages (about, terms, privacy, contact)

### User Dashboard

- [ ] Dashboard overview (`/my/dashboard`)
- [ ] Business profile management (create/update/view status)
- [ ] My visit requests (list, create, view)
- [ ] My rental requests (list, create, view) — requires verified profile
- [ ] My invoices (list, view, pay)
- [ ] My payments (list, view, check status)
- [ ] My notifications (list, mark read, preferences)
- [ ] My favorites (list, add, remove)
- [ ] Support tickets (list, create, reply, close)
- [ ] Ratings (create, update, delete)
- [ ] Device registration (push notifications)

### Investor Dashboard (Space Owners)

- [ ] My spaces (CRUD)
- [ ] Received visit requests (list, approve/reject)
- [ ] Received rental requests (list, approve/reject)
- [ ] My payments/revenue (list, summary)
- [ ] My rental contracts (list, view, sign)
- [ ] Activity history

### Sponsor Dashboard

- [ ] My sponsor contracts (list, view)
- [ ] My sponsor payments (list, view)
- [ ] My sponsor assets (CRUD — upload logos, banners, etc.)
- [ ] My exposure/ROI data (list, summary)

### Admin Dashboard

- [ ] Admin dashboard overview (`/manage/dashboard`)
- [ ] Events management (CRUD, sections, spaces, packages)
- [ ] Categories management (CRUD)
- [ ] Cities management (CRUD)
- [ ] Services management (CRUD)
- [ ] Users management (list, approve/reject/suspend)
- [ ] Business profiles management (list, approve/reject)
- [ ] Visit requests management (list, approve/reject)
- [ ] Rental requests management (list, approve/reject, record payment)
- [ ] Rental contracts management (CRUD, approve/reject/terminate)
- [ ] Sponsors management (CRUD, approve/activate/suspend)
- [ ] Sponsor packages management (CRUD per event)
- [ ] Sponsor contracts management (CRUD, approve/reject/complete)
- [ ] Sponsor payments management (CRUD, mark paid)
- [ ] Sponsor benefits management (CRUD, mark delivered)
- [ ] Sponsor assets management (list, approve/reject)
- [ ] Invoices management (CRUD, issue, mark paid, cancel)
- [ ] Ratings management (list, approve/reject, delete)
- [ ] Support tickets management (list, assign, reply, resolve, close)
- [ ] CMS Pages management (CRUD)
- [ ] FAQs management (CRUD)
- [ ] Banners management (CRUD)
- [ ] System settings
- [ ] Analytics (views, actions, users)
- [ ] Statistics

### Super Admin (Auth Service)

- [ ] Users CRUD
- [ ] Roles CRUD with permission assignment
- [ ] Permissions CRUD
- [ ] Services management (microservices)

---

## 📝 Quick Start Code Example

### JavaScript/TypeScript API Client Setup

```typescript
import axios from 'axios';

const AUTH_URL = 'https://auth-service-api.mahamexpo.sa/api/v1';
const EXPO_URL = 'https://expo-service-api.mahamexpo.sa/api/v1';

// Create API instances
const authApi = axios.create({ baseURL: AUTH_URL });
const expoApi = axios.create({ baseURL: EXPO_URL });

// Token management
let accessToken = localStorage.getItem('token');

// Add auth header to all requests
[authApi, expoApi].forEach(api => {
  api.interceptors.request.use(config => {
    if (accessToken) {
      config.headers.Authorization = `Bearer ${accessToken}`;
    }
    config.headers['Accept-Language'] = 'ar'; // or 'en'
    config.headers['Accept'] = 'application/json';
    return config;
  });
});

// Auto-refresh token on 401
authApi.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401 &&
        error.response?.data?.error_code === 'token_expired' &&
        !error.config._retry) {
      error.config._retry = true;
      try {
        const { data } = await authApi.post('/auth/refresh');
        accessToken = data.data.token;
        localStorage.setItem('token', accessToken);
        error.config.headers.Authorization = `Bearer ${accessToken}`;
        return authApi.request(error.config);
      } catch (refreshError) {
        localStorage.removeItem('token');
        window.location.href = '/login';
        return Promise.reject(refreshError);
      }
    }
    return Promise.reject(error);
  }
);

// Copy the same interceptor for expoApi
expoApi.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401 &&
        error.response?.data?.error_code === 'token_expired' &&
        !error.config._retry) {
      error.config._retry = true;
      try {
        const { data } = await authApi.post('/auth/refresh');
        accessToken = data.data.token;
        localStorage.setItem('token', accessToken);
        error.config.headers.Authorization = `Bearer ${accessToken}`;
        return expoApi.request(error.config);
      } catch (refreshError) {
        localStorage.removeItem('token');
        window.location.href = '/login';
        return Promise.reject(refreshError);
      }
    }
    return Promise.reject(error);
  }
);

// ─── Usage Examples ──────────────────────────────────────

// Login
async function login(identifier: string, password: string) {
  const { data } = await authApi.post('/auth/login', { identifier, password });
  accessToken = data.data.token;
  localStorage.setItem('token', accessToken);
  localStorage.setItem('user', JSON.stringify(data.data.user));
  return data.data;
}

// Get events
async function getEvents(filters?: Record<string, any>) {
  const { data } = await expoApi.get('/events', { params: filters });
  return data; // { success, data: [...], pagination: {...} }
}

// Create visit request
async function createVisitRequest(payload: {
  event_id: string;
  visit_date: string;
  visit_time: string;
  visitors_count: number;
  notes?: string;
  contact_phone: string;
}) {
  const { data } = await expoApi.post('/visit-requests', payload);
  return data;
}

// Pay invoice
async function payInvoice(payload: {
  invoice_id: string;
  card_number: string;
  card_holder_name: string;
  exp_month: number;
  exp_year: number;
  cvc: string;
}) {
  const { data } = await expoApi.post('/payments/pay-invoice', payload);

  if (data.data.requires_redirect) {
    // Open 3D Secure URL
    window.open(data.data.transaction_url, '_blank');
    // Then poll /payments/{id}/status
  }

  return data;
}

// Check permission
function hasPermission(permission: string): boolean {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  if (user.roles?.includes('super-admin')) return true;
  return user.permissions?.includes(permission) || false;
}
```

---

## 🔒 Security Notes

1. **Never store secret API keys in frontend** — all payment processing happens server-side
2. **Card numbers** are sent directly to the backend which tokenizes them via Tap — they are never stored
3. **JWT tokens** should be stored in `localStorage` or `httpOnly cookies`
4. **CORS** is configured server-side — the API accepts requests from configured origins
5. **Rate limiting**: Login is throttled to 10 requests/minute, OTP to 5/hour per phone
6. All IDs are **UUIDs** — never sequential integers
7. **File uploads** use `multipart/form-data` content type

---

> **Last Updated**: Based on commit `484f05e` — includes payment gateway, OTP, and dashboard settings features.
