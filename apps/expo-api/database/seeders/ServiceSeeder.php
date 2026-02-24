<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Electricity',
                'name_ar' => 'كهرباء',
                'description' => 'Electrical power supply and outlets',
                'description_ar' => 'توصيلات ومنافذ كهربائية',
                'icon' => 'bolt',
                'sort_order' => 1,
            ],
            [
                'name' => 'Water Supply',
                'name_ar' => 'مياه',
                'description' => 'Water supply and drainage',
                'description_ar' => 'توصيلات مياه وصرف',
                'icon' => 'droplet',
                'sort_order' => 2,
            ],
            [
                'name' => 'Internet & Wi-Fi',
                'name_ar' => 'إنترنت وواي فاي',
                'description' => 'High-speed internet connectivity',
                'description_ar' => 'اتصال إنترنت عالي السرعة',
                'icon' => 'wifi',
                'sort_order' => 3,
            ],
            [
                'name' => 'Air Conditioning',
                'name_ar' => 'تكييف',
                'description' => 'Central air conditioning system',
                'description_ar' => 'نظام تكييف مركزي',
                'icon' => 'snowflake',
                'sort_order' => 4,
            ],
            [
                'name' => 'Security',
                'name_ar' => 'أمن وحراسة',
                'description' => '24/7 security and surveillance',
                'description_ar' => 'خدمات أمن وحراسة على مدار الساعة',
                'icon' => 'shield',
                'sort_order' => 5,
            ],
            [
                'name' => 'Cleaning',
                'name_ar' => 'نظافة',
                'description' => 'Daily cleaning and maintenance',
                'description_ar' => 'خدمات تنظيف وصيانة يومية',
                'icon' => 'sparkles',
                'sort_order' => 6,
            ],
            [
                'name' => 'Parking',
                'name_ar' => 'مواقف سيارات',
                'description' => 'Dedicated parking spaces for exhibitors',
                'description_ar' => 'مواقف سيارات مخصصة للعارضين',
                'icon' => 'car',
                'sort_order' => 7,
            ],
            [
                'name' => 'Storage',
                'name_ar' => 'تخزين',
                'description' => 'Secure storage rooms for goods',
                'description_ar' => 'غرف تخزين آمنة للبضائع',
                'icon' => 'warehouse',
                'sort_order' => 8,
            ],
            [
                'name' => 'Signage & Branding',
                'name_ar' => 'لافتات وعلامات تجارية',
                'description' => 'Custom signage and branding setup',
                'description_ar' => 'تجهيز لافتات وعلامات تجارية مخصصة',
                'icon' => 'image',
                'sort_order' => 9,
            ],
            [
                'name' => 'Furniture',
                'name_ar' => 'أثاث',
                'description' => 'Tables, chairs, shelves, and display units',
                'description_ar' => 'طاولات وكراسي ورفوف ووحدات عرض',
                'icon' => 'armchair',
                'sort_order' => 10,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
