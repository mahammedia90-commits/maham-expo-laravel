<?php

namespace App\Enums;

enum SpaceStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case RENTED = 'rented';
    case UNAVAILABLE = 'unavailable';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'متاح',
            self::RESERVED => 'محجوز',
            self::RENTED => 'مؤجر',
            self::UNAVAILABLE => 'غير متاح',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::RENTED => 'Rented',
            self::UNAVAILABLE => 'Unavailable',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
