<?php

namespace App\Models;

use App\Enums\OneSignalSubscriptionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneSignalSubscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'push_token',
        'type',
        'device_model',
        'device_os',
        'app_version',
        'enabled',
        'last_active_at',
    ];

    protected $casts = [
        'type' => OneSignalSubscriptionType::class,
        'enabled' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopePushTypes($query)
    {
        $types = array_map(fn($t) => $t->value, OneSignalSubscriptionType::pushTypes());
        return $query->whereIn('type', $types);
    }

    public function scopeMobileTypes($query)
    {
        $types = array_map(fn($t) => $t->value, OneSignalSubscriptionType::mobileTypes());
        return $query->whereIn('type', $types);
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function disable(): void
    {
        $this->update(['enabled' => false]);
    }

    public function enable(): void
    {
        $this->update(['enabled' => true]);
    }

    public function touchLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }
}
