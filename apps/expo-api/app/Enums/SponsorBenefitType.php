<?php

namespace App\Enums;

enum SponsorBenefitType: string
{
    case SCREEN = 'screen';
    case BANNER = 'banner';
    case BOOTH = 'booth';
    case VIP_INVITATION = 'vip_invitation';
    case LOGO = 'logo';
    case NOTIFICATION = 'notification';
    case EMAIL = 'email';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::SCREEN => 'شاشة عرض',
            self::BANNER => 'بنر إعلاني',
            self::BOOTH => 'جناح/بوث',
            self::VIP_INVITATION => 'دعوة VIP',
            self::LOGO => 'ظهور شعار',
            self::NOTIFICATION => 'إشعار مدفوع',
            self::EMAIL => 'حملة بريدية',
            self::CUSTOM => 'مخصص',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::SCREEN => 'Display Screen',
            self::BANNER => 'Banner',
            self::BOOTH => 'Booth',
            self::VIP_INVITATION => 'VIP Invitation',
            self::LOGO => 'Logo Placement',
            self::NOTIFICATION => 'Sponsored Notification',
            self::EMAIL => 'Email Campaign',
            self::CUSTOM => 'Custom',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
