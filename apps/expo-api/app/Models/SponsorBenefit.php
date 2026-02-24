<?php

namespace App\Models;

use App\Enums\SponsorBenefitStatus;
use App\Enums\SponsorBenefitType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorBenefit extends Model
{
    use HasFactory, HasUuids;

    protected $attributes = [
        'quantity' => 1,
        'delivered_quantity' => 0,
        'status' => 'pending',
    ];

    protected $fillable = [
        'sponsor_contract_id',
        'benefit_type',
        'description',
        'description_ar',
        'quantity',
        'delivered_quantity',
        'status',
        'delivery_notes',
    ];

    protected $casts = [
        'benefit_type' => SponsorBenefitType::class,
        'status' => SponsorBenefitStatus::class,
        'quantity' => 'integer',
        'delivered_quantity' => 'integer',
    ];

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
        return $query->where('status', SponsorBenefitStatus::PENDING);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', SponsorBenefitStatus::DELIVERED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', SponsorBenefitStatus::IN_PROGRESS);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('benefit_type', $type);
    }

    public function scopeForContract($query, string $contractId)
    {
        return $query->where('sponsor_contract_id', $contractId);
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getBenefitTypeLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->benefit_type->label()
            : $this->benefit_type->labelEn();
    }

    public function getStatusLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->status->label()
            : $this->status->labelEn();
    }

    public function getDeliveryProgressAttribute(): float
    {
        if ($this->quantity === 0) {
            return 0;
        }

        return round(($this->delivered_quantity / $this->quantity) * 100, 1);
    }

    public function getIsFullyDeliveredAttribute(): bool
    {
        return $this->delivered_quantity >= $this->quantity;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markDelivered(int $quantity = 1, ?string $notes = null): void
    {
        $newDelivered = min($this->quantity, $this->delivered_quantity + $quantity);

        $this->update([
            'delivered_quantity' => $newDelivered,
            'status' => $newDelivered >= $this->quantity
                ? SponsorBenefitStatus::DELIVERED
                : SponsorBenefitStatus::IN_PROGRESS,
            'delivery_notes' => $notes ?? $this->delivery_notes,
        ]);
    }

    public function startDelivery(): void
    {
        $this->update(['status' => SponsorBenefitStatus::IN_PROGRESS]);
    }

    public function cancelBenefit(): void
    {
        $this->update(['status' => SponsorBenefitStatus::CANCELLED]);
    }
}
