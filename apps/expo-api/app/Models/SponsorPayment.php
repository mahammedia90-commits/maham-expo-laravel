<?php

namespace App\Models;

use App\Enums\SponsorPaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorPayment extends Model
{
    use HasFactory, HasUuids;

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'sponsor_contract_id',
        'payment_number',
        'amount',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'status' => SponsorPaymentStatus::class,
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->payment_number)) {
                $model->payment_number = self::generatePaymentNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SponsorContract::class, 'sponsor_contract_id');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopePending($query)
    {
        return $query->where('status', SponsorPaymentStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', SponsorPaymentStatus::PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', SponsorPaymentStatus::OVERDUE);
    }

    public function scopeForContract($query, string $contractId)
    {
        return $query->where('sponsor_contract_id', $contractId);
    }

    public function scopeDueBefore($query, string $date)
    {
        return $query->where('due_date', '<=', $date);
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

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === SponsorPaymentStatus::PENDING
            && $this->due_date->isPast();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public static function generatePaymentNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.sponsor_payment', 'SP');
        $date = now()->format('Ymd');

        $lastPayment = self::whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastPayment && preg_match('/(\d+)$/', $lastPayment->payment_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public function markAsPaid(?string $paymentMethod = null, ?string $transactionRef = null): void
    {
        $this->update([
            'status' => SponsorPaymentStatus::PAID,
            'paid_at' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'transaction_reference' => $transactionRef ?? $this->transaction_reference,
        ]);

        // Update contract paid amount
        $this->contract->recordPayment($this->amount);
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => SponsorPaymentStatus::OVERDUE]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => SponsorPaymentStatus::CANCELLED]);
    }
}
