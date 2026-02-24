<?php

namespace App\Models;

use App\Enums\ExposureChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorExposureTracking extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sponsor_exposure_tracking';

    protected $fillable = [
        'sponsor_id',
        'event_id',
        'sponsor_contract_id',
        'channel',
        'impressions_count',
        'clicks_count',
        'date',
        'metadata',
    ];

    protected $casts = [
        'channel' => ExposureChannel::class,
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
        'date' => 'date',
        'metadata' => 'array',
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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(SponsorContract::class, 'sponsor_contract_id');
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForSponsor($query, string $sponsorId)
    {
        return $query->where('sponsor_id', $sponsorId);
    }

    public function scopeForEvent($query, string $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeInDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getChannelLabelAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? $this->channel->label()
            : $this->channel->labelEn();
    }

    public function getClickThroughRateAttribute(): float
    {
        if ($this->impressions_count === 0) {
            return 0;
        }

        return round(($this->clicks_count / $this->impressions_count) * 100, 2);
    }

    /* ========================================
     * Methods
     * ======================================== */

    /**
     * Track or increment exposure for a sponsor on a specific channel/date
     */
    public static function trackImpression(
        string $sponsorId,
        string $eventId,
        string $channel,
        int $impressions = 1,
        ?string $contractId = null,
        ?array $metadata = null
    ): self {
        $record = self::firstOrCreate(
            [
                'sponsor_id' => $sponsorId,
                'event_id' => $eventId,
                'channel' => $channel,
                'date' => now()->toDateString(),
            ],
            [
                'sponsor_contract_id' => $contractId,
                'impressions_count' => 0,
                'clicks_count' => 0,
                'metadata' => $metadata,
            ]
        );

        $record->increment('impressions_count', $impressions);

        return $record;
    }

    /**
     * Track a click for a sponsor on a specific channel/date
     */
    public static function trackClick(
        string $sponsorId,
        string $eventId,
        string $channel
    ): void {
        $record = self::where([
            'sponsor_id' => $sponsorId,
            'event_id' => $eventId,
            'channel' => $channel,
            'date' => now()->toDateString(),
        ])->first();

        if ($record) {
            $record->increment('clicks_count');
        }
    }
}
