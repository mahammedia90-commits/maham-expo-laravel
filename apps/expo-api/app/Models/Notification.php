<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
