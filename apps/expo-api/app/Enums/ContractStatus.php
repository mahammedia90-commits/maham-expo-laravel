<?php

namespace App\Enums;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::PENDING => 'قيد المراجعة',
            self::ACTIVE => 'نشط',
            self::EXPIRED => 'منتهي',
            self::CANCELLED => 'ملغي',
            self::TERMINATED => 'مُنهى',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
            self::TERMINATED => 'Terminated',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => '#95a5a6',
            self::PENDING => '#f39c12',
            self::ACTIVE => '#27ae60',
            self::EXPIRED => '#e67e22',
            self::CANCELLED => '#e74c3c',
            self::TERMINATED => '#c0392b',
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
