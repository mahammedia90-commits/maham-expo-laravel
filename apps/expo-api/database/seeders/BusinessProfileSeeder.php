<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            // Approved Investor Profile
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'company_name' => 'TechVentures Saudi',
                'company_name_ar' => 'تيك فنتشرز السعودية',
                'commercial_registration_number' => '1010234567',
                'company_address' => 'King Fahad Road, Riyadh',
                'company_address_ar' => 'طريق الملك فهد، الرياض',
                'contact_phone' => '0501234567',
                'contact_email' => 'info@techventures.sa',
                'website' => 'https://techventures.sa',
                'business_type' => 'investor',
                'status' => 'approved',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(10),
            ],
            // Approved Merchant Profile
            [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'company_name' => 'Al-Salam Trading',
                'company_name_ar' => 'شركة السلام للتجارة',
                'commercial_registration_number' => '4030567890',
                'company_address' => 'Tahlia Street, Jeddah',
                'company_address_ar' => 'شارع التحلية، جدة',
                'contact_phone' => '0567891234',
                'contact_email' => 'info@alsalamtrading.sa',
                'business_type' => 'merchant',
                'status' => 'approved',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(7),
            ],
            // Pending Merchant Profile
            [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'company_name' => 'Fresh Foods Co.',
                'company_name_ar' => 'شركة الأطعمة الطازجة',
                'commercial_registration_number' => '1010987654',
                'company_address' => 'Olaya District, Riyadh',
                'company_address_ar' => 'حي العليا، الرياض',
                'contact_phone' => '0559876543',
                'contact_email' => 'contact@freshfoods.sa',
                'business_type' => 'merchant',
                'status' => 'pending',
            ],
            // Rejected Investor Profile
            [
                'user_id' => '00000000-0000-0000-0000-000000000013',
                'company_name' => 'Quick Invest',
                'company_name_ar' => 'كويك إنفست',
                'commercial_registration_number' => '2050111222',
                'contact_phone' => '0544332211',
                'contact_email' => 'info@quickinvest.sa',
                'business_type' => 'investor',
                'status' => 'rejected',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(3),
                'rejection_reason' => 'Commercial registration document is expired. Please upload a valid document.',
            ],
            // Another Approved Merchant
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'company_name' => 'Beauty World SA',
                'company_name_ar' => 'عالم الجمال السعودية',
                'commercial_registration_number' => '4030222333',
                'company_address' => 'Al Rawdah, Jeddah',
                'company_address_ar' => 'حي الروضة، جدة',
                'contact_phone' => '0512345678',
                'contact_email' => 'contact@beautyworld.sa',
                'website' => 'https://beautyworld.sa',
                'business_type' => 'merchant',
                'status' => 'approved',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(5),
            ],
            // Pending Investor
            [
                'user_id' => '00000000-0000-0000-0000-000000000015',
                'company_name' => 'Gulf Properties',
                'company_name_ar' => 'عقارات الخليج',
                'commercial_registration_number' => '2050333444',
                'company_address' => 'Corniche Road, Dammam',
                'company_address_ar' => 'طريق الكورنيش، الدمام',
                'contact_phone' => '0538765432',
                'contact_email' => 'info@gulfproperties.sa',
                'business_type' => 'investor',
                'status' => 'pending',
            ],
        ];

        foreach ($profiles as $profile) {
            BusinessProfile::create($profile);
            $this->command->info("Created profile: {$profile['company_name']}");
        }
    }
}
