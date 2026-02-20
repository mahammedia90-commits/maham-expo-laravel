<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $attributes = [
        'status' => 'pending',
        'payment_status' => 'pending',
        'paid_amount' => 0,
    ];

    protected $fillable = [
        'request_number',
        'space_id',
        'user_id',
        'business_profile_id',
        'start_date',
        'end_date',
        'total_price',
        'notes',
        'status',
        'payment_status',
        'paid_amount',
        'first_payment_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'investor_status',
        'investor_reviewed_by',
        'investor_reviewed_at',
        'investor_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'status' => RequestStatus::class,
        'payment_status' => PaymentStatus::class,
        'first_payment_at' => 'datetime',
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

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
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

    public function scopeForSpace($query, string $spaceId)
    {
        return $query->where('space_id', $spaceId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [RequestStatus::APPROVED, RequestStatus::COMPLETED])
            ->where('end_date', '>=', now()->toDateString());
    }

    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', PaymentStatus::PENDING);
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

    public function getPaymentStatusLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->payment_status->label()
            : $this->payment_status->labelEn();
    }

    public function getRentalDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    public function getCanBeModifiedAttribute(): bool
    {
        return $this->status->canBeModified();
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return $this->status->canBeCancelled()
            && $this->start_date > now()->toDateString();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public static function generateRequestNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.rental', 'RR');
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

        // Reserve the space
        $this->space->markAsReserved();
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

        // Check if space should be made available again
        $hasOtherActiveRentals = $this->space->rentalRequests()
            ->where('id', '!=', $this->id)
            ->whereIn('status', [RequestStatus::APPROVED, RequestStatus::COMPLETED])
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        if (!$hasOtherActiveRentals) {
            $this->space->markAsAvailable();
        }
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => RequestStatus::COMPLETED]);
        $this->space->markAsRented();
    }

    public function recordPayment(float $amount): void
    {
        $newPaidAmount = $this->paid_amount + $amount;

        $paymentStatus = PaymentStatus::PARTIAL;
        if ($newPaidAmount >= $this->total_price) {
            $paymentStatus = PaymentStatus::PAID;
            $newPaidAmount = $this->total_price;
        }

        $this->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $paymentStatus,
            'first_payment_at' => $this->first_payment_at ?? now(),
        ]);
    }
}
