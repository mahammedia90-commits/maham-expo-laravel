<?php

namespace App\Sdk;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Auth Service SDK
 * 
 * هذا الكلاس يُستخدم في المشاريع الأخرى للتواصل مع Auth Service
 * 
 * Installation in other projects:
 * composer require your-org/auth-sdk
 * 
 * Usage:
 * $authSdk = new AuthServiceSdk(config('services.auth.url'));
 * $user = $authSdk->verifyToken($token);
 */
class AuthServiceSdk
{
    protected string $baseUrl;
    protected int $timeout = 5;
    protected bool $cacheEnabled = true;
    protected int $cacheTtl = 300; // 5 minutes

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Configure SDK
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function disableCache(): self
    {
        $this->cacheEnabled = false;
        return $this;
    }

    public function setCacheTtl(int $seconds): self
    {
        $this->cacheTtl = $seconds;
        return $this;
    }

    /**
     * Verify user token
     */
    public function verifyToken(string $token): ?array
    {
        $cacheKey = 'auth_verify_' . md5($token);

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->request('POST', '/service/verify-token', [
            'token' => $token,
        ]);

        if (!$response['success']) {
            return null;
        }

        $userData = $response['data'];

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $userData, $this->cacheTtl);
        }

        return $userData;
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $userId, string $permission): bool
    {
        $cacheKey = "auth_perm_{$userId}_{$permission}";

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->request('POST', '/service/check-permission', [
            'user_id' => $userId,
            'permission' => $permission,
        ]);

        $hasPermission = $response['success'] && ($response['data']['has_permission'] ?? false);

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $hasPermission, $this->cacheTtl);
        }

        return $hasPermission;
    }

    /**
     * Get user info
     */
    public function getUserInfo(string $userId): ?array
    {
        $cacheKey = "auth_user_{$userId}";

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->request('POST', '/service/user-info', [
            'user_id' => $userId,
        ]);

        if (!$response['success']) {
            return null;
        }

        $userData = $response['data'];

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $userData, $this->cacheTtl);
        }

        return $userData;
    }

    /**
     * Clear cache for user
     */
    public function clearUserCache(string $userId): void
    {
        Cache::forget("auth_user_{$userId}");
        // Note: Permission cache keys would need to be tracked separately
    }

    /**
     * Make HTTP request to Auth Service
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->$method($this->baseUrl . '/api' . $endpoint, $data);

            return $response->json() ?? [
                'success' => false,
                'message' => 'Invalid response',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Auth service unavailable: ' . $e->getMessage(),
            ];
        }
    }
}

/**
 * Laravel Middleware للاستخدام في المشاريع الأخرى
 * Note: This middleware is for SDK usage in other projects
 * Do not use in this project - use App\Http\Middleware\AuthServiceMiddleware instead
 */
class AuthServiceSdkMiddleware
{
    protected AuthServiceSdk $authSdk;

    public function __construct()
    {
        $this->authSdk = new AuthServiceSdk(
            config('services.auth.url')
        );
    }

    public function handle($request, \Closure $next, ?string $permission = null)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token required',
            ], 401);
        }

        $userData = $this->authSdk->verifyToken($token);

        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        // Check permission if specified
        if ($permission && !$this->authSdk->hasPermission($userData['user']['id'], $permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied',
            ], 403);
        }

        // Add user data to request
        $request->merge(['auth_user' => $userData['user']]);

        return $next($request);
    }
}
