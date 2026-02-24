# خطة تنفيذ نظام الرعاة (Sponsor System) - Maham Expo

## نظرة عامة
بناء نظام رعاة متكامل داخل Maham Expo يشمل: باقات رعاية، عقود، مدفوعات، تتبع ظهور، لوحة تحكم خاصة بالراعي، مع role جديد في Auth Service.

---

## المرحلة 1: البنية التحتية (Enums + Migrations + Models + Config)

### 1.1 — إنشاء 8 Enums جديدة
**المسار:** `apps/expo-api/app/Enums/`

| الملف | القيم |
|--------|--------|
| `SponsorStatus.php` | pending, approved, active, suspended, inactive |
| `SponsorTier.php` | platinum, gold, silver, bronze, media_partner, strategic_partner |
| `SponsorContractStatus.php` | draft, pending, active, completed, cancelled |
| `SponsorPaymentStatus.php` | pending, paid, overdue, cancelled |
| `SponsorBenefitType.php` | screen, banner, booth, vip_invitation, logo, notification, email, custom |
| `SponsorBenefitStatus.php` | pending, in_progress, delivered, cancelled |
| `SponsorAssetType.php` | logo, banner, booth_design, video, document |
| `ExposureChannel.php` | app, website, screen, ticket, email, push_notification, social_media |

> كل enum يتبع النمط الموجود: `label()` (عربي) + `labelEn()` (إنجليزي) + `values()`

### 1.2 — إنشاء 7 Migrations
**المسار:** `apps/expo-api/database/migrations/`

#### جدول `sponsors`
```
uuid id (PK)
foreignUuid event_id → events (cascadeOnDelete)
string user_id (nullable) — مالك الحساب في Auth Service
string name, name_ar
string company_name, company_name_ar (nullable)
text description, description_ar (nullable)
string logo (nullable)
string contact_person (nullable)
string contact_email (nullable)
string contact_phone (nullable)
string website (nullable)
enum status [pending, approved, active, suspended, inactive] default pending
string created_by (nullable)
string created_from (nullable) — web/mobile/api
timestamps + softDeletes
indexes: event_id, user_id, status
```

#### جدول `sponsor_packages`
```
uuid id (PK)
foreignUuid event_id → events (cascadeOnDelete)
string name, name_ar
text description, description_ar (nullable)
enum tier [platinum, gold, silver, bronze, media_partner, strategic_partner]
decimal price (12,2)
integer max_sponsors (nullable) — عدد أقصى للرعاة بهذا المستوى
json benefits (nullable) — مصفوفة وصف المزايا
integer display_screens_count default 0
integer banners_count default 0
integer vip_invitations_count default 0
decimal booth_area_sqm (8,2) nullable
json logo_placement (nullable) — مواقع ظهور اللوقو
boolean is_active default true
integer sort_order default 0
timestamps + softDeletes
indexes: event_id, tier, is_active
```

#### جدول `sponsor_contracts`
```
uuid id (PK)
foreignUuid sponsor_id → sponsors (cascadeOnDelete)
foreignUuid sponsor_package_id → sponsor_packages (cascadeOnDelete)
foreignUuid event_id → events (cascadeOnDelete)
string contract_number (unique) — SC-YYYYMMDD-00001
date start_date
date end_date
decimal total_amount (12,2)
decimal paid_amount (12,2) default 0
enum payment_status [pending, partial, paid, refunded] default pending
enum status [draft, pending, active, completed, cancelled] default draft
text terms, terms_ar (nullable)
datetime signed_at (nullable)
string signed_by (nullable)
string reviewed_by (nullable)
datetime reviewed_at (nullable)
text rejection_reason (nullable)
text admin_notes (nullable)
text notes (nullable)
timestamps + softDeletes
indexes: sponsor_id, event_id, status, contract_number, payment_status
```

#### جدول `sponsor_payments`
```
uuid id (PK)
foreignUuid sponsor_contract_id → sponsor_contracts (cascadeOnDelete)
string payment_number — SP-YYYYMMDD-00001
decimal amount (12,2)
date due_date
datetime paid_at (nullable)
string payment_method (nullable)
string transaction_reference (nullable)
enum status [pending, paid, overdue, cancelled] default pending
text notes (nullable)
timestamps
indexes: sponsor_contract_id, status, due_date
```

#### جدول `sponsor_benefits`
```
uuid id (PK)
foreignUuid sponsor_contract_id → sponsor_contracts (cascadeOnDelete)
enum benefit_type [screen, banner, booth, vip_invitation, logo, notification, email, custom]
string description, description_ar (nullable)
integer quantity default 1
integer delivered_quantity default 0
enum status [pending, in_progress, delivered, cancelled] default pending
text delivery_notes (nullable)
timestamps
indexes: sponsor_contract_id, benefit_type, status
```

