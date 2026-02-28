<?php

namespace App\Services\OneSignal;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    protected string $appId;
    protected string $restApiKey;
    protected string $apiUrl;
    protected bool $enabled;

    public function __construct()
    {
        $this->appId = config('onesignal.app_id', '');
        $this->restApiKey = config('onesignal.rest_api_key', '');
        $this->apiUrl = rtrim(config('onesignal.api_url', 'https://api.onesignal.com'), '/');
        $this->enabled = config('onesignal.enabled', true);
    }

    /**
     * Get the app ID
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * Check if OneSignal is enabled and properly configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->appId) && !empty($this->restApiKey);
    }

    /**
     * Make an API request to OneSignal REST API
     */
    protected function apiRequest(string $method, string $endpoint, array $data = []): ?array
    {
        try {
            $request = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(10);

            $response = match (strtoupper($method)) {
                'GET' => $request->get($this->apiUrl . $endpoint, $data),
                'POST' => $request->post($this->apiUrl . $endpoint, $data),
                'PUT' => $request->put($this->apiUrl . $endpoint, $data),
                'DELETE' => $request->delete($this->apiUrl . $endpoint, $data),
                default => $request->post($this->apiUrl . $endpoint, $data),
            };

            if ($response->successful()) {
                return $response->json();
            }

            $this->logError('API request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            $this->logError('API request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Log errors to Laravel log
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error('[OneSignal] ' . $message, $context);
    }

    /**
     * Log info to Laravel log
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info('[OneSignal] ' . $message, $context);
    }
}
