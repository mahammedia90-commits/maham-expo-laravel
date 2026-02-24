<?php

namespace App\Models;

use App\Enums\SponsorStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsor extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'name_ar',
        'company_name',
        'company_name_ar',
        'description',
        'description_ar',
        'logo',
        'contact_person',
        'contact_email',
        'contact_phone',
        'website',
        'status',
        'created_by',
        'created_from',
    ];

    protected $casts = [
        'status' => SponsorStatus::class,
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(SponsorContract::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(SponsorAsset::class);
    }

    public function exposureTracking(): HasMany
    {
        return $this->hasMany(SponsorExposureTracking::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('status', SponsorStatus::ACTIVE);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', SponsorStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', SponsorStatus::PENDING);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('name_ar', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhere('company_name_ar', 'like', "%{$term}%")
                ->orWhere('contact_email', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }

    public function getLocalizedCompanyNameAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->company_name_ar ?? $this->company_name) : $this->company_name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getStatusLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->status->label()
            : $this->status->labelEn();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === SponsorStatus::ACTIVE;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function approve(): void
    {
        $this->update(['status' => SponsorStatus::APPROVED]);
    }

    public function activate(): void
    {
        $this->update(['status' => SponsorStatus::ACTIVE]);
    }

    public function suspend(): void
    {
        $this->update(['status' => SponsorStatus::SUSPENDED]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => SponsorStatus::INACTIVE]);
    }
}