#### جدول `sponsor_assets`
```
uuid id (PK)
foreignUuid sponsor_id → sponsors (cascadeOnDelete)
foreignUuid event_id → events (nullable, cascadeOnDelete)
enum type [logo, banner, booth_design, video, document]
string file_path
string file_name
integer file_size (nullable)
string mime_type (nullable)
boolean is_approved default false
string approved_by (nullable)
datetime approved_at (nullable)
text rejection_reason (nullable)
integer sort_order default 0
timestamps + softDeletes
indexes: sponsor_id, event_id, type, is_approved
```

#### جدول `sponsor_exposure_tracking`
```
uuid id (PK)
foreignUuid sponsor_id → sponsors (cascadeOnDelete)
foreignUuid event_id → events (cascadeOnDelete)
foreignUuid sponsor_contract_id → sponsor_contracts (nullable, cascadeOnDelete)
enum channel [app, website, screen, ticket, email, push_notification, social_media]
integer impressions_count default 0
integer clicks_count default 0
date date
json metadata (nullable)
timestamps
indexes: sponsor_id, event_id, channel, date
unique: [sponsor_id, event_id, channel, date]
```

### 1.3 — إنشاء 7 Models
**المسار:** `apps/expo-api/app/Models/`

| Model | Traits | Relations | Key Scopes |
|-------|--------|-----------|------------|
| `Sponsor` | HasFactory, HasUuids, SoftDeletes | belongsTo(Event), hasMany(Contract, Asset, ExposureTracking) | active(), forEvent(), forUser(), approved(), pending() |
| `SponsorPackage` | HasFactory, HasUuids, SoftDeletes | belongsTo(Event), hasMany(Contract) | active(), forEvent(), ofTier(), ordered() |
| `SponsorContract` | HasFactory, HasUuids, SoftDeletes | belongsTo(Sponsor, Package, Event), hasMany(Payment, Benefit) | active(), forSponsor(), forEvent(), pending() + approve/reject/complete methods |
| `SponsorPayment` | HasFactory, HasUuids | belongsTo(Contract) | pending(), paid(), overdue(), forContract() |
| `SponsorBenefit` | HasFactory, HasUuids | belongsTo(Contract) | pending(), delivered(), ofType(), forContract() |
| `SponsorAsset` | HasFactory, HasUuids, SoftDeletes | belongsTo(Sponsor, Event) | approved(), pending(), ofType(), forSponsor() |
| `SponsorExposureTracking` | HasFactory, HasUuids | belongsTo(Sponsor, Event, Contract) | forSponsor(), forEvent(), forChannel(), inDateRange() |

### 1.4 — تحديث Config
**الملف:** `apps/expo-api/config/expo-api.php`

إضافة:
```php
'roles' => [
    // ... existing
    'sponsor' => 'راعي',
],

'request_prefixes' => [
    // ... existing
    'sponsor_contract' => 'SC',
    'sponsor_payment' => 'SP',
],

'uploads' => [
    'paths' => [
        // ... existing
        'sponsors' => 'uploads/sponsors',
        'sponsor_assets' => 'uploads/sponsor-assets',
    ],
],

'cache' => [
    'ttl' => [
        // ... existing
        'sponsors' => 1800,
        'sponsor_packages' => 3600,
    ],
],

'sponsor' => [
    'max_assets_per_sponsor' => 20,
    'max_asset_size' => 10240, // 10MB
    'allowed_asset_types' => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4'],
],
```

### 1.5 — تحديث ApiErrorCode
**الملف:** `apps/expo-api/app/Support/ApiErrorCode.php`

إضافة أكواد خطأ جديدة:
```php
// SPONSOR ERRORS
SPONSOR_NOT_FOUND
SPONSOR_NOT_ACTIVE
SPONSOR_ALREADY_EXISTS

// SPONSOR PACKAGE ERRORS
SPONSOR_PACKAGE_NOT_FOUND
SPONSOR_PACKAGE_NOT_AVAILABLE
SPONSOR_PACKAGE_FULL — وصل الحد الأقصى

// SPONSOR CONTRACT ERRORS
SPONSOR_CONTRACT_NOT_FOUND
SPONSOR_CONTRACT_CANNOT_BE_MODIFIED
SPONSOR_CONTRACT_ALREADY_ACTIVE

// SPONSOR PAYMENT ERRORS
SPONSOR_PAYMENT_NOT_FOUND
SPONSOR_PAYMENT_ALREADY_PAID

// SPONSOR ASSET ERRORS
SPONSOR_ASSET_NOT_FOUND
SPONSOR_ASSET_NOT_APPROVED
SPONSOR_ASSET_UPLOAD_FAILED
```

