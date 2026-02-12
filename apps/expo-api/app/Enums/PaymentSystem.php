<?php

namespace App\Enums;

enum PaymentSystem: string
{
    case FULL = 'full';
    case INSTALLMENT = 'installment';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';

    public function label(): string
    {
        return match($this) {
            self::FULL => 'دفع كامل',
            self::INSTALLMENT => 'أقساط',
            self::DAILY => 'يومي',
            self::MONTHLY => 'شهري',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::FULL => 'Full Payment',
            self::INSTALLMENT => 'Installment',
            self::DAILY => 'Daily',
            self::MONTHLY => 'Monthly',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
