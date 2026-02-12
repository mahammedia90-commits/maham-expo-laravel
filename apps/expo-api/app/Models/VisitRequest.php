<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'request_number',
        'event_id',
        'user_id',
        'visit_date',
        'visit_time',
        'visitors_count',
        'notes',
        'contact_phone',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime:H:i',
        'visitors_count' => 'integer',
        'status' => RequestStatus::class,
        'reviewed_at' => 'datetime',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopePending($query)
    {
        return $query->where('status', RequestStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', RequestStatus::APPROVED);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('visit_date', '>=', now()->toDateString());
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getStatusLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->status->label()
            : $this->status->labelEn();
    }

    public function getCanBeModifiedAttribute(): bool
    {
        return $this->status->canBeModified();
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return $this->status->canBeCancelled()
            && $this->visit_date >= now()->toDateString();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public static function generateRequestNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.visit', 'VR');
        $date = now()->format('Ymd');

        $lastRequest = self::whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastRequest && preg_match('/(\d+)$/', $lastRequest->request_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function approve(string $reviewerId, ?string $notes = null): void
    {
        $this->update([
            'status' => RequestStatus::APPROVED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $reviewerId, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => RequestStatus::REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => RequestStatus::CANCELLED]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => RequestStatus::COMPLETED]);
    }
}
