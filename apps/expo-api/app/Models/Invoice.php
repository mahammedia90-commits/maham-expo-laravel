<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'invoiceable_type',
        'invoiceable_id',
        'title',
        'title_ar',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'items',
        'notes',
        'notes_ar',
        'created_by',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'items' => 'array',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            InvoiceStatus::ISSUED->value,
            InvoiceStatus::PARTIALLY_PAID->value,
            InvoiceStatus::OVERDUE->value,
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::OVERDUE)
            ->orWhere(function ($q) {
                $q->whereIn('status', [InvoiceStatus::ISSUED->value, InvoiceStatus::PARTIALLY_PAID->value])
                    ->where('due_date', '<', now()->toDateString());
            });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('invoice_number', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")
                ->orWhere('title_ar', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? $this->title) : $this->title;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now()->toDateString()
            && !in_array($this->status, [InvoiceStatus::PAID, InvoiceStatus::CANCELLED, InvoiceStatus::REFUNDED]);
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markAsPaid(string $paymentMethod, ?string $transactionRef = null): void
    {
        $this->update([
            'status' => InvoiceStatus::PAID,
            'paid_amount' => $this->total_amount,
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionRef,
        ]);
    }

    public function recordPayment(float $amount, string $paymentMethod, ?string $transactionRef = null): void
    {
        $newPaid = min((float) $this->paid_amount + $amount, (float) $this->total_amount);
        $status = $newPaid >= (float) $this->total_amount
            ? InvoiceStatus::PAID
            : InvoiceStatus::PARTIALLY_PAID;

        $this->update([
            'paid_amount' => $newPaid,
            'status' => $status,
            'paid_at' => $status === InvoiceStatus::PAID ? now() : null,
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionRef,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => InvoiceStatus::CANCELLED]);
    }

    public function issue(): void
    {
        $this->update(['status' => InvoiceStatus::ISSUED]);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function generateInvoiceNumber(): string
    {
        $prefix = config('expo-api.request_prefixes.invoice', 'INV');
        $date = now()->format('Ymd');
        $lastNumber = self::where('invoice_number', 'like', "{$prefix}-{$date}-%")
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }
}
