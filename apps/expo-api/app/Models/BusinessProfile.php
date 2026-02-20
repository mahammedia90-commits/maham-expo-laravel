<?php

namespace App\Models;

use App\Enums\BusinessType;
use App\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_name_ar',
        'commercial_registration_number',
        'commercial_registration_image',
        'national_id_number',
        'national_id_image',
        'company_logo',
        'avatar',
        'company_address',
        'company_address_ar',
        'contact_phone',
        'contact_email',
        'website',
        'business_type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'business_type' => BusinessType::class,
        'status' => ProfileStatus::class,
        'reviewed_at' => 'datetime',
    ];

    protected $hidden = [
        'national_id_number',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopePending($query)
    {
        return $query->where('status', ProfileStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ProfileStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ProfileStatus::REJECTED);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedCompanyNameAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->company_name_ar ?? $this->company_name)
            : $this->company_name;
    }

    public function getLocalizedCompanyAddressAttribute(): ?string
    {
        return app()->getLocale() === 'ar'
            ? ($this->company_address_ar ?? $this->company_address)
            : $this->company_address;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === ProfileStatus::APPROVED;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === ProfileStatus::PENDING;
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === ProfileStatus::REJECTED;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function approve(string $reviewerId): void
    {
        $this->update([
            'status' => ProfileStatus::APPROVED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $reviewerId, string $reason): void
    {
        $this->update([
            'status' => ProfileStatus::REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }
}
