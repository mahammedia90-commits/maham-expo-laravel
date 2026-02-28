<?php

namespace App\Services\OneSignal;

use Exception;

class OneSignalNotifier extends OneSignalService
{
    /**
     * Send a notification to specific users by their external user IDs.
     */
    public function sendToUsers(
        array $userIds,
        string $message,
        ?string $heading = null,
        ?array $data = null,
        ?array $options = []
    ): ?array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'OneSignal is disabled.'];
        }

        try {
            $payload = $this->buildNotificationPayload($message, $heading, $data, $options);

            // Target by external user IDs
            $payload['include_aliases'] = [
                'external_id' => array_map('strval', $userIds),
            ];
            $payload['target_channel'] = 'push';

            $response = $this->apiRequest('POST', '/api/v1/notifications', $payload);

            if ($response) {
                $this->logInfo('Notification sent to users', [
                    'user_ids' => $userIds,
                    'notification_id' => $response['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'id' => $response['id'] ?? null,
                    'recipients' => $response['recipients'] ?? 0,
                ];
            }

            return ['success' => false, 'error' => 'No response from OneSignal API'];
        } catch (Exception $e) {
            $this->logError('Failed to send notification to users', [
                'user_ids' => $userIds,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a notification to a single user
     */
    public function sendToUser(
        string $userId,
        string $message,
        ?string $heading = null,
        ?array $data = null,
        ?array $options = []
    ): ?array {
        return $this->sendToUsers([$userId], $message, $heading, $data, $options);
    }

    /**
     * Send a notification to a segment
     */
    public function sendToSegment(
        string $segment,
        string $message,
        ?string $heading = null,
        ?array $data = null,
        ?array $options = []
    ): ?array {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $payload = $this->buildNotificationPayload($message, $heading, $data, $options);
            $payload['included_segments'] = [$segment];

            $response = $this->apiRequest('POST', '/api/v1/notifications', $payload);

            if ($response) {
                $this->logInfo('Notification sent to segment', [
                    'segment' => $segment,
                    'notification_id' => $response['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'id' => $response['id'] ?? null,
                    'recipients' => $response['recipients'] ?? 0,
                ];
            }

            return null;
        } catch (Exception $e) {
            $this->logError('Failed to send notification to segment', [
                'segment' => $segment,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send to all users
     */
    public function sendToAll(
        string $message,
        ?string $heading = null,
        ?array $data = null,
        ?array $options = []
    ): ?array {
        return $this->sendToSegment(
            config('onesignal.default_segment', 'All'),
            $message,
            $heading,
            $data,
            $options
        );
    }

    /**
     * Send a data-only (silent) notification
     */
    public function sendSilentToUsers(
        array $userIds,
        array $data,
        ?string $message = null,
        ?string $heading = null,
        ?array $options = []
    ): ?array {
        $opts = array_merge($options ?? [], ['content_available' => true]);
        return $this->sendToUsers(
            $userIds,
            $message ?? ' ',
            $heading ?? ' ',
            array_merge($data, ['signal' => true]),
            $opts
        );
    }

    /**
     * Get notification details
     */
    public function getNotification(string $notificationId): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        return $this->apiRequest('GET', "/api/v1/notifications/{$notificationId}?app_id={$this->getAppId()}");
    }

    /**
     * Build notification payload for OneSignal REST API
     */
    protected function buildNotificationPayload(
        string $message,
        ?string $heading = null,
        ?array $data = null,
        ?array $options = []
    ): array {
        $payload = [
            'app_id' => $this->getAppId(),
            'contents' => ['en' => $message],
        ];

        // Arabic content
        if (!empty($options['ar'])) {
            $payload['contents']['ar'] = $options['ar'];
        }

        // Headings
        if ($heading) {
            $payload['headings'] = ['en' => $heading];
            if (!empty($options['heading_ar'])) {
                $payload['headings']['ar'] = $options['heading_ar'];
            }
        }

        // Data payload
        if ($data) {
            $payload['data'] = $data;
        }

        // URL
        if (!empty($options['url'])) {
            $payload['url'] = $options['url'];
        }

        // Icon
        if (!empty($options['icon'])) {
            $payload['small_icon'] = $options['icon'];
        }

        // Big picture
        if (!empty($options['image'])) {
            $payload['big_picture'] = $options['image'];
        }

        // Priority (0 = low, 10 = high)
        if (isset($options['priority'])) {
            $payload['priority'] = $options['priority'];
        }

        // Delayed send
        if (!empty($options['send_after'])) {
            $payload['send_after'] = $options['send_after'];
        }

        // TTL
        if (isset($options['ttl'])) {
            $payload['ttl'] = $options['ttl'];
        }

        // Content available (silent push)
        if (!empty($options['content_available'])) {
            $payload['content_available'] = true;
        }

        // Android channel
        if (!empty($options['android_channel_id'])) {
            $payload['android_channel_id'] = $options['android_channel_id'];
        }

        // iOS badge
        if (isset($options['ios_badge_type'])) {
            $payload['ios_badgeType'] = $options['ios_badge_type'];
            $payload['ios_badgeCount'] = $options['ios_badge_count'] ?? 1;
        }

        return $payload;
    }
}
