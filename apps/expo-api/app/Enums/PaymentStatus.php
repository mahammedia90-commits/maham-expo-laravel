<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'في انتظار الدفع',
            self::PARTIAL => 'مدفوع جزئياً',
            self::PAID => 'مدفوع',
            self::REFUNDED => 'مسترد',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::PENDING => 'Pending Payment',
            self::PARTIAL => 'Partially Paid',
            self::PAID => 'Paid',
            self::REFUNDED => 'Refunded',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
