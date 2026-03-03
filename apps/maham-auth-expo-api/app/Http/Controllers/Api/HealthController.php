<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Diagnostic endpoint — TEMPORARY (remove after debugging)
     */
    public function diagnose(Request $request): JsonResponse
    {
        $steps = [];

        // Step 1: Find user
        try {
            $user = \App\Models\User::where('email', 'admin@example.com')->first();
            $steps['find_user'] = $user ? 'found (id: ' . $user->id . ')' : 'NOT FOUND';
        } catch (\Throwable $e) {
            $steps['find_user'] = 'ERROR: ' . $e->getMessage();
            return response()->json(['steps' => $steps], 200);
        }

        if (!$user) {
            return response()->json(['steps' => $steps], 200);
        }

        // Step 2: Check password
        try {
            $passwordOk = \Illuminate\Support\Facades\Hash::check('password', $user->password);
            $steps['password_check'] = $passwordOk ? 'OK' : 'MISMATCH';
        } catch (\Throwable $e) {
            $steps['password_check'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 3: Load roles
        try {
            $roles = $user->roles->pluck('name')->toArray();
            $steps['load_roles'] = $roles;
        } catch (\Throwable $e) {
            $steps['load_roles'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 4: Generate JWT token
        try {
            $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
            $steps['jwt_token'] = $token ? 'generated (' . strlen($token) . ' chars)' : 'EMPTY';
        } catch (\Throwable $e) {
            $steps['jwt_token'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 5: Get all permissions
        try {
            $permissions = $user->getAllPermissions()->toArray();
            $steps['permissions'] = 'loaded (' . count($permissions) . ' permissions)';
        } catch (\Throwable $e) {
            $steps['permissions'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 6: Get fullInfo
        try {
            $fullInfo = $user->fullInfo;
            $steps['full_info'] = 'OK';
        } catch (\Throwable $e) {
            $steps['full_info'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 7: updateLastLogin
        try {
            $user->updateLastLogin('127.0.0.1');
            $steps['update_last_login'] = 'OK';
        } catch (\Throwable $e) {
            $steps['update_last_login'] = 'ERROR: ' . $e->getMessage();
        }

        // Step 8: Audit log
        try {
            $auditService = app(\App\Services\AuditService::class);
            $auditService->log('diagnose_test', $user, ['ip' => '127.0.0.1']);
            $steps['audit_log'] = 'OK';
        } catch (\Throwable $e) {
            $steps['audit_log'] = 'ERROR: ' . $e->getMessage();
        }

        return response()->json(['steps' => $steps], 200);
    }
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
