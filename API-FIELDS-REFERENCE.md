# Maham Expo API — Complete Fields Reference

> **Base URLs**
> - Auth API: `https://auth-service-api.mahamexpo.sa/api/v1`
> - Expo API: `https://expo-service-api.mahamexpo.sa/api/v1`

---

## Authentication (Auth API)

### POST `/auth/login`
| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `identifier` | string | ✅ | ❌ | Email or phone |
| `password` | string | ✅ | ❌ | Password |

**Response:**
```json
{
  "access_token": "string",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { "id": "uuid", "name": "string", "email": "string", "role": "string" }
}
```

---

## Events

### GET `/manage/events`
**Query Params:** `page`, `per_page`, `search`, `status`

**Response (paginated):**
```json
{
  "data": [Event],
  "pagination": { "current_page": 1, "last_page": 5, "total": 50, "per_page": 15 }
}
```

### POST `/manage/events` — Create Event
**Content-Type:** `multipart/form-data`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | Event name (EN) |
| `name_ar` | string | ✅ | ❌ | Event name (AR) |
| `category_id` | uuid | ✅ | ❌ | Category UUID |
| `city_id` | uuid | ✅ | ❌ | City UUID |
| `address` | string | ✅ | ❌ | Event address |
| `start_date` | date (Y-m-d) | ✅ | ❌ | Start date |
| `end_date` | date (Y-m-d) | ✅ | ❌ | End date (after start_date) |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `address_ar` | string | ❌ | ✅ | Address (AR) |
| `latitude` | numeric | ❌ | ✅ | Latitude |
| `longitude` | numeric | ❌ | ✅ | Longitude |
| `opening_time` | string (H:i) | ❌ | ✅ | e.g. "09:00" |
| `closing_time` | string (H:i) | ❌ | ✅ | e.g. "22:00" |
| `images[]` | file[] | ❌ | ✅ | Max 10 images, each max 5120KB |
| `images_360[]` | file[] | ❌ | ✅ | Max 5 images, each max 10240KB |
| `features` | string | ❌ | ✅ | Features (EN) |
| `features_ar` | string | ❌ | ✅ | Features (AR) |
| `organizer_name` | string | ❌ | ✅ | Organizer name |
| `organizer_phone` | string | ❌ | ✅ | Organizer phone |
| `organizer_email` | email | ❌ | ✅ | Organizer email |
| `website` | url | ❌ | ✅ | Website URL |
| `status` | enum | ❌ | ❌ | `draft`, `published`, `cancelled`, `completed` |
| `is_featured` | boolean | ❌ | ❌ | Send "1"/"0" in FormData |

### PUT `/manage/events/{id}` — Update Event
Same fields as create. Use `POST` with `_method=PUT` for FormData.
Additional fields on update:
| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `existing_images[]` | string[] | ❌ | ✅ | URLs of images to keep |
| `existing_images_360[]` | string[] | ❌ | ✅ | URLs of 360° images to keep |

### DELETE `/manage/events/{id}` — Delete Event

**Event Response Object:**
```json
{
  "id": "uuid",
  "name": "string",
  "name_ar": "string",
  "description": "string|null",
  "description_ar": "string|null",
  "category_id": "uuid",
  "city_id": "uuid",
  "address": "string",
  "address_ar": "string|null",
  "start_date": "2025-01-01",
  "end_date": "2025-01-05",
  "opening_time": "09:00",
  "closing_time": "22:00",
  "latitude": "24.7136",
  "longitude": "46.6753",
  "images": ["url1", "url2"],
  "images_360": ["url1"],
  "features": "string|null",
  "features_ar": "string|null",
  "organizer_name": "string|null",
  "organizer_phone": "string|null",
  "organizer_email": "string|null",
  "website": "string|null",
  "status": "draft",
  "is_featured": false,
  "created_at": "2025-01-01T00:00:00Z",
  "updated_at": "2025-01-01T00:00:00Z"
}
```

---

## Spaces (Nested under Events)

### GET `/manage/events/{eventId}/spaces` — List spaces for event
**Query Params:** `page`, `per_page`, `search`, `status`

