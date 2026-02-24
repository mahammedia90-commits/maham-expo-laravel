<?php

namespace App\Models;

use App\Enums\SponsorTier;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorPackage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'tier',
        'price',
        'max_sponsors',
        'benefits',
        'display_screens_count',
        'banners_count',
        'vip_invitations_count',
        'booth_area_sqm',
        'logo_placement',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'tier' => SponsorTier::class,
        'price' => 'decimal:2',
        'max_sponsors' => 'integer',
        'benefits' => 'array',
        'display_screens_count' => 'integer',
        'banners_count' => 'integer',
        'vip_invitations_count' => 'integer',
        'booth_area_sqm' => 'decimal:2',
        'logo_placement' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeOfTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price', 'desc');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('max_sponsors')
                    ->orWhereColumn(
                        \Illuminate\Support\Facades\DB::raw('(SELECT COUNT(*) FROM sponsor_contracts WHERE sponsor_contracts.sponsor_package_id = sponsor_packages.id AND sponsor_contracts.status IN ("active", "completed") AND sponsor_contracts.deleted_at IS NULL)'),
                        '<',
                        'max_sponsors'
                    );
            });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name) : $this->name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getTierLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->tier->label()
            : $this->tier->labelEn();
    }

    public function getActiveContractsCountAttribute(): int
    {
        return $this->contracts()->whereIn('status', ['active', 'completed'])->count();
    }

    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->max_sponsors === null) {
            return true;
        }

        return $this->active_contracts_count < $this->max_sponsors;
    }

    public function getRemainingSlots(): ?int
    {
        if ($this->max_sponsors === null) {
            return null;
        }

        return max(0, $this->max_sponsors - $this->active_contracts_count);
    }
}
