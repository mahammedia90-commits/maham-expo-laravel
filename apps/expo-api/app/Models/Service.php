<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
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

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'service_space')->withTimestamps();
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
