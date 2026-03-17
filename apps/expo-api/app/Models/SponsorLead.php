<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorLead extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sponsor_id',
        'event_id',
        'company_name',
        'company_name_ar',
        'contact_name',
        'contact_name_ar',
        'contact_email',
        'contact_phone',
        'industry',
        'industry_ar',
        'interest_level',
        'status',
        'notes',
        'notes_ar',
        'source',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'date',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForSponsor($query, string $sponsorId)
    {
        return $query->where('sponsor_id', $sponsorId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeOfInterest($query, string $level)
    {
        return $query->where('interest_level', $level);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('company_name', 'like', "%{$term}%")
                ->orWhere('company_name_ar', 'like', "%{$term}%")
                ->orWhere('contact_name', 'like', "%{$term}%")
                ->orWhere('contact_name_ar', 'like', "%{$term}%")
                ->orWhere('contact_email', 'like', "%{$term}%")
                ->orWhere('industry', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedCompanyNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->company_name_ar ?? $this->company_name) : $this->company_name;
    }

    public function getLocalizedContactNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->contact_name_ar ?? $this->contact_name) : $this->contact_name;
    }

    public function getLocalizedIndustryAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->industry_ar ?? $this->industry) : $this->industry;
    }
}
