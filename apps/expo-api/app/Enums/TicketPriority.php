<?php

namespace App\Enums;

enum TicketPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'منخفضة',
            self::MEDIUM => 'متوسطة',
            self::HIGH => 'عالية',
            self::URGENT => 'عاجلة',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => '#95a5a6',
            self::MEDIUM => '#3498db',
            self::HIGH => '#f39c12',
            self::URGENT => '#e74c3c',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
