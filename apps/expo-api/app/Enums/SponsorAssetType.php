<?php

namespace App\Enums;

enum SponsorAssetType: string
{
    case LOGO = 'logo';
    case BANNER = 'banner';
    case BOOTH_DESIGN = 'booth_design';
    case VIDEO = 'video';
    case DOCUMENT = 'document';

    public function label(): string
    {
        return match ($this) {
            self::LOGO => 'شعار',
            self::BANNER => 'بنر',
            self::BOOTH_DESIGN => 'تصميم بوث',
            self::VIDEO => 'فيديو',
            self::DOCUMENT => 'مستند',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::LOGO => 'Logo',
            self::BANNER => 'Banner',
            self::BOOTH_DESIGN => 'Booth Design',
            self::VIDEO => 'Video',
            self::DOCUMENT => 'Document',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
