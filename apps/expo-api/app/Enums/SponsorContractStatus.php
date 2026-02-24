<?php

namespace App\Enums;

enum SponsorContractStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'مسودة',
            self::PENDING => 'قيد المراجعة',
            self::ACTIVE => 'نشط',
            self::COMPLETED => 'مكتمل',
            self::CANCELLED => 'ملغي',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::PENDING => 'warning',
            self::ACTIVE => 'success',
            self::COMPLETED => 'info',
            self::CANCELLED => 'danger',
        };
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::DRAFT, self::PENDING]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::DRAFT, self::PENDING, self::ACTIVE]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
