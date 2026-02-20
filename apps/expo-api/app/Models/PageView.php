<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageView extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'platform',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForViewable($query, string $type, string $id)
    {
        return $query->where('viewable_type', $type)->where('viewable_id', $id);
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

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function track(Model $viewable, ?string $userId = null, string $platform = 'web'): self
    {
        return self::create([
            'viewable_type' => get_class($viewable),
            'viewable_id' => $viewable->id,
            'user_id' => $userId,
            'platform' => $platform,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
        ]);
    }
}
