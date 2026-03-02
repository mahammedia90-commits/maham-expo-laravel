<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    protected string $prefix;
    protected int $ttl;

    public function __construct()
    {
        $this->prefix = config('auth-service.cache.prefix', 'auth_');
        $this->ttl = config('auth-service.cache.ttl.service', 3600);
    }

    /**
     * Get cached service data by token
     */
    public function getService(string $token): ?array
    {
        try {
            $key = $this->prefix . 'service_' . md5($token);
            return Cache::get($key);
        } catch (\Throwable $e) {
            Log::warning('Cache unavailable for service lookup', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cache service data
     */
    public function cacheService(string $token, array $data): void
    {
        try {
            $key = $this->prefix . 'service_' . md5($token);
            Cache::put($key, $data, $this->ttl);
        } catch (\Throwable $e) {
            Log::warning('Cache write failed for service', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove cached service
     */
    public function forgetService(string $token): void
    {
        try {
            $key = $this->prefix . 'service_' . md5($token);
            Cache::forget($key);
        } catch (\Throwable $e) {
            // Cache unavailable
        }
    }

    /**
     * Clear all service caches
     */
    public function clearAllServices(): void
    {
        try {
            // Since we can't easily list keys, we just log the intent
            Log::info('Service cache clear requested');
        } catch (\Throwable $e) {
            // Cache unavailable
        }
    }
}
