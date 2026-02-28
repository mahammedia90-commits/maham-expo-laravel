<?php

namespace App\Services\OneSignal;

use App\Models\User;
use App\Models\OneSignalSubscription;
use App\Enums\OneSignalSubscriptionType;
use Exception;
use Illuminate\Http\Request;

class OneSignalUserManager extends OneSignalService
{
    /**
     * Register or update a user and their subscription in OneSignal.
     */
    public function registerUserSubscription(
        User $user,
        string $pushToken,
        OneSignalSubscriptionType $type = OneSignalSubscriptionType::ANDROID_PUSH,
        ?array $deviceInfo = null
    ): ?OneSignalSubscription {
        $subscriptionId = null;

        if ($this->isEnabled()) {
            try {
                // Create or update user in OneSignal via REST API v2
                $payload = [
                    'identity' => [
                        'external_id' => (string) $user->id,
                    ],
                    'subscriptions' => [
                        array_filter([
                            'type' => $type->value,
                            'token' => $pushToken,
                            'device_model' => $deviceInfo['device_model'] ?? null,
                            'device_os' => $deviceInfo['device_os'] ?? null,
                            'app_version' => $deviceInfo['app_version'] ?? null,
                        ]),
                    ],
                ];

                $response = $this->apiRequest('POST', "/api/v2/users", $payload);

                // Extract the subscription ID from the response
                $subscriptionId = $response['subscriptions'][0]['id']
                    ?? $response['identity']['onesignal_id']
                    ?? null;

                $this->logInfo('User subscription registered in OneSignal', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscriptionId,
                    'type' => $type->value,
                ]);
            } catch (Exception $e) {
                $this->logError('Failed to register user subscription in OneSignal (will store locally)', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Always store locally, even if OneSignal API fails
        $localSubscription = OneSignalSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'push_token' => $pushToken,
            ],
            [
                'subscription_id' => $subscriptionId ?? ('pending_' . uniqid()),
                'type' => $type,
                'device_model' => $deviceInfo['device_model'] ?? null,
                'device_os' => $deviceInfo['device_os'] ?? null,
                'app_version' => $deviceInfo['app_version'] ?? null,
                'enabled' => true,
                'last_active_at' => now(),
            ]
        );

        return $localSubscription;
    }

    /**
     * Register subscription from HTTP request.
     * Auto-detects subscription type from device_os.
     */
    public function registerFromRequest(Request $request, User $user): ?OneSignalSubscription
    {
        $pushToken = $request->input('push_token');
        $deviceOs = $request->input('device_os');

        $type = $this->detectSubscriptionTypeEnum($deviceOs);

        $deviceInfo = [
            'device_model' => $request->input('device_model'),
            'device_os' => $deviceOs,
            'app_version' => $request->input('app_version'),
        ];

        return $this->registerUserSubscription($user, $pushToken, $type, $deviceInfo);
    }

    /**
     * Detect subscription type enum from device OS string.
     * Public so DeviceController can call it directly.
     */
    public function detectSubscriptionTypeEnum(?string $deviceOs): OneSignalSubscriptionType
    {
        if (!$deviceOs) {
            return OneSignalSubscriptionType::ANDROID_PUSH;
        }

        $deviceOs = strtolower($deviceOs);

        if (str_contains($deviceOs, 'ios') || str_contains($deviceOs, 'iphone') || str_contains($deviceOs, 'ipad')) {
            return OneSignalSubscriptionType::IOS_PUSH;
        }

        if (str_contains($deviceOs, 'android')) {
            return OneSignalSubscriptionType::ANDROID_PUSH;
        }

        if (str_contains($deviceOs, 'chrome') || str_contains($deviceOs, 'firefox') ||
            str_contains($deviceOs, 'safari') || str_contains($deviceOs, 'edge') ||
            str_contains($deviceOs, 'web')) {
            return OneSignalSubscriptionType::WEB_PUSH;
        }

        return OneSignalSubscriptionType::ANDROID_PUSH;
    }

    /**
     * Update user tags / properties in OneSignal.
     */
    public function updateUserProperties(User $user, array $properties): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $payload = [
                'properties' => [
                    'tags' => $properties,
                ],
            ];

            $this->apiRequest(
                'PATCH',
                "/api/v2/users/by/external_id/" . urlencode((string) $user->id),
                $payload
            );

            $this->logInfo('User properties updated in OneSignal', [
                'user_id' => $user->id,
                'properties' => $properties,
            ]);

            return true;
        } catch (Exception $e) {
            $this->logError('Failed to update user properties', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Delete a subscription from OneSignal and the local database.
     */
    public function deleteSubscription(string $subscriptionId): bool
    {
        // Delete from local DB first
        OneSignalSubscription::where('subscription_id', $subscriptionId)->delete();

        if (!$this->isEnabled() || str_starts_with($subscriptionId, 'pending_')) {
            return true;
        }

        try {
            $this->apiRequest('DELETE', "/api/v2/subscriptions/{$subscriptionId}");

            $this->logInfo('Subscription deleted from OneSignal', ['subscription_id' => $subscriptionId]);

            return true;
        } catch (Exception $e) {
            $this->logError('Failed to delete subscription from OneSignal (local record already removed)', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
            return true; // local already deleted
        }
    }

    /**
     * Delete all subscriptions for a user
     */
    public function deleteAllUserSubscriptions(User $user): bool
    {
        try {
            $subscriptions = OneSignalSubscription::forUser($user->id)->get();

            foreach ($subscriptions as $subscription) {
                $this->deleteSubscription($subscription->subscription_id);
            }

            $this->logInfo('All user subscriptions deleted', ['user_id' => $user->id]);
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to delete all user subscriptions', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Disable all subscriptions for a user (soft-disable)
     */
    public function disableAllUserSubscriptions(User $user): bool
    {
        try {
            OneSignalSubscription::forUser($user->id)->update(['enabled' => false]);

            $this->logInfo('All user subscriptions disabled', ['user_id' => $user->id]);
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to disable user subscriptions', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Fetch user data from OneSignal
     */
    public function getUser(User $user): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            return $this->apiRequest(
                'GET',
                "/api/v2/users/by/external_id/" . urlencode((string) $user->id)
            );
        } catch (Exception $e) {
            $this->logError('Failed to get user from OneSignal', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
