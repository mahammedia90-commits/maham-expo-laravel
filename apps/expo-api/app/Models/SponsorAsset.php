<?php

namespace App\Models;

use App\Enums\SponsorAssetType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorAsset extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sponsor_id',
        'event_id',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_approved',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'sort_order',
    ];

    protected $casts = [
        'type' => SponsorAssetType::class,
        'file_size' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false)
            ->whereNull('rejection_reason');
    }

    public function scopeRejected($query)
    {
        return $query->where('is_approved', false)
            ->whereNotNull('rejection_reason');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForSponsor($query, string $sponsorId)
    {
        return $query->where('sponsor_id', $sponsorId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getTypeLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->type->label()
            : $this->type->labelEn();
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /* ========================================
     * Methods
     * ======================================== */

    public function approve(string $approvedBy): void
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'is_approved' => false,
            'rejection_reason' => $reason,
        ]);
    }
}
