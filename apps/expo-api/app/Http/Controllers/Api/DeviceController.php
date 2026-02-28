<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OneSignalSubscription;
use App\Services\OneSignal\OneSignalUserManager;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(
        protected OneSignalUserManager $userManager
    ) {}

    /**
     * Register a device for push notifications
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'push_token' => 'required|string|max:512',
            'device_os' => 'nullable|string|max:50',
            'device_model' => 'nullable|string|max:100',
            'app_version' => 'nullable|string|max:20',
        ]);

        $userId = $request->input('auth_user_id');

        // Build a fake User object with just the ID (since users live in auth service)
        $user = new \App\Models\User();
        $user->id = $userId;
        $user->exists = true;

        $subscription = $this->userManager->registerUserSubscription(
            user: $user,
            pushToken: $validated['push_token'],
            type: $this->userManager->detectSubscriptionTypeEnum(
                $validated['device_os'] ?? null
            ),
            deviceInfo: [
                'device_model' => $validated['device_model'] ?? null,
                'device_os' => $validated['device_os'] ?? null,
                'app_version' => $validated['app_version'] ?? null,
            ]
        );

        if (!$subscription) {
            // Even if OneSignal API fails, store locally for retry
            $subscription = $this->storeLocalSubscription($userId, $validated);
        }

        return ApiResponse::success(
            data: [
                'subscription_id' => $subscription->subscription_id ?? null,
                'type' => $subscription->type?->value ?? null,
                'enabled' => $subscription->enabled ?? true,
            ],
            message: __('messages.device.registered'),
            httpCode: 201
        );
    }

    /**
     * Unregister a device (disable push notifications)
     */
    public function unregister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'push_token' => 'required|string|max:512',
        ]);

        $userId = $request->input('auth_user_id');

        $subscription = OneSignalSubscription::where('user_id', $userId)
            ->where('push_token', $validated['push_token'])
            ->first();

        if (!$subscription) {
            return ApiResponse::error(
                message: __('messages.device.not_found'),
                errorCode: ApiErrorCode::NOT_FOUND,
                httpCode: 404
            );
        }

        // Try to delete from OneSignal
        if ($subscription->subscription_id) {
            $this->userManager->deleteSubscription($subscription->subscription_id);
        } else {
            $subscription->delete();
        }

        return ApiResponse::success(
            message: __('messages.device.unregistered')
        );
    }

    /**
     * List current user's registered devices
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $subscriptions = OneSignalSubscription::forUser($userId)
            ->enabled()
            ->latest()
            ->get()
            ->map(fn($sub) => [
                'id' => $sub->id,
                'type' => $sub->type?->value,
                'device_model' => $sub->device_model,
                'device_os' => $sub->device_os,
                'app_version' => $sub->app_version,
                'last_active_at' => $sub->last_active_at?->toISOString(),
                'created_at' => $sub->created_at->toISOString(),
            ]);

        return ApiResponse::success(data: $subscriptions);
    }

    /**
     * Store subscription locally when OneSignal API is unavailable
     */
    private function storeLocalSubscription(string $userId, array $data): OneSignalSubscription
    {
        $type = (new OneSignalUserManager())->detectSubscriptionTypeEnum(
            $data['device_os'] ?? null
        );

        return OneSignalSubscription::updateOrCreate(
            [
                'user_id' => $userId,
                'push_token' => $data['push_token'],
            ],
            [
                'subscription_id' => 'pending_' . uniqid(),
                'type' => $type,
                'device_model' => $data['device_model'] ?? null,
                'device_os' => $data['device_os'] ?? null,
                'app_version' => $data['app_version'] ?? null,
                'enabled' => true,
                'last_active_at' => now(),
            ]
        );
    }
}
