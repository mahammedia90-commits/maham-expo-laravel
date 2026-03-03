<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthServiceClient
{
    protected string $baseUrl;
    protected int $timeout;
    protected bool $cacheEnabled;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('expo-api.auth_service.url'), '/');
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

        // Try cache first (gracefully handle cache failures)
        if ($this->cacheEnabled) {
            try {
                if (Cache::has($cacheKey)) {
                    $cached = Cache::get($cacheKey);

                    // Validate cached data has valid permissions (not null-filled from old bug)
                    if ($cached && $this->hasValidPermissions($cached)) {
                        return $cached;
                    }

                    // Stale cache with invalid permissions — clear and re-fetch
                    Cache::forget($cacheKey);
                    Log::info('Cleared stale auth cache with invalid permissions', [
                        'user_id' => $cached['user']['id'] ?? 'unknown',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Cache unavailable for auth verification, proceeding without cache', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $response = $this->request('POST', '/service/verify-token', [
            'token' => $token,
        ]);

        if (!$response['success']) {
            return null;
        }

        $userData = $response['data'];

        if ($this->cacheEnabled) {
            try {
                Cache::put($cacheKey, $userData, $this->cacheTtl);
            } catch (\Throwable $e) {
                Log::warning('Cache write failed for auth verification', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $userData;
    }

    /**
     * Validate that cached user data has valid (non-null) permissions
     */
    protected function hasValidPermissions(array $data): bool
    {
        $permissions = $data['user']['permissions'] ?? null;

        // No permissions key at all
        if ($permissions === null) {
            return false;
        }

        // Empty permissions is valid (user might genuinely have none)
        if (empty($permissions)) {
            return true;
        }

        // Check that permissions are strings, not nulls (from the old double-pluck bug)
        foreach ($permissions as $permission) {
            if (!is_string($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $userId, string $permission): bool
    {
        $cacheKey = "auth_perm_{$userId}_{$permission}";

        if ($this->cacheEnabled) {
            try {
                if (Cache::has($cacheKey)) {
                    return Cache::get($cacheKey);
                }
            } catch (\Throwable $e) {
                // Cache unavailable, proceed without it
            }
        }

        $response = $this->request('POST', '/service/check-permission', [
            'user_id' => $userId,
            'permission' => $permission,
        ]);

        $hasPermission = $response['success'] && ($response['data']['has_permission'] ?? false);

        if ($this->cacheEnabled) {
            try {
                Cache::put($cacheKey, $hasPermission, $this->cacheTtl);
            } catch (\Throwable $e) {
                // Cache write failed, continue
            }
        }

        return $hasPermission;
    }

    /**
     * Get user info
     */
    public function getUserInfo(string $userId): ?array
    {
        $cacheKey = "auth_user_{$userId}";

        if ($this->cacheEnabled) {
            try {
                if (Cache::has($cacheKey)) {
                    return Cache::get($cacheKey);
                }
            } catch (\Throwable $e) {
                // Cache unavailable, proceed without it
            }
        }

        $response = $this->request('POST', '/service/user-info', [
            'user_id' => $userId,
        ]);

        if (!$response['success']) {
            return null;
        }

        $userData = $response['data'];

        if ($this->cacheEnabled) {
            try {
                Cache::put($cacheKey, $userData, $this->cacheTtl);
            } catch (\Throwable $e) {
                // Cache write failed, continue
            }
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
        try {
            Cache::forget("auth_user_{$userId}");
        } catch (\Throwable $e) {
            // Cache unavailable
        }
    }

    /**
     * Clear token cache
     */
    public function clearTokenCache(string $token): void
    {
        try {
            Cache::forget('auth_verify_' . md5($token));
        } catch (\Throwable $e) {
            // Cache unavailable
        }
    }

    /**
     * Make HTTP request to Auth Service
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::withHeaders([
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
