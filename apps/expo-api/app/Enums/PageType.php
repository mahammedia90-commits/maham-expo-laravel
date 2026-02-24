<?php

namespace App\Enums;

enum PageType: string
{
    case ABOUT = 'about';
    case TERMS = 'terms';
    case PRIVACY = 'privacy';
    case FAQ = 'faq';
    case CONTACT = 'contact';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match($this) {
            self::ABOUT => 'عن التطبيق',
            self::TERMS => 'الشروط والأحكام',
            self::PRIVACY => 'سياسة الخصوصية',
            self::FAQ => 'الأسئلة الشائعة',
            self::CONTACT => 'اتصل بنا',
            self::CUSTOM => 'صفحة مخصصة',
        };
    }

    public function labelEn(): string
    {
        return match($this) {
            self::ABOUT => 'About',
            self::TERMS => 'Terms & Conditions',
            self::PRIVACY => 'Privacy Policy',
            self::FAQ => 'FAQ',
            self::CONTACT => 'Contact Us',
            self::CUSTOM => 'Custom Page',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
