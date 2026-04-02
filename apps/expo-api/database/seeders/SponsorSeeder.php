<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'شركة التقنية المتقدمة',
                'name_en' => 'Advanced Tech Company',
                'email' => 'sponsor.tech@example.com',
                'phone' => '+966501111111',
                'company_type' => 'Technology',
                'sponsorship_tier' => 'platinum',
                'sponsorship_amount' => 2500000,
                'contract_start_date' => now()->subMonths(6)->toDateString(),
                'contract_end_date' => now()->addMonths(6)->toDateString(),
                'logo_url' => '/logos/tech-advanced.png',
                'status' => 'active',
                'contact_person' => 'محمد علي',
                'contact_email' => 'm.ali@techadvanced.com',
                'contact_phone' => '+966505555555',
            ],
            [
                'name' => 'مصرف الاستثمار الخليجي',
                'name_en' => 'Gulf Investment Bank',
                'email' => 'sponsor.bank@example.com',
                'phone' => '+966502222222',
                'company_type' => 'Finance',
                'sponsorship_tier' => 'gold',
                'sponsorship_amount' => 1500000,
                'contract_start_date' => now()->subMonths(4)->toDateString(),
                'contract_end_date' => now()->addMonths(8)->toDateString(),
                'logo_url' => '/logos/gulf-bank.png',
                'status' => 'active',
                'contact_person' => 'فاطمة العتيبي',
                'contact_email' => 'f.otaibi@gulfbank.com',
                'contact_phone' => '+966506666666',
            ],
            [
                'name' => 'شركة الشحن العالمية',
                'name_en' => 'Global Logistics Shipping',
                'email' => 'sponsor.logistics@example.com',
                'phone' => '+966503333333',
                'company_type' => 'Logistics',
                'sponsorship_tier' => 'gold',
                'sponsorship_amount' => 1200000,
                'contract_start_date' => now()->subMonths(3)->toDateString(),
                'contract_end_date' => now()->addMonths(9)->toDateString(),
                'logo_url' => '/logos/global-logistics.png',
                'status' => 'active',
                'contact_person' => 'سالم الشريف',
                'contact_email' => 's.sharif@globalship.com',
                'contact_phone' => '+966507777777',
            ],
            [
                'name' => 'شركة الإعلام والنشر',
                'name_en' => 'Media & Publishing Group',
                'email' => 'sponsor.media@example.com',
                'phone' => '+966504444444',
                'company_type' => 'Media',
                'sponsorship_tier' => 'silver',
                'sponsorship_amount' => 800000,
                'contract_start_date' => now()->subMonths(2)->toDateString(),
                'contract_end_date' => now()->addMonths(10)->toDateString(),
                'logo_url' => '/logos/media-pub.png',
                'status' => 'active',
                'contact_person' => 'ليلى المدني',
                'contact_email' => 'l.madani@mediapub.com',
                'contact_phone' => '+966508888888',
            ],
            [
                'name' => 'مركز التدريب والتطوير',
                'name_en' => 'Training & Development Center',
                'email' => 'sponsor.training@example.com',
                'phone' => '+966505555555',
                'company_type' => 'Education',
                'sponsorship_tier' => 'silver',
                'sponsorship_amount' => 750000,
                'contract_start_date' => now()->subMonths(1)->toDateString(),
                'contract_end_date' => now()->addMonths(11)->toDateString(),
                'logo_url' => '/logos/training-center.png',
                'status' => 'active',
                'contact_person' => 'علي الدعيع',
                'contact_email' => 'a.dueik@training.com',
                'contact_phone' => '+966509999999',
            ],
            [
                'name' => 'شركة الأمن والحماية',
                'name_en' => 'Security & Protection Co',
                'email' => 'sponsor.security@example.com',
                'phone' => '+966506666666',
                'company_type' => 'Security',
                'sponsorship_tier' => 'bronze',
                'sponsorship_amount' => 400000,
                'contract_start_date' => now()->subDays(15)->toDateString(),
                'contract_end_date' => now()->addMonths(12)->toDateString(),
                'logo_url' => '/logos/security.png',
                'status' => 'active',
                'contact_person' => 'خالد العنزي',
                'contact_email' => 'k.anazi@security.com',
                'contact_phone' => '+966501010101',
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::create($sponsor);
        }
    }
}
