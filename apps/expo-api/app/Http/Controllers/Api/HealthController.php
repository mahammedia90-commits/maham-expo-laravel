<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Health check endpoint with diagnostics.
     * ALWAYS returns 200 — Railway/Coolify/Docker treats non-200 as "unhealthy" and restarts the container.
     * Use the 'status' field in JSON body to indicate degraded state instead.
     */
    public function __invoke(): JsonResponse
    {
        $checks = ['status' => 'ok'];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'connected';
        } catch (\Throwable $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
            $checks['status'] = 'degraded';
        }

        // Redis check (only if configured)
        if (in_array(config('cache.default'), ['redis']) || config('queue.default') === 'redis') {
            try {
                Redis::ping();
                $checks['redis'] = 'connected';
            } catch (\Throwable $e) {
                $checks['redis'] = 'error: ' . $e->getMessage();
                $checks['status'] = 'degraded';
            }
        } else {
            $checks['redis'] = 'not configured';
        }

        // Cache driver check
        try {
            Cache::put('health_check', true, 10);
            $checks['cache'] = config('cache.default') . ' (ok)';
        } catch (\Throwable $e) {
            $checks['cache'] = config('cache.default') . ' (error: ' . $e->getMessage() . ')';
            $checks['status'] = 'degraded';
        }

        $checks['service'] = config('app.name');
        $checks['version'] = config('app.version');
        $checks['php'] = PHP_VERSION;
        $checks['laravel'] = app()->version();
        $checks['cache_driver'] = config('cache.default');
        $checks['queue_driver'] = config('queue.default');
        $checks['timestamp'] = now()->toISOString();

        return response()->json($checks, 200);
    }
}
