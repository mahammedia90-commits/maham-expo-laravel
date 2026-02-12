<?php

namespace App\Enums;

enum SpaceType: string
{
    case BOOTH = 'booth';
    case SHOP = 'shop';
    case OFFICE = 'office';
    case HALL = 'hall';
    case OUTDOOR = 'outdoor';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::BOOTH => 'كشك',
            self::SHOP => 'محل',
            self::OFFICE => 'مكتب',
            self::HALL => 'قاعة',
            self::OUTDOOR => 'خارجي',
            self::OTHER => 'أخرى',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::BOOTH => 'Booth',
            self::SHOP => 'Shop',
            self::OFFICE => 'Office',
            self::HALL => 'Hall',
            self::OUTDOOR => 'Outdoor',
            self::OTHER => 'Other',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
