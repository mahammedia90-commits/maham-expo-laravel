<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;

class MerchantBusinessProfileSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = [
            [
                'name' => 'متجر التقنية الحديثة',
                'name_en' => 'Modern Tech Store',
                'email' => 'tech.store@example.com',
                'phone' => '+966501111111',
                'type' => 'merchant',
                'sector' => 'Technology',
                'description' => 'متخصصون في بيع أحدث الأجهزة الإلكترونية والهواتف الذكية',
                'location' => 'الرياض',
                'country' => 'السعودية',
                'status' => 'active',
            ],
            [
                'name' => 'عالم الملابس الفاخرة',
                'name_en' => 'Luxury Fashion World',
                'email' => 'luxury.fashion@example.com',
                'phone' => '+966502222222',
                'type' => 'merchant',
                'sector' => 'Fashion',
                'description' => 'محل متخصص في الملابس والإكسسوارات الفاخرة من ماركات عالمية',
                'location' => 'جدة',
                'country' => 'السعودية',
                'status' => 'active',
            ],
            [
                'name' => 'مطعم النكهات العربية',
                'name_en' => 'Arab Flavors Restaurant',
                'email' => 'arab.flavors@example.com',
                'phone' => '+966503333333',
                'type' => 'merchant',
                'sector' => 'Food & Beverage',
                'description' => 'مطعم يقدم أشهى الأطباق العربية التقليدية والحديثة',
                'location' => 'الدمام',
                'country' => 'السعودية',
                'status' => 'active',
            ],
            [
                'name' => 'جواهر الزينة والذهب',
                'name_en' => 'Jewelry & Gold Gems',
                'email' => 'jewelry.gems@example.com',
                'phone' => '+966504444444',
                'type' => 'merchant',
                'sector' => 'Jewelry',
                'description' => 'محل متخصص في المجوهرات والذهب والأحجار الكريمة',
                'location' => 'الرياض',
                'country' => 'السعودية',
                'status' => 'active',
            ],
            [
                'name' => 'محل الأثاث والديكور',
                'name_en' => 'Furniture & Decor Home',
                'email' => 'furniture.home@example.com',
                'phone' => '+966505555555',
                'type' => 'merchant',
                'sector' => 'Home & Living',
                'description' => 'متجر متخصص في الأثاث والديكورات المنزلية الحديثة',
                'location' => 'القاهرة',
                'country' => 'مصر',
                'status' => 'active',
            ],
            [
                'name' => 'مركز اللياقة البدنية',
                'name_en' => 'Fitness Center Pro',
                'email' => 'fitness.center@example.com',
                'phone' => '+966506666666',
                'type' => 'merchant',
                'sector' => 'Health & Fitness',
                'description' => 'مركز شامل لتدريبات اللياقة البدنية والصحة',
                'location' => 'الرياض',
                'country' => 'السعودية',
                'status' => 'active',
            ],
        ];

        foreach ($merchants as $merchant) {
            BusinessProfile::create($merchant);
        }
    }
}
