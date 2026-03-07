<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| تحكم في الروابط المسموح لها بالوصول للـ API
|
| الأولوية: إعدادات لوحة التحكم (Dashboard) > متغيرات البيئة > القيم الافتراضية
|
| من لوحة التحكم: PUT /api/v1/manage/settings
|   { "settings": { "cors_allowed_origins": "http://localhost:3000,https://app.sa" } }
|
| أو من متغيرات البيئة:
|   CORS_ALLOWED_ORIGINS=http://localhost:3000,https://app.sa
|
*/

// 1. Try reading from Dashboard settings (cache → JSON file fallback)
$dashboardSettings = null;
try {
    $cacheFile = base_path('bootstrap/cache/system_settings.php');
    if (file_exists($cacheFile)) {
        $dashboardSettings = include $cacheFile;
    }
} catch (\Throwable $e) {
    // Ignore — will use env/defaults
}

// 2. Determine CORS values with priority: Dashboard > env > default
$rawOrigins = $dashboardSettings['cors_allowed_origins']
    ?? env('CORS_ALLOWED_ORIGINS', '*');

$maxAge = $dashboardSettings['cors_max_age']
    ?? (int) env('CORS_MAX_AGE', 86400);

$credentials = $dashboardSettings['cors_supports_credentials']
    ?? (bool) env('CORS_SUPPORTS_CREDENTIALS', false);

// Parse comma-separated origins into array
if ($rawOrigins === '*') {
    $origins = ['*'];
} else {
    $origins = array_values(array_filter(
        array_map('trim', explode(',', $rawOrigins))
    ));
}

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => array_values(array_filter(
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS_PATTERNS', '')))
    )),

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-Pagination-Total', 'X-Pagination-Pages'],

    'max_age' => $maxAge,

    'supports_credentials' => $credentials,
];
