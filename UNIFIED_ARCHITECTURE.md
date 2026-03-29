# MAHAM EXPO — Unified Laravel Backend Architecture
# بنية الباكند الموحد — مهام إكسبو

## 1. Architecture Blueprint

### Current State (Microservices)
```
┌─────────────────────┐     ┌─────────────────────┐
│ Auth Service         │     │ Expo Service         │
│ Port: 8001           │     │ Port: 8002           │
│ 7 Models             │     │ 37 Models            │
│ 8 Migrations         │     │ 46 Migrations        │
│ 12 Controllers       │     │ 83 Controllers       │
│ DB: auth_db          │     │ DB: expo_db          │
└─────────────────────┘     └─────────────────────┘
```

### Target State (Unified Monolith)
```
┌─────────────────────────────────────────────────────┐
│               Maham Expo API (Laravel)               │
│               Port: 8000                             │
│               DB: maham_expo (single)                │
│                                                       │
│  Domains:                                             │
│  ├── Auth (register, login, OTP, JWT, RBAC)          │
│  ├── Exhibitions (events, sections, spaces, venues)  │
│  ├── Bookings (visit/rental requests, approvals)     │
│  ├── Sponsorships (packages, contracts, assets)      │
│  ├── Finance (payments, invoices, ZATCA)             │
│  ├── Users (profiles, KYC, teams)                    │
│  ├── Notifications (push, in-app, preferences)      │
│  ├── Content (pages, FAQ, banners)                   │
│  └── Admin (dashboard, analytics, settings)          │
│                                                       │
│  Middleware Stack:                                     │
│  ├── SetLocale (ar/en)                               │
│  ├── auth:api (JWT via Passport/Sanctum)             │
│  ├── CheckPermission (dynamic RBAC)                  │
│  └── ThrottleRequests                                │
└─────────────────────────────────────────────────────┘
         │              │              │
    ┌────▼────┐   ┌─────▼─────┐  ┌────▼────┐
    │ Web     │   │ Mobile    │  │ Admin   │
    │ Portals │   │ App       │  │ Panel   │
    │ (React) │   │ (Flutter) │  │ (Next)  │
    └─────────┘   └───────────┘  └─────────┘
```

### Merging Strategy

The two Laravel apps share similar structure. To merge:

1. **Keep Expo Service as the base** (larger codebase, 37 models)
2. **Move Auth controllers/models INTO Expo** under `Auth` domain
3. **Eliminate internal HTTP calls** — replace with direct class calls
4. **Unify database** — single MySQL database `maham_expo`

### Service Provider Registration
```php
// app/Providers/AuthDomainServiceProvider.php
// app/Providers/ExhibitionDomainServiceProvider.php
// app/Providers/SponsorshipDomainServiceProvider.php
// app/Providers/FinanceDomainServiceProvider.php
```

## 2. Database Unification Plan

### Single Database: `maham_expo`

#### Core Tables (from Auth Service)
| Table | Source | Purpose |
|-------|--------|---------|
| users | auth | User accounts |
| roles | auth | Role definitions |
| permissions | auth | Permission definitions |
| role_permissions | auth | Role-permission pivot |
| user_roles | auth | User-role pivot |
| refresh_tokens | auth | JWT refresh tokens |
| audit_logs | auth | System audit trail |
| services | auth | Registered services |

#### Exhibition Tables (from Expo Service)
| Table | Source | Purpose |
|-------|--------|---------|
| events | expo | Exhibitions/conferences |
| sections | expo | Event zones/sections |
| spaces | expo | Booths/units/spaces |
| categories | expo | Event categories |
| cities | expo | Saudi cities |
| services | expo | Available services |

#### Business Tables
| Table | Source | Purpose |
|-------|--------|---------|
| business_profiles | expo | Merchant/investor profiles |
| visit_requests | expo | Visit booking requests |
| rental_requests | expo | Rental booking requests |
| rental_contracts | expo | Signed rental contracts |
| invoices | expo | ZATCA-compliant invoices |
| payments | expo | Tap payment records |
| team_members | expo | Organization team |

