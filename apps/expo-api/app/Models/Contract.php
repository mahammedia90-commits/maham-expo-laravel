<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\UnifiedContractStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'unified_contracts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'contract_number',
        'parent_contract_id',
        // Type & Classification
        'type',
        'sub_type',
        'category',
        // Template
        'template_id',
        'template_version',
        // Titles & Descriptions
        'title',
        'title_ar',
        'description',
        'description_ar',
        // Content
        'content_html',
        'content_html_ar',
        'terms_and_conditions',
        'special_conditions',
        // Relations
        'event_id',
        'space_id',
        'section_id',
        'sponsor_package_id',
        // Financial
        'currency',
        'subtotal',
        'discount_amount',
        'discount_percentage',
        'taxable_amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'paid_amount',
        'penalty_amount',
        // Payment
        'payment_method',
        'installments_count',
        'payment_terms_days',
        // Dates
        'start_date',
        'end_date',
        'signing_deadline',
        // Status
        'status',
        'payment_status',
        // Legal Approval
        'legal_approved',
        'legal_approved_by',
        'legal_approved_at',
        'legal_notes',
        // Finance Approval
        'finance_approved',
        'finance_approved_by',
        'finance_approved_at',
        'finance_notes',
        // Final Approval
        'final_approved_by',
        'final_approved_at',
        // Signing
        'is_fully_signed',
        'signed_at',
        'signature_method',
        // Rejection
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        // Cancellation
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        // Suspension
        'suspension_reason',
        'suspended_by',
        'suspended_at',
        // Termination
        'termination_reason',
        'terminated_by',
        'terminated_at',
        // Renewal
        'is_renewable',
        'renewal_reminder_days',
        'auto_renew',
        'renewed_contract_id',
        // AI Analysis
        'ai_risk_score',
        'ai_risk_analysis',
        'ai_analyzed_at',
        // Meta
        'metadata',
        'internal_notes',
        'admin_notes',
        // Audit
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'type' => ContractType::class,
        'status' => UnifiedContractStatus::class,
        'terms_and_conditions' => 'array',
        'special_conditions' => 'array',
        'ai_risk_analysis' => 'array',
        'metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'signing_deadline' => 'date',
        'legal_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'signed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'suspended_at' => 'datetime',
        'terminated_at' => 'datetime',
        'ai_analyzed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'ai_risk_score' => 'decimal:2',
        'legal_approved' => 'boolean',
        'finance_approved' => 'boolean',
        'is_fully_signed' => 'boolean',
        'is_renewable' => 'boolean',
        'auto_renew' => 'boolean',
        'payment_status' => 'string',
    ];

    // ──────────────────────────────────────────
    // Boot
    // ──────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Contract $contract) {
            if (empty($contract->contract_number)) {
                $year = now()->format('Y');
                $sequence = static::withTrashed()
                    ->whereYear('created_at', $year)
                    ->count() + 1;
                $contract->contract_number = sprintf('CT-%s-%05d', $year, $sequence);
            }
        });
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function parties(): HasMany
    {
        return $this->hasMany(ContractParty::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContractVersion::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ContractSignature::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ContractAttachment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ContractStatusLog::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(ContractPaymentPlan::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(ContractReminder::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function parentContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'parent_contract_id');
    }

    public function renewedContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'renewed_contract_id');
    }

    public function template(): BelongsTo
    {
        // Note: ContractTemplate model needs to be created
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', UnifiedContractStatus::ACTIVE);
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->where('status', UnifiedContractStatus::ACTIVE)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    public function scopeByType($query, ContractType|string $type)
    {
        $value = $type instanceof ContractType ? $type->value : $type;

        return $query->where('type', $value);
    }

    public function scopeOverduePayments($query)
    {
        return $query->where('payment_status', 'overdue');
    }

    // ──────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────

    public function getRemainingAmountAttribute(): float
    {
        return round((float) $this->total_amount - (float) $this->paid_amount, 2);
    }

    public function getApprovalProgressAttribute(): array
    {
        return [
            'legal' => $this->legal_approved,
            'finance' => $this->finance_approved,
            'final' => ! is_null($this->final_approved_by),
            'percentage' => (int) round(
                (($this->legal_approved ? 1 : 0) +
                 ($this->finance_approved ? 1 : 0) +
                 (! is_null($this->final_approved_by) ? 1 : 0)) / 3 * 100
            ),
        ];
    }

    public function getIsOverdueAttribute(): bool
    {
        if (is_null($this->end_date)) {
            return false;
        }

        return $this->status === UnifiedContractStatus::ACTIVE
            && $this->end_date->isPast();
    }
}
