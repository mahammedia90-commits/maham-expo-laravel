<?php

namespace App\Models;

use App\Enums\SpaceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
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

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
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

    public function getAvailableSpacesCountAttribute(): int
    {
        return $this->spaces()->where('status', SpaceStatus::AVAILABLE)->count();
    }

    public function getSpacesCountAttribute(): int
    {
        return $this->spaces()->count();
    }
}
