<?php

namespace App\Enums;

enum RequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد المراجعة',
            self::APPROVED => 'موافق عليه',
            self::REJECTED => 'مرفوض',
            self::CANCELLED => 'ملغي',
            self::COMPLETED => 'مكتمل',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
            self::COMPLETED => 'info',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canBeModified(): bool
    {
        return $this === self::PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED]);
    }
}
