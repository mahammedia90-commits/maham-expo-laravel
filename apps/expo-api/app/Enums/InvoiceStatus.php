<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case ISSUED = 'issued';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::ISSUED => 'صادرة',
            self::PAID => 'مدفوعة',
            self::PARTIALLY_PAID => 'مدفوعة جزئياً',
            self::OVERDUE => 'متأخرة',
            self::CANCELLED => 'ملغاة',
            self::REFUNDED => 'مستردة',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::ISSUED => 'Issued',
            self::PAID => 'Paid',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => '#95a5a6',
            self::ISSUED => '#3498db',
            self::PAID => '#27ae60',
            self::PARTIALLY_PAID => '#f39c12',
            self::OVERDUE => '#e74c3c',
            self::CANCELLED => '#7f8c8d',
            self::REFUNDED => '#9b59b6',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
