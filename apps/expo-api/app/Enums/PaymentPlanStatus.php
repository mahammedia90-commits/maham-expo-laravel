<?php

namespace App\Enums;

enum PaymentPlanStatus: string
{
    case UPCOMING = 'upcoming';
    case DUE = 'due';
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::UPCOMING => 'قادم',
            self::DUE => 'مستحق',
            self::PAID => 'مدفوع',
            self::PARTIAL => 'مدفوع جزئياً',
            self::OVERDUE => 'متأخر',
            self::CANCELLED => 'ملغي',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::UPCOMING => 'Upcoming',
            self::DUE => 'Due',
            self::PAID => 'Paid',
            self::PARTIAL => 'Partial',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UPCOMING => 'gray',
            self::DUE => 'yellow',
            self::PAID => 'green',
            self::PARTIAL => 'blue',
            self::OVERDUE => 'red',
            self::CANCELLED => 'slate',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