#### Sponsorship Tables
| Table | Source | Purpose |
|-------|--------|---------|
| sponsors | expo | Sponsor entities |
| sponsor_packages | expo | Sponsorship tiers |
| sponsor_contracts | expo | Sponsor agreements |
| sponsor_payments | expo | Sponsor payments |
| sponsor_assets | expo | Brand assets (logos etc) |
| sponsor_benefits | expo | Package benefits |
| sponsor_deliverables | expo | Deliverable tracking |
| sponsor_leads | expo | Lead generation |
| sponsor_exposure_tracking | expo | Exposure analytics |

#### Communication Tables
| Table | Source | Purpose |
|-------|--------|---------|
| notifications | expo | In-app notifications |
| notification_preferences | expo | User preferences |
| support_tickets | expo | Support system |
| ticket_replies | expo | Ticket responses |

#### Content Tables
| Table | Source | Purpose |
|-------|--------|---------|
| pages | expo | CMS pages |
| faqs | expo | FAQ entries |
| banners | expo | Banner ads |
| ratings | expo | User ratings |
| favorites | expo | User favorites |

### Migration Order
1. Core (users, roles, permissions) — from Auth
2. Categories, Cities, Services — from Expo
3. Events, Sections, Spaces — from Expo
4. Business profiles, Teams — from Expo
5. Bookings (visit/rental requests) — from Expo
6. Contracts, Invoices, Payments — from Expo
7. Sponsorship tables — from Expo
8. Communication tables — from Expo
9. Content tables — from Expo
10. Analytics, tracking — from Expo

## 3. Auth & Authorization

### Public Routes (No Auth)
```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/refresh
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
POST /api/v1/auth/otp/send
POST /api/v1/auth/otp/verify
POST /api/v1/auth/otp/complete-registration
GET  /api/v1/events (public listing)
GET  /api/v1/events/{id} (public detail)
GET  /api/v1/categories
GET  /api/v1/cities
GET  /api/v1/statistics
GET  /api/v1/banners
GET  /api/v1/faqs
GET  /api/v1/pages/{slug}
```

### Authenticated Routes (JWT Required)
```
GET  /api/v1/auth/me
POST /api/v1/auth/logout
GET  /api/v1/my/dashboard
GET  /api/v1/profile
PUT  /api/v1/profile
GET  /api/v1/notifications
GET  /api/v1/favorites
GET  /api/v1/invoices
GET  /api/v1/support-tickets
```

### Role-Specific Routes
```
# Investor (/api/v1/my/spaces, /api/v1/my/received-*)
# Merchant (/api/v1/visit-requests, /api/v1/rental-requests)
# Sponsor (/api/v1/my/sponsor-*)
# Admin (/api/v1/manage/*)
# SuperAdmin (/api/v1/manage/categories, cities, users, settings)
```

### Internal Auth Facade
```php
// Instead of HTTP calls to /service/verify-token
use App\Services\AuthFacade;

AuthFacade::verifyToken($token);     // Returns User or null
AuthFacade::checkPermission($user, 'events.create'); // Returns bool
AuthFacade::getUserInfo($userId);    // Returns User with roles
```

## 4. API Response Standard

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation completed",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

Error:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## 5. Role-Based Workflows

### Merchant Journey
1. Register → OTP verify → Complete profile
2. Browse events → View spaces → Submit visit request
3. Visit approved → Submit rental request
4. Rental approved → Invoice generated → Pay via Tap
5. Contract created → E-sign → Booking confirmed
6. Track bookings, payments, notifications

### Investor Journey
1. Register → KYC verification → Profile approved
2. Create event → Add sections → Add spaces
3. Set pricing → Publish event
4. Receive visit/rental requests → Approve/Reject
5. Monitor payments → Track revenue → View analytics

