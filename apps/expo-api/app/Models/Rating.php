<?php

namespace App\Models;

use App\Enums\RatingType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'rateable_type',
        'rateable_id',
        'type',
        'overall_rating',
        'cleanliness_rating',
        'location_rating',
        'facilities_rating',
        'value_rating',
        'communication_rating',
        'comment',
        'comment_ar',
        'is_approved',
        'rental_request_id',
    ];

    protected $casts = [
        'type' => RatingType::class,
        'overall_rating' => 'integer',
        'cleanliness_rating' => 'integer',
        'location_rating' => 'integer',
        'facilities_rating' => 'integer',
        'value_rating' => 'integer',
        'communication_rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForRateable($query, string $rateableType, string $rateableId)
    {
        return $query->where('rateable_type', $rateableType)
            ->where('rateable_id', $rateableId);
    }

    public function scopeMinRating($query, int $min)
    {
        return $query->where('overall_rating', '>=', $min);
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedCommentAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->comment_ar ?? $this->comment) : $this->comment;
    }

    public function getAverageDetailedRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->cleanliness_rating,
            $this->location_rating,
            $this->facilities_rating,
            $this->value_rating,
            $this->communication_rating,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : null;
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function getAverageForRateable(string $rateableType, string $rateableId): array
    {
        $ratings = self::approved()
            ->forRateable($rateableType, $rateableId);

        $count = $ratings->count();

        if ($count === 0) {
            return [
                'average' => 0,
                'count' => 0,
                'breakdown' => [],
            ];
        }

        return [
            'average' => round($ratings->avg('overall_rating'), 1),
            'count' => $count,
            'breakdown' => [
                'cleanliness' => round($ratings->whereNotNull('cleanliness_rating')->avg('cleanliness_rating') ?? 0, 1),
                'location' => round($ratings->whereNotNull('location_rating')->avg('location_rating') ?? 0, 1),
                'facilities' => round($ratings->whereNotNull('facilities_rating')->avg('facilities_rating') ?? 0, 1),
                'value' => round($ratings->whereNotNull('value_rating')->avg('value_rating') ?? 0, 1),
                'communication' => round($ratings->whereNotNull('communication_rating')->avg('communication_rating') ?? 0, 1),
            ],
        ];
    }
}
