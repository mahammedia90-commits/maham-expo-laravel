<?php

namespace App\Models;

use App\Enums\PaymentPlanStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPaymentPlan extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'installment_number',
        'label',
        'label_ar',
        'amount',
        'vat_amount',
        'total_amount',
        'due_date',
        'grace_period_days',
        'status',
        'paid_amount',
        'paid_at',
        'penalty_rate',
        'penalty_amount',
        'penalty_applied_at',
        'invoice_id',
        'invoice_number',
        'notes',
    ];

    protected $casts = [
        'status' => PaymentPlanStatus::class,
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'penalty_applied_at' => 'datetime',
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeOverdue($query)
    {
        return $query->where('status', PaymentPlanStatus::OVERDUE);
    }

    public function scopeDue($query)
    {
        return $query->where('status', PaymentPlanStatus::DUE);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', PaymentPlanStatus::UPCOMING);
    }
}
