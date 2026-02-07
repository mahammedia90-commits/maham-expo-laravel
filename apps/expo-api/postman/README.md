# Maham Expo API - Postman Collection

مجموعة Postman لاختبار APIs تطبيق محام للمعارض

## الملفات

| الملف | الوصف |
|-------|-------|
| `Maham_Expo_API.postman_collection.json` | مجموعة جميع APIs |
| `Maham_Expo_Local.postman_environment.json` | متغيرات البيئة المحلية |

## التثبيت

1. افتح Postman
2. اضغط على **Import**
3. اختر الملفين:
   - `Maham_Expo_API.postman_collection.json`
   - `Maham_Expo_Local.postman_environment.json`

## الاستخدام

### 1. اختر البيئة
من القائمة العلوية اليمنى، اختر **Maham Expo - Local**

### 2. تسجيل الدخول
1. اذهب إلى **🔐 Authentication** → **Login**
2. أدخل بيانات الدخول
3. اضغط **Send**
4. سيتم حفظ التوكن تلقائياً في المتغيرات

### 3. استخدم باقي APIs
الآن يمكنك استخدام جميع APIs المحمية

## المتغيرات

| المتغير | الوصف | القيمة الافتراضية |
|---------|-------|-------------------|
| `base_url` | رابط Expo API | `http://127.0.0.1:8002/api` |
| `auth_url` | رابط Auth Service | `http://127.0.0.1:8001/api` |
| `token` | JWT Token (يتم ملؤه تلقائياً) | - |

## الأقسام

| القسم | الوصف | التحقق |
|-------|-------|--------|
| 🔐 Authentication | تسجيل الدخول والتسجيل | - |
| 🏥 Health Check | فحص الخدمة | - |
| 📁 Categories | التصنيفات | عام |
| 🏙️ Cities | المدن | عام |
| 🎪 Events | الفعاليات | عام |
| 📐 Spaces | المساحات | عام |
| 👤 Business Profile | الملف التجاري | مسجل دخوله |
| ❤️ Favorites | المفضلة | مسجل دخوله |
| 🔔 Notifications | الإشعارات | مسجل دخوله |
| 🎫 Visit Requests | طلبات الزيارة | مسجل دخوله |
| 🏢 Rental Requests | طلبات الإيجار | ملف تجاري موثق |
| 👑 Admin | لوحة التحكم | admin/super-admin |

## اللغات

أضف Header التالي لتغيير اللغة:
```
Accept-Language: ar  // العربية
Accept-Language: en  // English
```

## الحالات (Status)

### Events
- `draft` - مسودة
- `published` - منشور
- `ended` - انتهى
- `cancelled` - ملغي

### Requests
- `pending` - قيد المراجعة
- `approved` - موافق عليه
- `rejected` - مرفوض
- `cancelled` - ملغي
- `completed` - مكتمل

### Business Profile
- `pending` - قيد المراجعة
- `approved` - موثق
- `rejected` - مرفوض

### Spaces
- `available` - متاح
- `reserved` - محجوز
- `rented` - مؤجر
- `unavailable` - غير متاح
