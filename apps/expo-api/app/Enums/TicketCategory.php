<?php

namespace App\Enums;

enum TicketCategory: string
{
    case GENERAL = 'general';
    case TECHNICAL = 'technical';
    case BILLING = 'billing';
    case SPACE = 'space';
    case EVENT = 'event';
    case CONTRACT = 'contract';
    case COMPLAINT = 'complaint';
    case SUGGESTION = 'suggestion';

    public function label(): string
    {
        return match($this) {
            self::GENERAL => 'عام',
            self::TECHNICAL => 'تقني',
            self::BILLING => 'مالي',
            self::SPACE => 'مساحات',
            self::EVENT => 'فعاليات',
            self::CONTRACT => 'عقود',
            self::COMPLAINT => 'شكوى',
            self::SUGGESTION => 'اقتراح',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::GENERAL => 'General',
            self::TECHNICAL => 'Technical',
            self::BILLING => 'Billing',
            self::SPACE => 'Space',
            self::EVENT => 'Event',
            self::CONTRACT => 'Contract',
            self::COMPLAINT => 'Complaint',
            self::SUGGESTION => 'Suggestion',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
