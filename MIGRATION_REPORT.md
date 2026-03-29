# MAHAM EXPO — Laravel Unification Report
# تقرير توحيد الباكند على Laravel

## Executive Summary

Laravel backend (`maham-expo-laravel`) is now the **single source of truth** for the entire Maham Expo platform.

## 1. Backend Unification Report

### Auth Service (maham-auth-expo-api)
| Component | Count |
|-----------|-------|
| Models | 7 |
| Controllers | 12 |
| Migrations | 8 |
| Services | 5 |
| Tests | 5 |

### Expo Service (expo-api)
| Component | Count |
|-----------|-------|
| Models | 37 |
| Controllers | 92 |
| Migrations | 46 |
| Services | 9 |
| Jobs | 3 |
| Events | 5 |
| Listeners | 3 |
| Policies | 3 |
| Notifications | 1 |
| Form Requests | 16 |
| Enums | 25 |
| Tests | 10 (89 test cases) |

**Total: 44 Models, 104 Controllers, 54 Migrations**

## 2. Auth Authority Report

Auth microservice is the **PRIMARY AUTHORITY** for:
- ✅ User identity (register, login, OTP)
- ✅ JWT token issuing + refresh
- ✅ Role-based access (RBAC)
- ✅ Permission-based access (dynamic)
- ✅ Service-to-service verification
- ✅ User categories (admin, investor, merchant, sponsor)

## 3. API Migration Map

### Public Endpoints (No Auth)
| Endpoint | Method | Status |
|----------|--------|--------|
| /api/v1/auth/register | POST | ✅ |
| /api/v1/auth/login | POST | ✅ |
| /api/v1/auth/otp/send | POST | ✅ |
| /api/v1/auth/otp/verify | POST | ✅ |
| /api/v1/events | GET | ✅ |
| /api/v1/events/{id} | GET | ✅ |
| /api/v1/categories | GET | ✅ |
| /api/v1/cities | GET | ✅ |
| /api/v1/statistics | GET | ✅ |
| /api/v1/banners | GET | ✅ |
| /api/v1/faqs | GET | ✅ |
| /api/v1/pages/{slug} | GET | ✅ |

### Merchant Endpoints
| Endpoint | Status |
|----------|--------|
| /api/v1/visit-requests | ✅ |
| /api/v1/rental-requests | ✅ |
| /api/v1/invoices | ✅ |
| /api/v1/support-tickets | ✅ |
| /api/v1/my/dashboard | ✅ |

### Investor Endpoints
| Endpoint | Status |
|----------|--------|
| /api/v1/my/spaces | ✅ |
| /api/v1/my/received-visit-requests | ✅ |
| /api/v1/my/received-rental-requests | ✅ |
| /api/v1/my/payments | ✅ |
| /api/v1/my/rental-contracts | ✅ |

### Sponsor Endpoints
| Endpoint | Status |
|----------|--------|
| /api/v1/my/sponsor-contracts | ✅ |
| /api/v1/my/sponsor-payments | ✅ |
| /api/v1/my/sponsor-assets | ✅ |
| /api/v1/my/sponsor-exposure | ✅ |

### Admin Endpoints (30+)
| Endpoint | Status |
|----------|--------|
| /api/v1/manage/events | ✅ |
| /api/v1/manage/spaces | ✅ |
| /api/v1/manage/rental-requests | ✅ |
| /api/v1/manage/rental-contracts | ✅ |
| /api/v1/manage/invoices | ✅ |
| /api/v1/manage/sponsors | ✅ |
| /api/v1/manage/sponsor-packages | ✅ |
| /api/v1/manage/users | ✅ |
| /api/v1/manage/dashboard | ✅ |
| /api/v1/manage/analytics | ✅ |

## 4. Database Schema Summary

### Core Tables: 8 (auth)
users, roles, permissions, role_permissions, user_roles, refresh_tokens, audit_logs, services

### Business Tables: 46 (expo)
events, sections, spaces, categories, cities, services, business_profiles,
visit_requests, rental_requests, rental_contracts, invoices, payments,
sponsors, sponsor_packages, sponsor_contracts, sponsor_payments,
sponsor_assets, sponsor_benefits, sponsor_deliverables, sponsor_leads,
sponsor_exposure_tracking, notifications, notification_preferences,
support_tickets, ticket_replies, ratings, favorites, banners, faqs, pages,
team_members, user_activities, page_views, one_signal_subscriptions,
business_activity_types, member_types

**Total: 54 tables in unified schema**

## 5. AI Migration Report

| Component | Status |
|-----------|--------|
| AiService.php | ✅ Created — central AI brain |
| recommendEvents() | ✅ Smart recommendations |
| scoreLead() | ✅ CRM lead scoring (0-100) |
| generateAlerts() | ✅ Smart alerts |
| predictRevenue() | ✅ Revenue forecasting |
| chat() | ✅ AI assistant with Arabic fallback |
| AnalyzeLeadJob | ✅ Queued |
| GenerateSmartAlertsJob | ✅ Queued |
| PredictRevenueJob | ✅ Queued |

## 6. Testing Coverage Report

| Test File | Cases | Type |
|-----------|-------|------|
| AuthTest | 10 | Feature |
| EventTest | 8 | Feature |
| BookingTest | 4 | Feature |
| SponsorTest | 4 | Feature |
| PublicApiTest | 15 | Feature |
| AuthenticatedApiTest | 16 | Feature |
| AdminApiTest | 31 | Feature |
| ExampleTest | 1 | Feature |
| **Total** | **89** | **Feature** |

## 7. Frontend/Mobile Integration Status

| System | API Source | Status |
|--------|-----------|--------|
| Admin Panel (Next.js) | Laravel API | ✅ Connected |
| Sponsor Portal | Laravel via tRPC proxy | ⚠️ Needs direct REST |
| Investor Portal | Laravel via tRPC proxy | ⚠️ Needs direct REST |
| Merchant Portal | Laravel via tRPC proxy | ⚠️ Needs direct REST |
| Mobile App (Flutter) | Laravel REST API | ✅ Connected |

## 8. Remaining Blockers

| Item | Status | Action |
|------|--------|--------|
| Firebase config files | ❌ Missing | Owner must create Firebase project |
| Tap Payments live key | ⚠️ Test key present | Owner must get sk_live_ |
| Twilio/Msegat OTP | ⚠️ Twilio test | Owner must configure production |
| VPS deployment | ❌ Needs SSH | Owner must deploy Docker |
| DNS A records | ❌ Not configured | Owner must set in CloudFlare |
| SSL certificates | ❌ Not installed | certbot --nginx |
| Apple Developer | ❌ Needs account | Owner must sign iOS app |
| Android keystore | ❌ Needs generation | keytool -genkey |
| Web portals → REST migration | ⚠️ Still on tRPC | Phase 2 |

## Saudi Compliance Status

| Item | Status |
|------|--------|
| VAT 15% | ✅ Auto-calculated on invoices |
| ZATCA UUID | ✅ Auto-generated |
| SAR Currency | ✅ Default |
| Arabic RTL | ✅ Supported |
| Phone +966 | ✅ Validated |
| National ID | ✅ Validated |
| Timezone | ✅ Asia/Riyadh |
