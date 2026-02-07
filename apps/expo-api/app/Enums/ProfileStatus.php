<?php

namespace App\Enums;

enum ProfileStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد المراجعة',
            self::APPROVED => 'موثق',
            self::REJECTED => 'مرفوض',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Verified',
            self::REJECTED => 'Rejected',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::PENDING, self::REJECTED]);
    }
}