### Sponsor Journey
1. Register → Browse events → View packages
2. Select package → Submit sponsorship request
3. Admin/Investor approves → Contract created
4. Pay → Upload brand assets → Track deliverables
5. Monitor exposure analytics → ROI reports

### Admin Journey
1. Login → CEO Dashboard → View KPIs
2. Manage events/spaces/packages
3. Approve KYC/profiles/requests
4. Generate invoices → Track payments
5. Manage content (pages, FAQ, banners)
6. Monitor system (audit logs, analytics)

## 6. Frontend Integration Guide

### Web Portals (React)
```javascript
// Change from:
const API_BASE = "http://localhost:3000/api/trpc";

// To:
const API_BASE = "https://api.mahamexpo.sa/api/v1";
// Auth: "https://auth-service-api.mahamexpo.sa/api/v1"
// Expo: "https://expo-service-api.mahamexpo.sa/api/v1"
```

### Mobile App (Flutter)
```dart
// Already configured in api_urls.dart:
// authBaseUrl → auth-service-api.mahamexpo.sa/api/v1
// expoBaseUrl → expo-service-api.mahamexpo.sa/api/v1
```

### Token Flow
1. Client calls `POST /api/v1/auth/otp/send` with phone
2. User receives SMS OTP
3. Client calls `POST /api/v1/auth/otp/verify` with phone + code
4. Server returns `{ access_token, refresh_token, user }`
5. Client stores tokens securely
6. All subsequent requests include `Authorization: Bearer {token}`
7. On 401 → Client calls `POST /api/v1/auth/refresh`

## 7. Migration Strategy

### Phase 1: Database Consolidation
- Export both databases
- Create unified migration set
- Run migrations on single `maham_expo` database
- Import data with ID mapping

### Phase 2: Codebase Merge
- Copy Auth controllers/models into Expo app
- Remove internal HTTP calls
- Update route files
- Test all endpoints

### Phase 3: Frontend Updates
- Update API URLs in all portals
- Test auth flow
- Test data flows

### Phase 4: Deployment
- Docker single-service deployment
- Nginx reverse proxy
- SSL certificates

## 8. Testing Plan

### Unit Tests (PHPUnit)
- AuthController: register, login, OTP, refresh, logout
- EventController: CRUD, filtering, pagination
- BookingController: create, approve, reject
- PaymentController: charge, verify, webhook

### Integration Tests
- Merchant booking flow (end-to-end)
- Sponsor contract flow
- Admin approval flow
- Invoice + payment flow

### Manual QA
- Saudi phone OTP
- Tap payment sandbox
- Arabic RTL layout
- PDF invoice generation

## 9. Saudi Compliance

- **VAT 15%**: Applied on all invoices
- **ZATCA Phase 2**: QR code + UUID + hash on invoices
- **Currency**: SAR (Saudi Riyal)
- **Phone**: +966 format, OTP via Twilio/Msegat
- **Language**: Arabic-first, English secondary
- **Timezone**: Asia/Riyadh
- **National ID**: 10-digit validation (starts with 1-3)

## 10. Deployment

### Docker (Current)
```yaml
services:
  auth-service:    # Port 8001
  expo-api:        # Port 8002
  dashboard:       # Port 3000
  auth-mysql:      # Port 3306
  expo-mysql:      # Port 3307
  auth-redis:      # Port 6379
  expo-redis:      # Port 6380
  nginx:           # Port 80/443
```

### .env Template
```
APP_URL=https://api.mahamexpo.sa
DB_HOST=mysql
DB_DATABASE=maham_expo
DB_USERNAME=maham_user
DB_PASSWORD=secure_password
JWT_SECRET=generated_secret
TAP_SECRET_KEY=sk_live_xxx
TWILIO_ACCOUNT_SID=xxx
TWILIO_VERIFY_SERVICE_SID=xxx
```
