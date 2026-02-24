<?php

namespace App\Enums;

enum SponsorTier: string
{
    case PLATINUM = 'platinum';
    case GOLD = 'gold';
    case SILVER = 'silver';
    case BRONZE = 'bronze';
    case MEDIA_PARTNER = 'media_partner';
    case STRATEGIC_PARTNER = 'strategic_partner';

    public function label(): string
    {
        return match ($this) {
            self::PLATINUM => 'شريك استراتيجي - بلاتيني',
            self::GOLD => 'راعي رئيسي - ذهبي',
            self::SILVER => 'راعي داعم - فضي',
            self::BRONZE => 'راعي - برونزي',
            self::MEDIA_PARTNER => 'شريك إعلامي',
            self::STRATEGIC_PARTNER => 'تحالف استراتيجي',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::PLATINUM => 'Platinum Sponsor',
            self::GOLD => 'Gold Sponsor',
            self::SILVER => 'Silver Sponsor',
            self::BRONZE => 'Bronze Sponsor',
            self::MEDIA_PARTNER => 'Media Partner',
            self::STRATEGIC_PARTNER => 'Strategic Alliance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLATINUM => '#E5E4E2',
            self::GOLD => '#FFD700',
            self::SILVER => '#C0C0C0',
            self::BRONZE => '#CD7F32',
            self::MEDIA_PARTNER => '#1DA1F2',
            self::STRATEGIC_PARTNER => '#6B21A8',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::PLATINUM => 1,
            self::GOLD => 2,
            self::SILVER => 3,
            self::BRONZE => 4,
            self::MEDIA_PARTNER => 5,
            self::STRATEGIC_PARTNER => 6,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
