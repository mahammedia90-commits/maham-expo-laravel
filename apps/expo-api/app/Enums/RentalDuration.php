<?php

namespace App\Enums;

enum RentalDuration: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case FULL_EVENT = 'full_event';

    public function label(): string
    {
        return match($this) {
            self::DAILY => 'يومي',
            self::WEEKLY => 'أسبوعي',
            self::MONTHLY => 'شهري',
            self::FULL_EVENT => 'طوال الفعالية',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::FULL_EVENT => 'Full Event',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
