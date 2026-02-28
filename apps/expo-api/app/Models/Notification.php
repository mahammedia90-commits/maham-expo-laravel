<?php

namespace App\Models;

use App\Services\OneSignal\OneSignalNotifier;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'title_ar',
        'body',
        'body_ar',
        'type',
        'data',
        'action_url',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Boot the model — fire push notification on creation.
     */
    protected static function booted(): void
    {
        static::created(function (Notification $notification) {
            try {
                $notifier = app(OneSignalNotifier::class);

                if (!$notifier->isEnabled()) {
                    return;
                }

                // Check user push preference
                $preferences = \App\Models\NotificationPreference::getOrCreateForUser($notification->user_id);
                if (!$preferences->push_enabled) {
                    return;
                }

                $pushData = array_filter([
                    'type' => $notification->type,
                    'notification_id' => $notification->id,
                    'action_url' => $notification->action_url,
                    ...(is_array($notification->data) ? $notification->data : []),
                ]);

                $notifier->sendToUser(
                    userId: $notification->user_id,
                    message: $notification->body ?? $notification->title,
                    heading: $notification->title,
                    data: $pushData ?: null,
                    options: array_filter([
                        'ar' => $notification->body_ar ?? $notification->title_ar,
                        'heading_ar' => $notification->title_ar,
                        'url' => $notification->action_url,
                    ])
                );
            } catch (\Exception $e) {
                // Push failure should never prevent in-app notification creation
                Log::warning('[Notification] Push notification failed', [
                    'notification_id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title;
    }

    public function getLocalizedBodyAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->body_ar : $this->body;
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function send(
        string $userId,
        string $title,
        string $titleAr,
        string $type,
        ?string $body = null,
        ?string $bodyAr = null,
        ?array $data = null,
        ?string $actionUrl = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'title_ar' => $titleAr,
            'body' => $body,
            'body_ar' => $bodyAr,
            'type' => $type,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }

    public static function markAllAsReadForUser(string $userId): int
    {
        return self::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public static function getUnreadCountForUser(string $userId): int
    {
        return self::forUser($userId)->unread()->count();
    }
}