### POST `/manage/events/{eventId}/spaces` — Create space
**Content-Type:** `multipart/form-data`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | Space name (EN) |
| `location_code` | string | ✅ | ❌ | Unique code scoped to event |
| `area_sqm` | numeric | ✅ | ❌ | Area in sqm, min 1 |
| `price_total` | numeric | ✅ | ❌ | Total price |
| `name_ar` | string | ❌ | ✅ | Space name (AR) |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `price_per_day` | numeric | ❌ | ✅ | Daily price |
| `images[]` | file[] | ❌ | ✅ | Space images |
| `images_360[]` | file[] | ❌ | ✅ | 360° images |
| `amenities[]` | string[] | ❌ | ✅ | Array of amenity strings |
| `amenities_ar` | string | ❌ | ✅ | Amenities (AR) |
| `status` | enum | ❌ | ✅ | `available`, `reserved`, `rented`, `unavailable` |
| `floor_number` | integer | ❌ | ✅ | Floor number |
| `section_id` | uuid | ❌ | ✅ | Section UUID |
| `space_type` | string | ❌ | ✅ | `booth`, `hall`, `room`, `outdoor` |
| `payment_system` | string | ❌ | ✅ | Payment system type |
| `rental_duration` | string | ❌ | ✅ | Rental duration |
| `latitude` | numeric | ❌ | ✅ | Latitude |
| `longitude` | numeric | ❌ | ✅ | Longitude |
| `address` | string | ❌ | ✅ | Address |
| `address_ar` | string | ❌ | ✅ | Address (AR) |
| `services[]` | uuid[] | ❌ | ✅ | Array of service UUIDs |

### GET `/manage/spaces/{id}` — Show space (top-level)
### PUT `/manage/spaces/{id}` — Update space (top-level, use POST + _method=PUT for FormData)
Additional update fields:
| Field | Type | Description |
|-------|------|-------------|
| `existing_images[]` | string[] | URLs of images to keep |
| `existing_images_360[]` | string[] | URLs of 360° images to keep |

### DELETE `/manage/spaces/{id}` — Delete space (top-level)

