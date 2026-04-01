<?php

namespace App\Enums;

enum ContractType: string
{
    case LEASE = 'lease';
    case SPONSORSHIP = 'sponsorship';
    case PARTNERSHIP = 'partnership';
    case SERVICE = 'service';
    case EMPLOYMENT = 'employment';

    public function label(): string
    {
        return match ($this) {
            self::LEASE => 'عقد إيجار',
            self::SPONSORSHIP => 'عقد رعاية',
            self::PARTNERSHIP => 'عقد شراكة',
            self::SERVICE => 'عقد خدمات',
            self::EMPLOYMENT => 'عقد توظيف',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::LEASE => 'Lease',
            self::SPONSORSHIP => 'Sponsorship',
            self::PARTNERSHIP => 'Partnership',
            self::SERVICE => 'Service',
            self::EMPLOYMENT => 'Employment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LEASE => 'blue',
            self::SPONSORSHIP => 'purple',
            self::PARTNERSHIP => 'green',
            self::SERVICE => 'orange',
            self::EMPLOYMENT => 'teal',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
