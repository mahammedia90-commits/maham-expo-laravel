<?php

namespace App\Models;

use App\Enums\PaymentSystem;
use App\Enums\RentalDuration;
use App\Enums\SpaceStatus;
use App\Enums\SpaceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'location_code',
        'area_sqm',
        'price_per_day',
        'price_total',
        'images',
        'images_360',
        'amenities',
        'amenities_ar',
        'status',
        'floor_number',
        'section_id',
        'space_type',
        'payment_system',
        'rental_duration',
        'allowed_business_type',
        'investor_conditions',
        'investor_conditions_ar',
        'operational_notes',
        'operational_notes_ar',
        'latitude',
        'longitude',
        'address',
        'address_ar',
        'is_featured',
    ];

    protected $casts = [
        'area_sqm' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_total' => 'decimal:2',
        'images' => 'array',
        'images_360' => 'array',
        'amenities' => 'array',
        'amenities_ar' => 'array',
        'status' => SpaceStatus::class,
        'floor_number' => 'integer',
        'space_type' => SpaceType::class,
        'payment_system' => PaymentSystem::class,
        'rental_duration' => RentalDuration::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_space')->withTimestamps();
    }

    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function rentalContracts(): HasMany
    {
        return $this->hasMany(RentalContract::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeAvailable($query)
    {
        return $query->where('status', SpaceStatus::AVAILABLE);
    }

    public function scopeInEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeInSection($query, string $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeOfType($query, string $spaceType)
    {
        return $query->where('space_type', $spaceType);
    }

    public function scopeWithPaymentSystem($query, string $paymentSystem)
    {
        return $query->where('payment_system', $paymentSystem);
    }

    public function scopeWithRentalDuration($query, string $rentalDuration)
    {
        return $query->where('rental_duration', $rentalDuration);
    }

    public function scopeHasService($query, string $serviceId)
    {
        return $query->whereHas('services', function ($q) use ($serviceId) {
            $q->where('services.id', $serviceId);
        });
    }

    public function scopeInPriceRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price_total', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price_total', '<=', $max);
        }
        return $query;
    }

    public function scopeInAreaRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('area_sqm', '>=', $min);
        }
        if ($max !== null) {
            $query->where('area_sqm', '<=', $max);
        }
        return $query;
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
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description;
    }

    public function getLocalizedAmenitiesAttribute(): ?array
    {
        return app()->getLocale() === 'ar' ? $this->amenities_ar : $this->amenities;
    }

    public function getLocalizedAddressAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->address_ar ?? $this->address) : $this->address;
    }

    public function getLocalizedInvestorConditionsAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->investor_conditions_ar ?? $this->investor_conditions) : $this->investor_conditions;
    }

    public function getLocalizedOperationalNotesAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->operational_notes_ar ?? $this->operational_notes) : $this->operational_notes;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->where('is_approved', true)->avg('overall_rating') ?? 0, 1);
    }

    public function getRatingsCountAttribute(): int
    {
        return $this->ratings()->where('is_approved', true)->count();
    }

    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === SpaceStatus::AVAILABLE;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markAsReserved(): void
    {
        $this->update(['status' => SpaceStatus::RESERVED]);
    }

    public function markAsRented(): void
    {
        $this->update(['status' => SpaceStatus::RENTED]);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => SpaceStatus::AVAILABLE]);
    }

    public function isAvailableForDates(string $startDate, string $endDate): bool
    {
        if ($this->status !== SpaceStatus::AVAILABLE) {
            return false;
        }

        // Check for conflicting approved rental requests
        return !$this->rentalRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
}
