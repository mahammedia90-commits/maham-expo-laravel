<?php

namespace Database\Seeders;

use App\Models\BusinessActivityType;
use Illuminate\Database\Seeder;

class BusinessActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'General Trading',
                'name_ar' => 'تجارة عامة',
                'description' => 'General import, export and trading activities',
                'description_ar' => 'أنشطة الاستيراد والتصدير والتجارة العامة',
                'icon' => 'shopping-bag',
                'sort_order' => 1,
            ],
            [
                'name' => 'Food & Beverages',
                'name_ar' => 'أغذية ومشروبات',
                'description' => 'Restaurants, cafes, food production and distribution',
                'description_ar' => 'مطاعم، كافيهات، إنتاج وتوزيع الأغذية',
                'icon' => 'utensils',
                'sort_order' => 2,
            ],
            [
                'name' => 'Fashion & Apparel',
                'name_ar' => 'أزياء وملابس',
                'description' => 'Clothing, accessories and fashion retail',
                'description_ar' => 'ملابس، إكسسوارات وبيع الأزياء',
                'icon' => 'shirt',
                'sort_order' => 3,
            ],
            [
                'name' => 'Technology & Electronics',
                'name_ar' => 'تقنية وإلكترونيات',
                'description' => 'IT solutions, electronics and tech products',
                'description_ar' => 'حلول تقنية، إلكترونيات ومنتجات تقنية',
                'icon' => 'laptop',
                'sort_order' => 4,
            ],
            [
                'name' => 'Health & Beauty',
                'name_ar' => 'صحة وجمال',
                'description' => 'Cosmetics, skincare, health products',
                'description_ar' => 'مستحضرات تجميل، عناية بالبشرة، منتجات صحية',
                'icon' => 'heart',
                'sort_order' => 5,
            ],
            [
                'name' => 'Real Estate',
                'name_ar' => 'عقارات',
                'description' => 'Property management, development and investment',
                'description_ar' => 'إدارة وتطوير واستثمار العقارات',
                'icon' => 'building',
                'sort_order' => 6,
            ],
            [
                'name' => 'Construction & Contracting',
                'name_ar' => 'مقاولات وبناء',
                'description' => 'Construction services and contracting',
                'description_ar' => 'خدمات البناء والمقاولات',
                'icon' => 'hammer',
                'sort_order' => 7,
            ],
            [
                'name' => 'Education & Training',
                'name_ar' => 'تعليم وتدريب',
                'description' => 'Educational services and professional training',
                'description_ar' => 'خدمات تعليمية وتدريب مهني',
                'icon' => 'graduation-cap',
                'sort_order' => 8,
            ],
            [
                'name' => 'Tourism & Hospitality',
                'name_ar' => 'سياحة وضيافة',
                'description' => 'Hotels, tourism services and hospitality',
                'description_ar' => 'فنادق، خدمات سياحية وضيافة',
                'icon' => 'plane',
                'sort_order' => 9,
            ],
            [
                'name' => 'Manufacturing',
                'name_ar' => 'تصنيع',
                'description' => 'Industrial manufacturing and production',
                'description_ar' => 'تصنيع وإنتاج صناعي',
                'icon' => 'factory',
                'sort_order' => 10,
            ],
            [
                'name' => 'Agriculture',
                'name_ar' => 'زراعة',
                'description' => 'Farming, agriculture and agricultural products',
                'description_ar' => 'زراعة ومنتجات زراعية',
                'icon' => 'sprout',
                'sort_order' => 11,
            ],
            [
                'name' => 'Automotive',
                'name_ar' => 'سيارات',
                'description' => 'Car sales, parts and automotive services',
                'description_ar' => 'بيع سيارات، قطع غيار وخدمات سيارات',
                'icon' => 'car',
                'sort_order' => 12,
            ],
            [
                'name' => 'Media & Advertising',
                'name_ar' => 'إعلام وإعلان',
                'description' => 'Media production, marketing and advertising',
                'description_ar' => 'إنتاج إعلامي، تسويق وإعلان',
                'icon' => 'megaphone',
                'sort_order' => 13,
            ],
            [
                'name' => 'Professional Services',
                'name_ar' => 'خدمات مهنية',
                'description' => 'Consulting, legal and professional services',
                'description_ar' => 'استشارات، خدمات قانونية ومهنية',
                'icon' => 'briefcase',
                'sort_order' => 14,
            ],
            [
                'name' => 'Other',
                'name_ar' => 'أخرى',
                'description' => 'Other business activity types',
                'description_ar' => 'أنواع أنشطة تجارية أخرى',
                'icon' => 'ellipsis',
                'sort_order' => 99,
            ],
        ];

        foreach ($types as $type) {
            BusinessActivityType::firstOrCreate(
                ['name' => $type['name']],
                array_merge($type, ['is_active' => true])
            );
        }
    }
}