### 1.6 — تحديث ملفات اللغة
**الملفات:** `apps/expo-api/lang/en/messages.php` + `lang/ar/messages.php`

إضافة ~40 رسالة للرعاة (إنشاء، تحديث، حذف، موافقة، رفض، عقود، مدفوعات، أصول).

---

## المرحلة 2: Controllers الأدمن (إدارة الرعاة)

### 2.1 — Admin Controllers (6 ملفات)
**المسار:** `apps/expo-api/app/Http/Controllers/Api/Admin/`

| Controller | Endpoints | الوصف |
|-----------|-----------|--------|
| `SponsorController` | index, store, show, update, destroy | CRUD كامل للرعاة مع فلترة وبحث |
| `SponsorPackageController` | index, store, show, update, destroy | إدارة باقات الرعاية لكل فعالية |
| `SponsorContractController` | index, store, show, update, approve, reject, complete | إدارة العقود مع workflow الموافقة |
| `SponsorPaymentController` | index, store, show, update | تسجيل وتتبع المدفوعات |
| `SponsorBenefitController` | index, store, show, update, markDelivered | تتبع تسليم المزايا |
| `SponsorAssetController` | index, show, approve, reject | الموافقة على المواد الإعلانية |

### 2.2 — Supervisor Controllers
**المسار:** `apps/expo-api/app/Http/Controllers/Api/Supervisor/`

المشرف يستخدم نفس Admin Controllers لكن read-only:
- عرض الرعاة والباقات والعقود (بدون إنشاء/تعديل/حذف)
- الموافقة/الرفض على العقود

### 2.3 — Routes (Admin + Supervisor)
**الملف:** `apps/expo-api/routes/api/v1.php`

```
admin/sponsors                    GET, POST
admin/sponsors/{sponsor}          GET, PUT, DELETE
admin/events/{event}/sponsor-packages   GET, POST
admin/sponsor-packages/{package}  GET, PUT, DELETE
admin/sponsor-contracts           GET, POST
admin/sponsor-contracts/{contract} GET, PUT
admin/sponsor-contracts/{contract}/approve  PUT
admin/sponsor-contracts/{contract}/reject   PUT
admin/sponsor-contracts/{contract}/complete PUT
admin/sponsor-payments            GET, POST
admin/sponsor-payments/{payment}  GET, PUT
admin/sponsor-benefits            GET, POST
admin/sponsor-benefits/{benefit}  GET, PUT
admin/sponsor-benefits/{benefit}/deliver    PUT
admin/sponsor-assets              GET
admin/sponsor-assets/{asset}      GET
admin/sponsor-assets/{asset}/approve  PUT
admin/sponsor-assets/{asset}/reject   PUT

supervisor/sponsors               GET
supervisor/sponsors/{sponsor}     GET
supervisor/sponsor-contracts      GET
supervisor/sponsor-contracts/{contract} GET
supervisor/sponsor-contracts/{contract}/approve PUT
supervisor/sponsor-contracts/{contract}/reject  PUT
```

---

## المرحلة 3: Sponsor Role + Controllers (لوحة تحكم الراعي)

### 3.1 — Sponsor Controllers (6 ملفات)
**المسار:** `apps/expo-api/app/Http/Controllers/Api/Sponsor/`

| Controller | Endpoints | الوصف |
|-----------|-----------|--------|
| `DashboardController` | index | لوحة التحكم: عقود نشطة، مدفوعات، ظهور، مزايا |
| `ContractController` | index, show | عرض عقود الراعي فقط (read-only) |
| `PaymentController` | index, show | عرض جدول مدفوعاته |
| `AssetController` | index, store, show, update, destroy | رفع وإدارة مواد إعلانية |
| `ExposureController` | index, summary | عرض إحصائيات الظهور والـ ROI |
| `StatisticsController` | index | إحصائيات شاملة |

### 3.2 — Routes (Sponsor)
```
sponsor/dashboard                 GET
sponsor/statistics                GET
sponsor/contracts                 GET
sponsor/contracts/{contract}      GET
sponsor/payments                  GET
sponsor/payments/{payment}        GET
sponsor/assets                    GET, POST
sponsor/assets/{asset}            GET, PUT, DELETE
sponsor/exposure                  GET
sponsor/exposure/summary          GET
```

كل endpoint يتحقق من `sponsor.user_id === auth_user_id` (نفس نمط المستثمر).

---

## المرحلة 4: Public Endpoints + تكامل مع Event

### 4.1 — Public SponsorController
**المسار:** `apps/expo-api/app/Http/Controllers/Api/SponsorController.php`

