<?php

namespace App\Models;

use App\Enums\PageType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'title_ar',
        'content',
        'content_ar',
        'type',
        'is_active',
        'sort_order',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'type' => PageType::class,
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'meta' => 'array',
    ];

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title;
    }

    public function getLocalizedContentAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->content_ar : $this->content;
    }
}
