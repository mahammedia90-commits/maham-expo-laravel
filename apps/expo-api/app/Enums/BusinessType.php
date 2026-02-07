<?php

namespace App\Enums;

enum BusinessType: string
{
    case INVESTOR = 'investor';
    case MERCHANT = 'merchant';

    public function label(): string
    {
        return match($this) {
            self::INVESTOR => 'مستثمر',
            self::MERCHANT => 'تاجر',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::INVESTOR => 'Investor',
            self::MERCHANT => 'Merchant',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
