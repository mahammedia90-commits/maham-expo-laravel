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
        $status = 'ok';
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'connected';
        } catch (\Throwable $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
            $status = 'degraded';
        }

        // Redis check
        $cacheDriver = config('cache.default');
        if ($cacheDriver === 'redis' || config('queue.default') === 'redis') {
            try {
                Redis::ping();
                $checks['redis'] = 'connected';
            } catch (\Throwable $e) {
                $checks['redis'] = 'unavailable: ' . $e->getMessage();
                $status = 'degraded';
            }
        } else {
            $checks['redis'] = 'not configured as primary driver';
        }

        // Cache driver check
        try {
            Cache::put('health_test', true, 5);
            $checks['cache'] = 'working (' . $cacheDriver . ')';
        } catch (\Throwable $e) {
            $checks['cache'] = 'error: ' . $e->getMessage();
            $status = 'degraded';
        }

        // JWT check
        $checks['jwt'] = !empty(config('jwt.secret')) ? 'configured' : 'MISSING JWT_SECRET';
        if ($checks['jwt'] !== 'configured') {
            $status = 'degraded';
        }

        return response()->json([
            'status' => $status,
            'service' => config('auth-service.service_name'),
            'version' => config('auth-service.service_version'),
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
            'drivers' => [
                'cache' => $cacheDriver,
                'queue' => config('queue.default'),
                'session' => config('session.driver'),
            ],
            'php_version' => PHP_VERSION,
        ], 200);
    }
}
