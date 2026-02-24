<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'subject',
        'subject_ar',
        'description',
        'description_ar',
        'category',
        'priority',
        'status',
        'assigned_to',
        'related_id',
        'related_type',
        'attachments',
        'resolved_at',
        'closed_at',
        'resolved_by',
    ];

    protected $casts = [
        'category' => TicketCategory::class,
        'priority' => TicketPriority::class,
        'status' => TicketStatus::class,
        'attachments' => 'array',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            TicketStatus::OPEN->value,
            TicketStatus::IN_PROGRESS->value,
            TicketStatus::WAITING_REPLY->value,
        ]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [
            TicketStatus::RESOLVED->value,
            TicketStatus::CLOSED->value,
        ]);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOfPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, string $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('subject', 'like', "%{$term}%")
                ->orWhere('subject_ar', 'like', "%{$term}%")
                ->orWhere('ticket_number', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedSubjectAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->subject_ar ?? $this->subject) : $this->subject;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status->isActive();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markInProgress(string $assignedTo): void
    {
        $this->update([
            'status' => TicketStatus::IN_PROGRESS,
            'assigned_to' => $assignedTo,
        ]);
    }

    public function markWaitingReply(): void
    {
        $this->update(['status' => TicketStatus::WAITING_REPLY]);
    }

    public function resolve(string $resolvedBy): void
    {
        $this->update([
            'status' => TicketStatus::RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy,
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => TicketStatus::CLOSED,
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => TicketStatus::OPEN,
            'resolved_at' => null,
            'resolved_by' => null,
            'closed_at' => null,
        ]);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function generateTicketNumber(): string
    {
        $prefix = 'TK';
        $date = now()->format('Ymd');
        $lastNumber = self::where('ticket_number', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }
}
