<?php

namespace App\Models;

use App\Enums\SponsorContractStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorContract extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $attributes = [
        'status' => 'draft',
        'payment_status' => 'pending',
        'paid_amount' => 0,
    ];

    protected $fillable = [
        'sponsor_id',
        'sponsor_package_id',
        'event_id',
        'contract_number',
        'start_date',
        'end_date',
        'total_amount',
        'paid_amount',
        'payment_status',
        'status',
        'terms',
        'terms_ar',
        'signed_at',
        'signed_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'status' => SponsorContractStatus::class,
        'signed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->contract_number)) {
                $model->contract_number = self::generateContractNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function sponsorPackage(): BelongsTo
    {
        return $this->belongsTo(SponsorPackage::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SponsorPayment::class);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(SponsorBenefit::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('status', SponsorContractStatus::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', SponsorContractStatus::PENDING);
    }

    public function scopeForSponsor($query, string $sponsorId)
    {
        return $query->where('sponsor_id', $sponsorId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
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
        $labels = [
            'pending' => app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending',
            'partial' => app()->getLocale() === 'ar' ? 'مدفوع جزئياً' : 'Partially Paid',
            'paid' => app()->getLocale() === 'ar' ? 'مدفوع بالكامل' : 'Fully Paid',
            'refunded' => app()->getLocale() === 'ar' ? 'مسترد' : 'Refunded',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getContractDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getCanBeModifiedAttribute(): bool
    {
        return $this->status->canBeModified();
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return $this->status->canBeCancelled();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public static function generateContractNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.sponsor_contract', 'SC');
        $date = now()->format('Ymd');

        $lastNumber = self::where('contract_number', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('contract_number')
            ->value('contract_number');

        $sequence = 1;
        if ($lastNumber && preg_match('/(\d+)$/', $lastNumber, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function approve(string $reviewerId, ?string $notes = null): void
    {
        $this->update([
            'status' => SponsorContractStatus::ACTIVE,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
            'rejection_reason' => null,
        ]);

        // Activate sponsor if not already active
        if ($this->sponsor->status !== \App\Enums\SponsorStatus::ACTIVE) {
            $this->sponsor->activate();
        }
    }

    public function reject(string $reviewerId, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => SponsorContractStatus::CANCELLED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }

    public function complete(): void
    {
        $this->update(['status' => SponsorContractStatus::COMPLETED]);
    }

    public function cancel(): void
    {
        $this->update(['status' => SponsorContractStatus::CANCELLED]);
    }

    public function recordPayment(float $amount): void
    {
        $newPaidAmount = $this->paid_amount + $amount;

        $paymentStatus = 'partial';
        if ($newPaidAmount >= $this->total_amount) {
            $paymentStatus = 'paid';
            $newPaidAmount = $this->total_amount;
        }

        $this->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $paymentStatus,
        ]);
    }
}
