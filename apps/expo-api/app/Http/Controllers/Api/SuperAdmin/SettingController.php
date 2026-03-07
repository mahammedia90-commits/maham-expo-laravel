<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * SuperAdmin Setting Controller
 *
 * Manages system-level settings stored in cache with JSON file fallback.
 */
class SettingController extends Controller
{
    private const CACHE_KEY = 'system_settings';

    private array $defaults = [
        'site_name' => 'Maham Expo',
        'site_name_ar' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'support_email' => '',
        'maintenance_mode' => false,
        'allow_registration' => true,
        'auto_approve_profiles' => false,
        'max_visit_requests_per_day' => 10,
        'max_rental_requests_per_merchant' => 5,
        'default_currency' => 'SAR',
        'timezone' => 'Asia/Riyadh',

        // ── CORS Settings ──
        'cors_allowed_origins' => '*',           // * أو روابط مفصولة بفاصلة
        'cors_supports_credentials' => false,
        'cors_max_age' => 86400,
    ];

    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success($this->getSettings());
    }

    /**
     * Get a single setting by key
     */
    public function show(string $key): JsonResponse
    {
        $settings = $this->getSettings();

        if (!array_key_exists($key, $settings)) {
            return ApiResponse::notFound(__('messages.setting.not_found'));
        }

        return ApiResponse::success([
            'key' => $key,
            'value' => $settings[$key],
        ]);
    }

    /**
     * Update settings (batch update)
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.site_name' => 'sometimes|string|max:255',
            'settings.site_name_ar' => 'sometimes|string|max:255',
            'settings.contact_email' => 'sometimes|email|max:255',
            'settings.contact_phone' => 'sometimes|string|max:20',
            'settings.support_email' => 'sometimes|email|max:255',
            'settings.maintenance_mode' => 'sometimes|boolean',
            'settings.allow_registration' => 'sometimes|boolean',
            'settings.auto_approve_profiles' => 'sometimes|boolean',
            'settings.max_visit_requests_per_day' => 'sometimes|integer|min:1|max:100',
            'settings.max_rental_requests_per_merchant' => 'sometimes|integer|min:1|max:50',
            'settings.default_currency' => 'sometimes|string|max:10',
            'settings.timezone' => 'sometimes|string|max:50',

            // CORS
            'settings.cors_allowed_origins' => 'sometimes|string|max:2000',
            'settings.cors_supports_credentials' => 'sometimes|boolean',
            'settings.cors_max_age' => 'sometimes|integer|min:0|max:604800',
        ]);

        $currentSettings = $this->getSettings();
        $updatedSettings = array_merge($currentSettings, $validated['settings']);

        Cache::forever(self::CACHE_KEY, $updatedSettings);

        // Persist to JSON file as backup
        $settingsPath = storage_path('app/settings.json');
        file_put_contents($settingsPath, json_encode($updatedSettings, JSON_PRETTY_PRINT));

        // Sync CORS settings to bootstrap cache (for config:cache compatibility)
        $this->syncCorsBootstrapCache($updatedSettings);

        // Rebuild config cache so CORS changes take effect immediately
        try {
            Artisan::call('config:cache');
        } catch (\Throwable $e) {
            // Ignore — cache will be rebuilt on next request
        }

        return ApiResponse::success(
            data: $updatedSettings,
            message: __('messages.setting.updated')
        );
    }

    /**
     * Get settings from cache or file fallback
     */
    private function getSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settingsPath = storage_path('app/settings.json');

            if (file_exists($settingsPath)) {
                $saved = json_decode(file_get_contents($settingsPath), true);
                return array_merge($this->defaults, $saved ?? []);
            }

            return $this->defaults;
        });
    }

    /**
     * Save CORS-related settings to a bootstrap cache file
     * so config/cors.php can read them during config:cache
     */
    private function syncCorsBootstrapCache(array $settings): void
    {
        $corsKeys = ['cors_allowed_origins', 'cors_supports_credentials', 'cors_max_age'];
        $corsSettings = [];

        foreach ($corsKeys as $key) {
            if (isset($settings[$key])) {
                $corsSettings[$key] = $settings[$key];
            }
        }

        $cachePath = base_path('bootstrap/cache/system_settings.php');
        $content = '<?php return ' . var_export($corsSettings, true) . ';' . PHP_EOL;
        file_put_contents($cachePath, $content);
    }
}
