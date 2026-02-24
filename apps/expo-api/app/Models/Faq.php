<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'question',
        'question_ar',
        'answer',
        'answer_ar',
        'category',
        'is_active',
        'sort_order',
        'views_count',
        'helpful_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'helpful_count' => 'integer',
    ];

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('views_count');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('question', 'like', "%{$term}%")
                ->orWhere('question_ar', 'like', "%{$term}%")
                ->orWhere('answer', 'like', "%{$term}%")
                ->orWhere('answer_ar', 'like', "%{$term}%");
        });
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedQuestionAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->question_ar : $this->question;
    }

    public function getLocalizedAnswerAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->answer_ar : $this->answer;
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }
}
