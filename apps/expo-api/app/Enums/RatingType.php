<?php

namespace App\Enums;

enum RatingType: string
{
    case SPACE = 'space';
    case EVENT = 'event';
    case INVESTOR = 'investor';
    case MERCHANT = 'merchant';

    public function label(): string
    {
        return match($this) {
            self::SPACE => 'تقييم مساحة',
            self::EVENT => 'تقييم فعالية',
            self::INVESTOR => 'تقييم مستثمر',
            self::MERCHANT => 'تقييم تاجر',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::SPACE => 'Space Rating',
            self::EVENT => 'Event Rating',
            self::INVESTOR => 'Investor Rating',
            self::MERCHANT => 'Merchant Rating',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
