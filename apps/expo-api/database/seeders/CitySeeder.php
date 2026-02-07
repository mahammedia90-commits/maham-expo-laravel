<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Riyadh',
                'name_ar' => 'الرياض',
                'region' => 'Riyadh Region',
                'region_ar' => 'منطقة الرياض',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'sort_order' => 1,
            ],
            [
                'name' => 'Jeddah',
                'name_ar' => 'جدة',
                'region' => 'Makkah Region',
                'region_ar' => 'منطقة مكة المكرمة',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'sort_order' => 2,
            ],
            [
                'name' => 'Dammam',
                'name_ar' => 'الدمام',
                'region' => 'Eastern Region',
                'region_ar' => 'المنطقة الشرقية',
                'latitude' => 26.3927,
                'longitude' => 49.9777,
                'sort_order' => 3,
            ],
            [
                'name' => 'Makkah',
                'name_ar' => 'مكة المكرمة',
                'region' => 'Makkah Region',
                'region_ar' => 'منطقة مكة المكرمة',
                'latitude' => 21.3891,
                'longitude' => 39.8579,
                'sort_order' => 4,
            ],
            [
                'name' => 'Madinah',
                'name_ar' => 'المدينة المنورة',
                'region' => 'Madinah Region',
                'region_ar' => 'منطقة المدينة المنورة',
                'latitude' => 24.5247,
                'longitude' => 39.5692,
                'sort_order' => 5,
            ],
            [
                'name' => 'Khobar',
                'name_ar' => 'الخبر',
                'region' => 'Eastern Region',
                'region_ar' => 'المنطقة الشرقية',
                'latitude' => 26.2172,
                'longitude' => 50.1971,
                'sort_order' => 6,
            ],
            [
                'name' => 'Tabuk',
                'name_ar' => 'تبوك',
                'region' => 'Tabuk Region',
                'region_ar' => 'منطقة تبوك',
                'latitude' => 28.3838,
                'longitude' => 36.5550,
                'sort_order' => 7,
            ],
            [
                'name' => 'Abha',
                'name_ar' => 'أبها',
                'region' => 'Asir Region',
                'region_ar' => 'منطقة عسير',
                'latitude' => 18.2164,
                'longitude' => 42.5053,
                'sort_order' => 8,
            ],
            [
                'name' => 'Buraidah',
                'name_ar' => 'بريدة',
                'region' => 'Qassim Region',
                'region_ar' => 'منطقة القصيم',
                'latitude' => 26.3260,
                'longitude' => 43.9750,
                'sort_order' => 9,
            ],
            [
                'name' => 'Hail',
                'name_ar' => 'حائل',
                'region' => 'Hail Region',
                'region_ar' => 'منطقة حائل',
                'latitude' => 27.5114,
                'longitude' => 41.7208,
                'sort_order' => 10,
            ],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
