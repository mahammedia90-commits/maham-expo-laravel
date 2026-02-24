<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_REPLY = 'waiting_reply';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'مفتوحة',
            self::IN_PROGRESS => 'قيد المعالجة',
            self::WAITING_REPLY => 'بانتظار الرد',
            self::RESOLVED => 'تم الحل',
            self::CLOSED => 'مغلقة',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::WAITING_REPLY => 'Waiting Reply',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => '#3498db',
            self::IN_PROGRESS => '#f39c12',
            self::WAITING_REPLY => '#9b59b6',
            self::RESOLVED => '#27ae60',
            self::CLOSED => '#95a5a6',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::OPEN, self::IN_PROGRESS, self::WAITING_REPLY]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
