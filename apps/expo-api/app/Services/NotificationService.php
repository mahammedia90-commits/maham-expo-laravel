<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Services\OneSignal\OneSignalNotifier;
use Illuminate\Support\Facades\Log;

/**
 * Unified Notification Service
 *
 * Bridges in-app notifications (database) with push notifications (OneSignal).
 * Respects user notification preferences before sending.
 */
class NotificationService
{
    protected OneSignalNotifier $pushNotifier;

    public function __construct(OneSignalNotifier $pushNotifier)
    {
        $this->pushNotifier = $pushNotifier;
    }

    /**
     * Send a notification to a user (both in-app + push)
     *
     * @param string $userId      UUID of the target user
     * @param string $title       English title
     * @param string $titleAr     Arabic title
     * @param string $type        Notification type (e.g., 'visit_request_approved')
     * @param string|null $body   English body text
     * @param string|null $bodyAr Arabic body text
     * @param array|null $data    Extra data payload
     * @param string|null $actionUrl  URL for the notification action
     */
    public function send(
        string $userId,
        string $title,
        string $titleAr,
        string $type,
        ?string $body = null,
        ?string $bodyAr = null,
        ?array $data = null,
        ?string $actionUrl = null
    ): ?Notification {
        // Get user's notification preferences
        $preferences = NotificationPreference::getOrCreateForUser($userId);

        // Determine the notification category
        $category = $this->getNotificationCategory($type);

        // Check if user wants this category of notification
        if (!$this->shouldNotifyCategory($preferences, $category)) {
            Log::debug('[NotificationService] Skipped notification — user disabled category', [
                'user_id' => $userId,
                'type' => $type,
                'category' => $category,
            ]);
            return null;
        }

        $notification = null;

        // Save in-app notification (if enabled)
        if ($preferences->in_app_enabled) {
            $notification = Notification::send(
                userId: $userId,
                title: $title,
                titleAr: $titleAr,
                type: $type,
                body: $body,
                bodyAr: $bodyAr,
                data: $data,
                actionUrl: $actionUrl
            );
        }

        // Send push notification (if enabled)
        if ($preferences->push_enabled) {
            $this->sendPush(
                userId: $userId,
                title: $title,
                titleAr: $titleAr,
                body: $body,
                bodyAr: $bodyAr,
                type: $type,
                data: $data,
                actionUrl: $actionUrl
            );
        }

        return $notification;
    }

    /**
     * Send push notification via OneSignal
     */
    protected function sendPush(
        string $userId,
        string $title,
        string $titleAr,
        ?string $body,
        ?string $bodyAr,
        string $type,
        ?array $data,
        ?string $actionUrl
    ): void {
        try {
            $pushData = array_filter([
                'type' => $type,
                'action_url' => $actionUrl,
                ...(is_array($data) ? $data : []),
            ]);

            $this->pushNotifier->sendToUsers(
                userIds: [$userId],
                message: $body ?? $title,
                heading: $title,
                data: $pushData ?: null,
                options: array_filter([
                    'ar' => $bodyAr ?? $titleAr,
                    'heading_ar' => $titleAr,
                    'url' => $actionUrl,
                ])
            );
        } catch (\Exception $e) {
            // Push failure should never block in-app notification
            Log::warning('[NotificationService] Push notification failed', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMany(
        array $userIds,
        string $title,
        string $titleAr,
        string $type,
        ?string $body = null,
        ?string $bodyAr = null,
        ?array $data = null,
        ?string $actionUrl = null
    ): void {
        foreach ($userIds as $userId) {
            $this->send($userId, $title, $titleAr, $type, $body, $bodyAr, $data, $actionUrl);
        }
    }

    /**
     * Send a push-only notification (no database record)
     */
    public function sendPushOnly(
        string $userId,
        string $title,
        string $titleAr,
        ?string $body = null,
        ?string $bodyAr = null,
        string $type = 'push',
        ?array $data = null,
        ?string $actionUrl = null
    ): void {
        $preferences = NotificationPreference::getOrCreateForUser($userId);

        if (!$preferences->push_enabled) {
            return;
        }

        $this->sendPush($userId, $title, $titleAr, $body, $bodyAr, $type, $data, $actionUrl);
    }

    /**
     * Map notification type to preference category
     */
    protected function getNotificationCategory(string $type): string
    {
        return match (true) {
            // Request updates
            str_contains($type, 'visit_request') => 'request_updates',
            str_contains($type, 'rental_request') => 'request_updates',
            str_contains($type, 'profile') => 'request_updates',

            // Payment reminders
            str_contains($type, 'payment') => 'payment_reminders',
            str_contains($type, 'invoice') => 'payment_reminders',

            // Event updates
            str_contains($type, 'event') => 'event_updates',

            // Contract milestones
            str_contains($type, 'contract') => 'contract_milestones',
            str_contains($type, 'rental_contract') => 'contract_milestones',
            str_contains($type, 'sponsor_contract') => 'contract_milestones',

            // Promotions
            str_contains($type, 'promotion') => 'promotions',
            str_contains($type, 'offer') => 'promotions',

            // Support
            str_contains($type, 'support') => 'support_updates',
            str_contains($type, 'ticket') => 'support_updates',

            // Ratings
            str_contains($type, 'rating') => 'ratings',

            // Default — always send
            default => 'general',
        };
    }

    /**
     * Check if user wants notifications for this category
     */
    protected function shouldNotifyCategory(NotificationPreference $prefs, string $category): bool
    {
        return match ($category) {
            'request_updates' => $prefs->notify_request_updates,
            'payment_reminders' => $prefs->notify_payment_reminders,
            'event_updates' => $prefs->notify_event_updates,
            'contract_milestones' => $prefs->notify_contract_milestones,
            'promotions' => $prefs->notify_promotions,
            'support_updates' => $prefs->notify_support_updates,
            'ratings' => $prefs->notify_ratings,
            default => true, // Unknown categories always allowed
        };
    }
}
