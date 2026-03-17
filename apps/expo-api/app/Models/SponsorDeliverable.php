<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorDeliverable extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sponsor_contract_id',
        'category',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'specs',
        'status',
        'deadline',
        'completed_at',
        'file_path',
        'file_name',
        'file_size',
        'rejection_reason',
        'admin_notes',
        'sort_order',
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'date',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SponsorContract::class, 'sponsor_contract_id');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForContract($query, string $contractId)
    {
        return $query->where('sponsor_contract_id', $contractId);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('deadline');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['completed', 'rejected'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString());
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? $this->title) : $this->title;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? $this->description) : $this->description;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline
            && !in_array($this->status, ['completed', 'rejected'])
            && $this->deadline->isPast();
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()->toDateString(),
        ]);
    }
}
