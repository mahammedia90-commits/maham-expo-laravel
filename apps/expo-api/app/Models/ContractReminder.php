<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractReminder extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'type',
        'remind_at',
        'reminded_at',
        'is_sent',
        'target_user_id',
        'target_role',
        'title',
        'title_ar',
        'message',
        'message_ar',
        'is_recurring',
        'recurrence_days',
        'max_reminders',
        'reminder_count',
        'created_at',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'reminded_at' => 'datetime',
        'is_sent' => 'boolean',
        'is_recurring' => 'boolean',
        'created_at' => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('is_sent', false)
            ->where('remind_at', '<=', now());
    }
}
