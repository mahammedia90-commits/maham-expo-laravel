<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'message_ar',
        'is_staff_reply',
        'attachments',
    ];

    protected $casts = [
        'is_staff_reply' => 'boolean',
        'attachments' => 'array',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedMessageAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->message_ar ?? $this->message) : $this->message;
    }
}
