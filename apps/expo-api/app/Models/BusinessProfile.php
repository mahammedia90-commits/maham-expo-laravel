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
        'full_name',
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
        // KYC Personal
        'date_of_birth',
        'nationality',
        'city',
        // KYC Company
        'business_activity_type',
        'establishment_year',
        'vat_number',
        'national_address',
        'employee_count',
        // KYC Bank
        'bank_name',
        'iban',
        'account_holder_name',
        'account_number',
        // KYC Documents
        'vat_certificate_image',
        'authorization_letter_image',
        'national_address_doc',
        'bank_letter_image',
        'company_profile_doc',
        'product_catalog_doc',
        // KYC Tracking
        'legal_declaration_accepted',
        'legal_declaration_accepted_at',
        'kyc_step',
        'kyc_submitted_at',
    ];

    protected $casts = [
        'business_type' => BusinessType::class,
        'status' => ProfileStatus::class,
        'reviewed_at' => 'datetime',
        'date_of_birth' => 'date',
        'legal_declaration_accepted' => 'boolean',
        'legal_declaration_accepted_at' => 'datetime',
        'kyc_step' => 'integer',
        'kyc_submitted_at' => 'datetime',
    ];

    protected $hidden = [
        'national_id_number',
        'iban',
        'account_number',
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
