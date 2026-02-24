<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'category_id',
        'city_id',
        'address',
        'address_ar',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'opening_time',
        'closing_time',
        'images',
        'images_360',
        'features',
        'features_ar',
        'organizer_name',
        'organizer_phone',
        'organizer_email',
        'website',
        'status',
        'is_featured',
        'views_count',
        'expected_visitors',
        'investment_opportunity_rating',
        'promotional_video',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
        'images' => 'array',
        'images_360' => 'array',
        'features' => 'array',
        'features_ar' => 'array',
        'status' => EventStatus::class,
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'expected_visitors' => 'integer',
        'investment_opportunity_rating' => 'decimal:1',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function visitRequests(): HasMany
    {
        return $this->hasMany(VisitRequest::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function sponsorPackages(): HasMany
    {
        return $this->hasMany(SponsorPackage::class);
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

    public function scopePublished($query)
    {
        return $query->where('status', EventStatus::PUBLISHED);
    }

    public function scopeActive($query)
    {
        return $query->published()
            ->where('end_date', '>=', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->published()
            ->where('start_date', '>', now()->toDateString());
    }

    public function scopeOngoing($query)
    {
        return $query->published()
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCity($query, string $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeInCategory($query, string $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('name_ar', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('description_ar', 'like', "%{$term}%");
        });
    }

    public function scopeHasSpacesInPriceRange($query, ?float $min, ?float $max)
    {
        return $query->whereHas('spaces', function ($q) use ($min, $max) {
            if ($min !== null) {
                $q->where('price_total', '>=', $min);
            }
            if ($max !== null) {
                $q->where('price_total', '<=', $max);
            }
        });
    }

    public function scopeHasSpacesInAreaRange($query, ?float $min, ?float $max)
    {
        return $query->whereHas('spaces', function ($q) use ($min, $max) {
            if ($min !== null) {
                $q->where('area_sqm', '>=', $min);
            }
            if ($max !== null) {
                $q->where('area_sqm', '<=', $max);
            }
        });
    }

    public function scopeHasSpacesWithRentalDuration($query, string $rentalDuration)
    {
        return $query->whereHas('spaces', function ($q) use ($rentalDuration) {
            $q->where('rental_duration', $rentalDuration);
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description;
    }

    public function getLocalizedAddressAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->address_ar ?? $this->address) : $this->address;
    }

    public function getLocalizedFeaturesAttribute(): ?array
    {
        return app()->getLocale() === 'ar' ? $this->features_ar : $this->features;
    }

    public function getMainImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }

    public function getIsOngoingAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date > now()->toDateString();
    }

    public function getIsEndedAttribute(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    public function getAvailableSpacesCountAttribute(): int
    {
        return $this->spaces()->where('status', 'available')->count();
    }

    public function getTotalSpacesCountAttribute(): int
    {
        return $this->spaces()->count();
    }

    public function getMinPriceAttribute(): ?float
    {
        $minPrice = $this->spaces()->min('price_total');
        return $minPrice !== null ? (float) $minPrice : null;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function canBeVisited(): bool
    {
        return $this->status === EventStatus::PUBLISHED
            && !$this->is_ended;
    }
}
