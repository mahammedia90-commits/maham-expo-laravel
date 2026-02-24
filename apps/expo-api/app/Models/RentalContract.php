<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContract extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'rental_request_id',
        'event_id',
        'space_id',
        'merchant_id',
        'investor_id',
        'start_date',
        'end_date',
        'total_amount',
        'paid_amount',
        'payment_status',
        'status',
        'terms',
        'terms_ar',
        'signed_at',
        'signed_by_merchant',
        'signed_by_investor',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'admin_notes',
        'documents',
    ];

    protected $casts = [
        'status' => ContractStatus::class,
        'payment_status' => PaymentStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'signed_at' => 'datetime',
        'approved_at' => 'datetime',
        'documents' => 'array',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $contract) {
            if (empty($contract->contract_number)) {
                $contract->contract_number = self::generateContractNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForMerchant($query, string $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeForInvestor($query, string $investorId)
    {
        return $query->where('investor_id', $investorId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ContractStatus::ACTIVE);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->active()
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('contract_number', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getPaymentProgressAttribute(): float
    {
        if ((float) $this->total_amount <= 0) {
            return 0;
        }
        return round(((float) $this->paid_amount / (float) $this->total_amount) * 100, 1);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    public function getIsSignedAttribute(): bool
    {
        return $this->signed_by_merchant && $this->signed_by_investor;
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function approve(string $approvedBy): void
    {
        $this->update([
            'status' => ContractStatus::ACTIVE,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject(string $reason, string $rejectedBy): void
    {
        $this->update([
            'status' => ContractStatus::CANCELLED,
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
        ]);
    }

    public function signByMerchant(string $merchantId): void
    {
        $this->update([
            'signed_by_merchant' => $merchantId,
            'signed_at' => $this->signed_by_investor ? now() : $this->signed_at,
        ]);
    }

    public function signByInvestor(string $investorId): void
    {
        $this->update([
            'signed_by_investor' => $investorId,
            'signed_at' => $this->signed_by_merchant ? now() : $this->signed_at,
        ]);
    }

    public function terminate(string $reason): void
    {
        $this->update([
            'status' => ContractStatus::TERMINATED,
            'admin_notes' => $reason,
        ]);
    }

    public function recordPayment(float $amount): void
    {
        $newPaidAmount = (float) $this->paid_amount + $amount;
        $totalAmount = (float) $this->total_amount;

        $paymentStatus = match (true) {
            $newPaidAmount >= $totalAmount => PaymentStatus::PAID,
            $newPaidAmount > 0 => PaymentStatus::PARTIAL,
            default => PaymentStatus::PENDING,
        };

        $this->update([
            'paid_amount' => min($newPaidAmount, $totalAmount),
            'payment_status' => $paymentStatus,
        ]);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function generateContractNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.rental_contract', 'RC');
        $date = now()->format('Ymd');
        $lastNumber = self::where('contract_number', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('contract_number')
            ->value('contract_number');

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    public static function createFromRentalRequest(RentalRequest $rentalRequest, array $extraData = []): self
    {
        return self::create(array_merge([
            'rental_request_id' => $rentalRequest->id,
            'event_id' => $rentalRequest->event_id,
            'space_id' => $rentalRequest->space_id,
            'merchant_id' => $rentalRequest->user_id,
            'investor_id' => $rentalRequest->investor_id ?? $extraData['investor_id'] ?? null,
            'start_date' => $rentalRequest->start_date,
            'end_date' => $rentalRequest->end_date,
            'total_amount' => $rentalRequest->total_price,
            'status' => ContractStatus::DRAFT,
        ], $extraData));
    }
}
