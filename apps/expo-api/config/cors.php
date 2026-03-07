<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| تحكم في الروابط المسموح لها بالوصول للـ API
|
| CORS_ALLOWED_ORIGINS=*                                    ← السماح للجميع
| CORS_ALLOWED_ORIGINS=http://localhost:3000                ← رابط واحد
| CORS_ALLOWED_ORIGINS=http://localhost:3000,https://app.sa ← عدة روابط
|
*/

$allowedOrigins = env('CORS_ALLOWED_ORIGINS', '*');

// Parse comma-separated origins into array
if ($allowedOrigins === '*') {
    $origins = ['*'];
} else {
    $origins = array_values(array_filter(
        array_map('trim', explode(',', $allowedOrigins))
    ));
}

return [
    /*
     * المسارات التي يُطبق عليها CORS
     */
    'paths' => ['api/*'],

    /*
     * الطرق المسموحة (GET, POST, PUT, DELETE, etc.)
     */
    'allowed_methods' => ['*'],

    /*
     * الروابط المسموح لها بالوصول
     * يمكنك التحكم بها من خلال متغير البيئة CORS_ALLOWED_ORIGINS
     */
    'allowed_origins' => $origins,

    /*
     * أنماط الروابط المسموحة (regex)
     * مثال: ['#^https://.*\.mahamexpo\.sa$#']
     */
    'allowed_origins_patterns' => array_values(array_filter(
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS_PATTERNS', '')))
    )),

    /*
     * الهيدرات المسموحة في الطلبات
     */
    'allowed_headers' => ['*'],

    /*
     * الهيدرات المكشوفة في الردود
     */
    'exposed_headers' => ['X-Pagination-Total', 'X-Pagination-Pages'],

    /*
     * مدة تخزين نتيجة preflight (بالثواني)
     * 86400 = 24 ساعة
     */
    'max_age' => (int) env('CORS_MAX_AGE', 86400),

    /*
     * السماح بإرسال الـ Credentials (cookies, authorization headers)
     * ملاحظة: إذا كان true، لا يمكن استخدام * في allowed_origins
     */
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),
];
