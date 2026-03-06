<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'user_id',
        'payable_type',
        'payable_id',
        'charge_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'source',
        'gateway_reference',
        'payment_reference',
        'track_id',
        'transaction_url',
        'three_d_secure',
        'redirect_status',
        'webhook_status',
        'error_code',
        'error_message',
        'tap_response',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'three_d_secure' => 'boolean',
        'tap_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_INITIATED = 'initiated';
    const STATUS_PENDING = 'pending';
    const STATUS_CAPTURED = 'captured';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_ABANDONED = 'abandoned';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = self::generatePaymentNumber();
            }
        });
    }

    /**
     * Polymorphic relation to payable (Invoice, RentalContract, etc.)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCaptured($query)
    {
        return $query->where('status', self::STATUS_CAPTURED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_INITIATED, self::STATUS_PENDING]);
    }

    // ─── Status Helpers ──────────────────────────────────────

    public function isCaptured(): bool
    {
        return $this->status === self::STATUS_CAPTURED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_INITIATED, self::STATUS_PENDING]);
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark payment as captured (successful)
     */
    public function markCaptured(array $tapData = []): void
    {
        $this->update([
            'status' => self::STATUS_CAPTURED,
            'charge_id' => $tapData['id'] ?? $this->charge_id,
            'payment_method' => $tapData['source']['payment_method'] ?? $this->payment_method,
            'gateway_reference' => $tapData['reference']['gateway'] ?? $this->gateway_reference,
            'payment_reference' => $tapData['reference']['payment'] ?? $this->payment_reference,
            'track_id' => $tapData['reference']['track'] ?? $this->track_id,
            'tap_response' => $tapData,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markFailed(array $tapData = []): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_code' => $tapData['response']['code'] ?? null,
            'error_message' => $tapData['response']['message'] ?? null,
            'tap_response' => $tapData,
        ]);
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $lastNumber = self::where('payment_number', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('payment_number')
            ->value('payment_number');

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }
}
