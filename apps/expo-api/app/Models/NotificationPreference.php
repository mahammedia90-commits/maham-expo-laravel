<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'email_enabled',
        'push_enabled',
        'in_app_enabled',
        'notify_request_updates',
        'notify_payment_reminders',
        'notify_event_updates',
        'notify_contract_milestones',
        'notify_promotions',
        'notify_support_updates',
        'notify_ratings',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'notify_request_updates' => 'boolean',
        'notify_payment_reminders' => 'boolean',
        'notify_event_updates' => 'boolean',
        'notify_contract_milestones' => 'boolean',
        'notify_promotions' => 'boolean',
        'notify_support_updates' => 'boolean',
        'notify_ratings' => 'boolean',
    ];

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function getOrCreateForUser(string $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => true,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ]
        );
    }
}
