<?php

namespace App\Enums;

enum ExposureChannel: string
{
    case APP = 'app';
    case WEBSITE = 'website';
    case SCREEN = 'screen';
    case TICKET = 'ticket';
    case EMAIL = 'email';
    case PUSH_NOTIFICATION = 'push_notification';
    case SOCIAL_MEDIA = 'social_media';

    public function label(): string
    {
        return match ($this) {
            self::APP => 'التطبيق',
            self::WEBSITE => 'الموقع الإلكتروني',
            self::SCREEN => 'الشاشات الرقمية',
            self::TICKET => 'التذاكر',
            self::EMAIL => 'البريد الإلكتروني',
            self::PUSH_NOTIFICATION => 'الإشعارات',
            self::SOCIAL_MEDIA => 'وسائل التواصل',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::APP => 'Mobile App',
            self::WEBSITE => 'Website',
            self::SCREEN => 'Digital Screens',
            self::TICKET => 'Tickets',
            self::EMAIL => 'Email',
            self::PUSH_NOTIFICATION => 'Push Notifications',
            self::SOCIAL_MEDIA => 'Social Media',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
