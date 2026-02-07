<?php

namespace App\Models;

use App\Enums\SpaceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'amenities',
        'amenities_ar',
        'status',
        'floor_number',
        'section',
    ];

    protected $casts = [
        'area_sqm' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_total' => 'decimal:2',
        'images' => 'array',
        'amenities' => 'array',
        'amenities_ar' => 'array',
        'status' => SpaceStatus::class,
        'floor_number' => 'integer',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function rentalRequests(): HasMany
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
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
