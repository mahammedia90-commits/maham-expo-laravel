<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'image',
        'image_ar',
        'link_url',
        'position',
        'is_active',
        'start_date',
        'end_date',
        'sort_order',
        'clicks_count',
        'impressions_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'sort_order' => 'integer',
        'clicks_count' => 'integer',
        'impressions_count' => 'integer',
    ];

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getLocalizedImageAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->image_ar ?? $this->image) : $this->image;
    }

    public function getClickThroughRateAttribute(): float
    {
        if ($this->impressions_count <= 0) {
            return 0;
        }
        return round(($this->clicks_count / $this->impressions_count) * 100, 2);
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function trackClick(): void
    {
        $this->increment('clicks_count');
    }

    public function trackImpression(): void
    {
        $this->increment('impressions_count');
    }
}