**Space Response Object:**
```json
{
  "id": "uuid",
  "event_id": "uuid",
  "name": "string",
  "name_ar": "string|null",
  "location_code": "string",
  "area_sqm": 50,
  "price_total": 10000,
  "price_per_day": 2000,
  "description": "string|null",
  "description_ar": "string|null",
  "images": ["url"],
  "images_360": ["url"],
  "amenities": ["WiFi", "AC"],
  "status": "available",
  "floor_number": 1,
  "space_type": "booth",
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Sponsors

### GET `/manage/sponsors` — List sponsors
**Query Params:** `page`, `per_page`, `status`

### POST `/manage/sponsors` — Create sponsor
**Content-Type:** `multipart/form-data`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `event_id` | uuid | ✅ | ❌ | Event UUID |
| `name` | string | ✅ | ❌ | Sponsor name (EN) |
| `name_ar` | string | ✅ | ❌ | Sponsor name (AR) |
| `company_name` | string | ❌ | ✅ | Company name (EN) |
| `company_name_ar` | string | ❌ | ✅ | Company name (AR) |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `logo` | file (image) | ❌ | ✅ | Company logo image |
| `contact_person` | string | ❌ | ✅ | Contact person name |
| `contact_email` | email | ❌ | ✅ | Contact email |
| `contact_phone` | string | ❌ | ✅ | Contact phone |
| `website` | url | ❌ | ✅ | Website URL |
| `status` | enum | ❌ | ❌ | `pending`, `approved`, `active`, `suspended`, `inactive` |
| `user_id` | uuid | ❌ | ❌ | Assign to user |

### PUT `/manage/sponsors/{id}` — Update sponsor
Same fields as create. Use `POST` with `_method=PUT` for FormData.

### DELETE `/manage/sponsors/{id}` — Delete sponsor

### PUT `/manage/sponsors/{id}/approve` — Approve sponsor (no body)
### PUT `/manage/sponsors/{id}/activate` — Activate sponsor (no body)
### PUT `/manage/sponsors/{id}/suspend` — Suspend sponsor (no body)

**Sponsor Response Object:**
```json
{
  "id": "uuid",
  "event_id": "uuid",
  "event": { "name": "string", "name_ar": "string" },
  "name": "string",
  "name_ar": "string",
  "company_name": "string|null",
  "company_name_ar": "string|null",
  "description": "string|null",
  "description_ar": "string|null",
  "logo": "url|null",
  "contact_person": "string|null",
  "contact_email": "string|null",
  "contact_phone": "string|null",
  "website": "string|null",
  "status": "pending",
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Banners

### GET `/manage/banners` — List banners
**Query Params:** `page`, `per_page`

### POST `/manage/banners` — Create banner
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `title` | string | ✅ | ❌ | Banner title (EN) |
| `title_ar` | string | ✅ | ❌ | Banner title (AR) |
| `image` | string (URL) | ✅ | ❌ | Image URL (NOT file upload) |
| `position` | string | ✅ | ❌ | e.g. `home_top`, `home_bottom`, `sidebar` |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `image_ar` | string (URL) | ❌ | ✅ | Arabic image URL |
| `link_url` | url | ❌ | ✅ | Click-through URL |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |
| `start_date` | date | ❌ | ✅ | Start display date |
| `end_date` | date | ❌ | ✅ | End display date |

### PUT `/manage/banners/{id}` — Update banner (JSON)
### DELETE `/manage/banners/{id}` — Delete banner

**Banner Response Object:**
```json
{
  "id": "uuid",
  "title": "string",
  "title_ar": "string",
  "image": "url",
  "image_ar": "url|null",
  "position": "home_top",
  "description": "string|null",
  "description_ar": "string|null",
  "link_url": "url|null",
  "is_active": true,
  "sort_order": 0,
  "start_date": "2025-01-01|null",
  "end_date": "2025-12-31|null",
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Sponsor Packages (Nested under Events)

### GET `/manage/events/{eventId}/sponsor-packages` — List packages
**Query Params:** `page`, `per_page`

### POST `/manage/events/{eventId}/sponsor-packages` — Create package
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | Package name (EN) |
| `name_ar` | string | ✅ | ❌ | Package name (AR) |
| `tier` | enum | ✅ | ❌ | `platinum`, `gold`, `silver`, `bronze`, `media`, `strategic` |
| `price` | numeric | ✅ | ❌ | Package price |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `max_sponsors` | integer | ❌ | ✅ | Max allowed sponsors |
| `benefits` | string[] | ❌ | ✅ | Array of benefit descriptions |
| `display_screens_count` | integer | ❌ | ✅ | Number of screens |
| `banners_count` | integer | ❌ | ✅ | Number of banners |
| `vip_invitations_count` | integer | ❌ | ✅ | Number of VIP invitations |
| `booth_area_sqm` | numeric | ❌ | ✅ | Booth area in sqm |
| `logo_placement` | string[] | ❌ | ✅ | Where logo is placed |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |

### GET `/manage/sponsor-packages/{id}` — Show package (top-level)
### PUT `/manage/sponsor-packages/{id}` — Update package (top-level, JSON)
### DELETE `/manage/sponsor-packages/{id}` — Delete package (top-level)

**Sponsor Package Response Object:**
```json
{
  "id": "uuid",
  "event_id": "uuid",
  "name": "string",
  "name_ar": "string",
  "tier": "gold",
  "price": 50000,
  "description": "string|null",
  "description_ar": "string|null",
  "max_sponsors": 5,
  "current_sponsors": 2,
  "benefits": ["Benefit 1", "Benefit 2"],
  "display_screens_count": 3,
  "banners_count": 5,
  "vip_invitations_count": 10,
  "booth_area_sqm": 50,
  "logo_placement": ["stage", "website"],
  "is_active": true,
  "sort_order": 0,
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Categories (SuperAdmin only)

### GET `/manage/categories` — List categories
**Query Params:** `page`, `per_page`, `search`

### POST `/manage/categories` — Create category
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | Category name (EN), unique |
| `name_ar` | string | ✅ | ❌ | Category name (AR), unique |
| `icon` | string | ❌ | ✅ | Icon name/emoji |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |

### PUT `/manage/categories/{id}` — Update category (JSON)
### DELETE `/manage/categories/{id}` — Delete category

**Category Response Object:**
```json
{
  "id": "uuid",
  "name": "string",
  "name_ar": "string",
  "icon": "string|null",
  "description": "string|null",
  "description_ar": "string|null",
  "is_active": true,
  "sort_order": 0,
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Cities (SuperAdmin only)

### GET `/manage/cities` — List cities
### POST `/manage/cities` — Create city
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | City name (EN) |
| `name_ar` | string | ✅ | ❌ | City name (AR) |
| `region` | string | ❌ | ✅ | Region name (EN) |
| `region_ar` | string | ❌ | ✅ | Region name (AR) |
| `latitude` | numeric | ❌ | ✅ | Latitude |
| `longitude` | numeric | ❌ | ✅ | Longitude |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |

### PUT `/manage/cities/{id}` — Update city (JSON)
### DELETE `/manage/cities/{id}` — Delete city

---

## Services

### GET `/manage/services` — List services
**Query Params:** `page`, `per_page`, `search`

### POST `/manage/services` — Create service
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `name` | string | ✅ | ❌ | Service name (EN) |
| `name_ar` | string | ❌ | ✅ | Service name (AR) |
| `description` | string | ❌ | ✅ | Description (EN) |
| `description_ar` | string | ❌ | ✅ | Description (AR) |
| `icon` | string | ❌ | ✅ | Icon name |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |

### PUT `/manage/services/{id}` — Update service (JSON)
### DELETE `/manage/services/{id}` — Delete service

---

## Pages

### GET `/manage/pages` — List pages
### POST `/manage/pages` — Create page
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `slug` | string | ✅ | ❌ | URL slug, unique |
| `title` | string | ✅ | ❌ | Page title (EN) |
| `title_ar` | string | ✅ | ❌ | Page title (AR) |
| `content` | string | ✅ | ❌ | Content (EN) |
| `content_ar` | string | ✅ | ❌ | Content (AR) |
| `type` | string | ✅ | ❌ | Page type |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |
| `meta` | array | ❌ | ✅ | SEO meta data |

### PUT `/manage/pages/{id}` — Update page (JSON)
### DELETE `/manage/pages/{id}` — Delete page

---

## FAQs

### GET `/manage/faqs` — List FAQs
### POST `/manage/faqs` — Create FAQ
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `question` | string | ✅ | ❌ | Question (EN) |
| `question_ar` | string | ✅ | ❌ | Question (AR) |
| `answer` | string | ✅ | ❌ | Answer (EN) |
| `answer_ar` | string | ✅ | ❌ | Answer (AR) |
| `category` | string | ❌ | ✅ | FAQ category |
| `is_active` | boolean | ❌ | ❌ | Default true |
| `sort_order` | integer | ❌ | ❌ | Display order |

### PUT `/manage/faqs/{id}` — Update FAQ (JSON)
### DELETE `/manage/faqs/{id}` — Delete FAQ

---

## Ratings

### GET `/manage/ratings` — List ratings
**Query Params:** `page`, `per_page`, `status`

### GET `/manage/ratings/{id}` — Show rating
### PUT `/manage/ratings/{id}/approve` — Approve rating (no body needed)
### PUT `/manage/ratings/{id}/reject` — Reject rating (no body, **deletes** the rating)
### DELETE `/manage/ratings/{id}` — Delete rating

**Rating Response Object:**
```json
{
  "id": "uuid",
  "user": { "id": "uuid", "name": "string" },
  "overall_rating": 5,
  "type": "string",
  "comment": "string|null",
  "comment_ar": "string|null",
  "status": "pending",
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Sponsor Assets

### GET `/manage/sponsor-assets` — List assets
**Query Params:** `page`, `per_page`

### GET `/manage/sponsor-assets/{id}` — Show asset
### PUT `/manage/sponsor-assets/{id}/approve` — Approve asset (no body needed)
### PUT `/manage/sponsor-assets/{id}/reject` — Reject asset

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `rejection_reason` | string | ✅ | ❌ | Reason for rejection, max 2000 chars |

**Sponsor Asset Response Object:**
```json
{
  "id": "uuid",
  "sponsor": { "company_name": "string" },
  "benefit": { "name": "string", "name_ar": "string" },
  "type": "logo|banner|video|document",
  "file_url": "url",
  "file_name": "string",
  "status": "pending_review",
  "review_notes": "string|null",
  "submitted_at": "2025-01-01T00:00:00Z",
  "reviewed_at": "2025-01-01T00:00:00Z|null",
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

## Sponsor Contracts

### GET `/manage/sponsor-contracts` — List contracts
### POST `/manage/sponsor-contracts` — Create contract
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `sponsor_id` | uuid | ✅ | ❌ | Sponsor UUID |
| `sponsor_package_id` | uuid | ✅ | ❌ | Package UUID |
| `event_id` | uuid | ✅ | ❌ | Event UUID |
| `start_date` | date | ✅ | ❌ | Contract start |
| `end_date` | date | ✅ | ❌ | Contract end |
| `total_amount` | numeric | ✅ | ❌ | Total amount |
| `terms` | string | ❌ | ✅ | Terms (EN) |
| `terms_ar` | string | ❌ | ✅ | Terms (AR) |
| `notes` | string | ❌ | ✅ | Notes |
| `status` | enum | ❌ | ❌ | Contract status |

### PUT `/manage/sponsor-contracts/{id}` — Update contract (JSON)

---

## Invoices

### GET `/manage/invoices` — List invoices
### POST `/manage/invoices` — Create invoice
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `user_id` | uuid | ✅ | ❌ | User UUID |
| `invoiceable_type` | string | ✅ | ❌ | Polymorphic type |
| `invoiceable_id` | uuid | ✅ | ❌ | Polymorphic ID |
| `title` | string | ✅ | ❌ | Invoice title |
| `subtotal` | numeric | ✅ | ❌ | Subtotal amount |
| `total_amount` | numeric | ✅ | ❌ | Total amount |
| `issue_date` | date | ✅ | ❌ | Issue date |
| `due_date` | date | ✅ | ❌ | Due date |
| `title_ar` | string | ❌ | ✅ | Title (AR) |
| `tax_amount` | numeric | ❌ | ✅ | Tax amount |
| `discount_amount` | numeric | ❌ | ✅ | Discount amount |
| `items` | array | ❌ | ✅ | Invoice line items |
| `notes` | string | ❌ | ✅ | Notes (EN) |
| `notes_ar` | string | ❌ | ✅ | Notes (AR) |

### PUT `/manage/invoices/{id}` — Update invoice (JSON)

---

## Rental Contracts

### GET `/manage/rental-contracts` — List rental contracts
### POST `/manage/rental-contracts` — Create rental contract
**Content-Type:** `application/json`

| Field | Type | Required | Nullable | Description |
|-------|------|----------|----------|-------------|
| `rental_request_id` | uuid | ✅ | ❌ | Rental request UUID |
| `terms` | string | ❌ | ✅ | Terms (EN) |
| `terms_ar` | string | ❌ | ✅ | Terms (AR) |
| `admin_notes` | string | ❌ | ✅ | Admin notes |

### PUT `/manage/rental-contracts/{id}` — Update rental contract (JSON)

---

## Important Notes

### Content-Type Rules
| Resource | Content-Type | Why |
|----------|-------------|-----|
| Events | `multipart/form-data` | Has image file uploads |
| Spaces | `multipart/form-data` | Has image & 360° file uploads |
| Sponsors | `multipart/form-data` | Has logo file upload |
| Banners | `application/json` | Image is a URL string, NOT a file |
| All others | `application/json` | No file uploads |

### Nested Routes (No top-level list/create!)
| Resource | List/Create Route | Show/Update/Delete Route |
|----------|-------------------|--------------------------|
| Spaces | `GET/POST /manage/events/{eventId}/spaces` | `GET/PUT/DELETE /manage/spaces/{id}` |
| Sponsor Packages | `GET/POST /manage/events/{eventId}/sponsor-packages` | `GET/PUT/DELETE /manage/sponsor-packages/{id}` |
| Sections | `GET/POST /manage/events/{eventId}/sections` | `GET/PUT/DELETE /manage/sections/{id}` |

### FormData PUT workaround
For resources using `multipart/form-data`, Laravel requires using `POST` with `_method=PUT` field:
```javascript
fd.append('_method', 'PUT');
await expoApi.post(`/manage/resources/${id}`, fd, {
  headers: { 'Content-Type': 'multipart/form-data' }
});
```

### Boolean fields in FormData
Send `"1"` or `"0"` as strings (not `"true"`/`"false"`):
```javascript
fd.append('is_featured', formData.is_featured ? '1' : '0');
```

### Pagination Response Format
```json
{
  "data": [],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "total": 50,
    "per_page": 15
  }
}
```
