<?php

namespace Database\Seeders;

use App\Models\InvestorProfile;
use Illuminate\Database\Seeder;

class InvestorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $investors = [
            [
                'name' => 'أحمد الراشد',
                'name_en' => 'Ahmed Al-Rashid',
                'email' => 'ahmed.rashid@example.com',
                'phone' => '+966501234567',
                'company' => 'Rashid Capital Investment',
                'sector' => 'Technology',
                'investment_amount' => 5000000,
                'portfolio_value' => 8500000,
                'roi_percentage' => 25.5,
                'status' => 'active',
            ],
            [
                'name' => 'سارة محمد',
                'name_en' => 'Sarah Mohammed',
                'email' => 'sarah.m@example.com',
                'phone' => '+966502345678',
                'company' => 'Global Ventures LLC',
                'sector' => 'Real Estate',
                'investment_amount' => 3500000,
                'portfolio_value' => 6200000,
                'roi_percentage' => 18.2,
                'status' => 'active',
            ],
            [
                'name' => 'محمد حسن',
                'name_en' => 'Mohammed Hassan',
                'email' => 'm.hassan@example.com',
                'phone' => '+966503456789',
                'company' => 'Hassan Investment Group',
                'sector' => 'Retail',
                'investment_amount' => 2800000,
                'portfolio_value' => 4900000,
                'roi_percentage' => 22.1,
                'status' => 'active',
            ],
            [
                'name' => 'فاطمة العتيبي',
                'name_en' => 'Fatima Al-Otaibi',
                'email' => 'fatima.otaibi@example.com',
                'phone' => '+966504567890',
                'company' => 'Al-Otaibi Capital',
                'sector' => 'Technology',
                'investment_amount' => 4200000,
                'portfolio_value' => 7100000,
                'roi_percentage' => 28.3,
                'status' => 'active',
            ],
            [
                'name' => 'علي النوري',
                'name_en' => 'Ali Al-Nouri',
                'email' => 'ali.nouri@example.com',
                'phone' => '+966505678901',
                'company' => 'Nouri Investment Partners',
                'sector' => 'Hospitality',
                'investment_amount' => 2100000,
                'portfolio_value' => 3800000,
                'roi_percentage' => 15.7,
                'status' => 'pending',
            ],
            [
                'name' => 'ليلى الشريف',
                'name_en' => 'Laila Al-Sharif',
                'email' => 'laila.sharif@example.com',
                'phone' => '+966506789012',
                'company' => 'Sharif Wealth Management',
                'sector' => 'Real Estate',
                'investment_amount' => 6500000,
                'portfolio_value' => 10200000,
                'roi_percentage' => 31.2,
                'status' => 'active',
            ],
        ];

        foreach ($investors as $investor) {
            InvestorProfile::create($investor);
        }
    }
}