عرض رعاة الفعالية للعامة (الموقع/التطبيق):

```
GET /v1/events/{event}/sponsors          — قائمة الرعاة النشطين
GET /v1/events/{event}/sponsor-packages  — الباقات المتاحة
```

### 4.2 — تحديث Event Model
إضافة relationship:
```php
public function sponsors(): HasMany
public function sponsorPackages(): HasMany
```

### 4.3 — تحديث Dashboard Controllers
إضافة إحصائيات الرعاة في:
- Admin Dashboard: عدد الرعاة، إجمالي عقود الرعاية، إجمالي إيرادات الرعاية
- SuperAdmin Dashboard: نفس الشيء + مقارنة بين الفعاليات

---

## المرحلة 5: Auth Service (Role + Permissions + Seeders)

### 5.1 — تحديث RolesAndPermissionsSeeder
**الملف:** `apps/maham-auth-expo-api/database/seeders/RolesAndPermissionsSeeder.php`

إضافة role جديد:
```php
'sponsor' => [
    'display_name' => 'Sponsor',
    'display_name_ar' => 'راعي',
    'description' => 'Event sponsor with dashboard access',
    'level' => 20,
]
```

إضافة ~25 permission جديدة:
```
sponsors.view, sponsors.create, sponsors.update, sponsors.delete, sponsors.approve, sponsors.reject, sponsors.view-all
sponsor-packages.view, sponsor-packages.create, sponsor-packages.update, sponsor-packages.delete
sponsor-contracts.view, sponsor-contracts.create, sponsor-contracts.update, sponsor-contracts.approve, sponsor-contracts.reject, sponsor-contracts.view-all
sponsor-payments.view, sponsor-payments.create, sponsor-payments.view-all
sponsor-assets.view, sponsor-assets.create, sponsor-assets.update, sponsor-assets.delete, sponsor-assets.approve
sponsor-exposure.view
```

تعيين permissions للأدوار:
- **sponsor**: sponsors.view, sponsor-contracts.view, sponsor-payments.view, sponsor-assets.*, sponsor-exposure.view
- **admin**: جميع sponsor permissions
- **moderator**: sponsor*.view + sponsor-contracts.approve/reject

### 5.2 — تحديث SampleUsersSeeder
إضافة 2 رعاة تجريبيين:
```php
'Abdulrahman Al-Fahad' => sponsor1@techgroup.sa (sponsor, ID: 00000000-0000-0000-0000-000000000016)
'Majed Al-Shammari' => sponsor2@mediapartners.sa (sponsor, ID: 00000000-0000-0000-0000-000000000017)
```

---

## ملخص الملفات (إجمالي ~45 ملف جديد/معدل)

### ملفات جديدة (~38 ملف):
| النوع | العدد | المسار |
|-------|-------|--------|
| Enums | 8 | `app/Enums/Sponsor*.php` + `ExposureChannel.php` |
| Migrations | 7 | `database/migrations/` |
| Models | 7 | `app/Models/Sponsor*.php` |
| Admin Controllers | 6 | `app/Http/Controllers/Api/Admin/Sponsor*Controller.php` |
| Sponsor Controllers | 6 | `app/Http/Controllers/Api/Sponsor/*Controller.php` |
| Public Controller | 1 | `app/Http/Controllers/Api/SponsorController.php` |
| Supervisor (reuse) | 0 | يستخدم Admin Controllers (read-only عبر routes) |
| Resources | 3 | `app/Http/Resources/Sponsor*Resource.php` |

### ملفات معدلة (~7 ملفات):
| الملف | التعديل |
|-------|---------|
| `routes/api/v1.php` | إضافة ~35 route جديد |
| `config/expo-api.php` | إضافة إعدادات الرعاة |
| `app/Support/ApiErrorCode.php` | إضافة ~15 error code |
| `app/Models/Event.php` | إضافة sponsors + sponsorPackages relationships |
| `lang/en/messages.php` | إضافة ~40 رسالة |
| `lang/ar/messages.php` | إضافة ~40 رسالة |
| Auth Service seeders (2 ملفات) | إضافة role + permissions + sample users |

---

## ترتيب التنفيذ

```
المرحلة 1 → Enums → Migrations → Models → Config → ErrorCodes → Lang
المرحلة 2 → Admin Controllers → Admin Routes → Supervisor Routes
المرحلة 3 → Sponsor Controllers → Sponsor Routes
المرحلة 4 → Public Controller → Public Routes → Event Model → Dashboards
المرحلة 5 → Auth Service Seeders
```

كل مرحلة مستقلة ويمكن اختبارها بشكل منفصل.
