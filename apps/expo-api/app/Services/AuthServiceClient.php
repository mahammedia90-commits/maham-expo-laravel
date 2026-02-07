<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthServiceClient
{
    protected string $baseUrl;
    protected ?string $serviceToken;
    protected int $timeout;
    protected bool $cacheEnabled;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('expo-api.auth_service.url'), '/');
        $this->serviceToken = config('expo-api.auth_service.token');
        $this->timeout = config('expo-api.auth_service.timeout', 5);
        $this->cacheEnabled = config('expo-api.cache.enabled', true);
        $this->cacheTtl = config('expo-api.auth_service.cache_ttl', 300);
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
     * Check if user has role
     */
    public function hasRole(array $userRoles, string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(array $userRoles): bool
    {
        return $this->hasRole($userRoles, ['admin', 'super-admin']);
    }

    /**
     * Clear cache for user
     */
    public function clearUserCache(string $userId): void
    {
        Cache::forget("auth_user_{$userId}");
    }

    /**
     * Clear token cache
     */
    public function clearTokenCache(string $token): void
    {
        Cache::forget('auth_verify_' . md5($token));
    }

    /**
     * Make HTTP request to Auth Service
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
                'X-Service-Token' => $this->serviceToken,
                'Accept' => 'application/json',
                'Accept-Language' => app()->getLocale(),
            ])
            ->timeout($this->timeout)
            ->$method($this->baseUrl . '/api' . $endpoint, $data);

            return $response->json() ?? [
                'success' => false,
                'message' => 'Invalid response from auth service',
            ];
        } catch (\Exception $e) {
            Log::error('Auth service request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Auth service unavailable: ' . $e->getMessage(),
            ];
        }
    }
}
