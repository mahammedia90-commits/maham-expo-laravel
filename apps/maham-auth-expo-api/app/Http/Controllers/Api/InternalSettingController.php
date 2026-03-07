<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Internal Settings Controller
 *
 * Receives settings from expo-api service (internal Docker network only).
 * Writes to bootstrap cache so config files can read them.
 */
class InternalSettingController extends Controller
{
    /**
     * Receive settings from expo-api and sync to bootstrap cache
     */
    public function syncSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.sms_enabled' => 'sometimes|boolean',
            'settings.sms_default_channel' => 'sometimes|string|in:sms,whatsapp',
            'settings.sms_max_attempts_per_hour' => 'sometimes|integer|min:1|max:20',
            'settings.sms_code_length' => 'sometimes|integer|min:4|max:8',
        ]);

        try {
            // Read existing cached settings
            $cachePath = base_path('bootstrap/cache/system_settings.php');
            $existing = [];

            if (file_exists($cachePath)) {
                try {
                    $existing = include $cachePath;
                } catch (\Throwable $e) {
                    $existing = [];
                }
            }

            // Merge new settings
            $merged = array_merge($existing, $validated['settings']);

            // Write to bootstrap cache
            $content = '<?php return ' . var_export($merged, true) . ';' . PHP_EOL;
            file_put_contents($cachePath, $content);

            // Rebuild config cache
            Artisan::call('config:cache');

            Log::info('Settings synced from expo-api', ['keys' => array_keys($validated['settings'])]);

            return response()->json([
                'success' => true,
                'message' => 'Settings synced successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to sync settings', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync settings',
            ], 500);
        }
    }
}
