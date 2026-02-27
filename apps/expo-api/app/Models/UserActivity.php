<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'platform',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
        'session_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /* ========================================
     * Allowed values
     * ======================================== */

    public const ACTIONS = [
        'view',
        'search',
        'click',
        'share',
        'filter',
        'download',
        'favorite',
        'unfavorite',
        'apply',
        'submit',
        'page_enter',
        'page_exit',
    ];

    public const TRACKABLE_TYPES = [
        'event'            => \App\Models\Event::class,
        'space'            => \App\Models\Space::class,
        'section'          => \App\Models\Section::class,
        'sponsor'          => \App\Models\Sponsor::class,
        'page'             => \App\Models\Page::class,
        'banner'           => \App\Models\Banner::class,
        'faq'              => \App\Models\Faq::class,
        'service'          => \App\Models\Service::class,
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function resource(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'resource_type', 'resource_id');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeForResource($query, string $type, ?string $id = null)
    {
        $query->where('resource_type', $type);
        if ($id) {
            $query->where('resource_id', $id);
        }
        return $query;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->where('created_at', '>=', now()->startOfWeek());
    }

    public function scopeThisMonth($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }
        return $query;
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    /**
     * Record a user activity
     */
    public static function record(
        string $action,
        ?string $userId = null,
        ?string $resourceType = null,
        ?string $resourceId = null,
        string $platform = 'web',
        ?array $metadata = null,
        ?string $sessionId = null,
    ): self {
        return self::create([
            'user_id'       => $userId,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'platform'      => $platform,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'referrer'      => request()->headers->get('referer'),
            'metadata'      => $metadata,
            'session_id'    => $sessionId,
        ]);
    }

    /**
     * Resolve short resource type to full class name
     */
    public static function resolveResourceType(?string $type): ?string
    {
        if (!$type) {
            return null;
        }

        return self::TRACKABLE_TYPES[$type] ?? null;
    }
}
