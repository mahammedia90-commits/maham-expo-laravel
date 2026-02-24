<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Trade Exhibitions',
                'name_ar' => 'المعارض التجارية',
                'icon' => 'store',
                'description' => 'Commercial and trade exhibitions',
                'description_ar' => 'المعارض التجارية والأسواق',
                'sort_order' => 1,
            ],
            [
                'name' => 'Technology & Innovation',
                'name_ar' => 'التقنية والابتكار',
                'icon' => 'cpu',
                'description' => 'Technology, innovation, and startup exhibitions',
                'description_ar' => 'معارض التقنية والابتكار والشركات الناشئة',
                'sort_order' => 2,
            ],
            [
                'name' => 'Food & Beverages',
                'name_ar' => 'الأغذية والمشروبات',
                'icon' => 'utensils',
                'description' => 'Food, beverages, and hospitality exhibitions',
                'description_ar' => 'معارض الأغذية والمشروبات والضيافة',
                'sort_order' => 3,
            ],
            [
                'name' => 'Fashion & Beauty',
                'name_ar' => 'الأزياء والجمال',
                'icon' => 'shirt',
                'description' => 'Fashion, beauty, and cosmetics exhibitions',
                'description_ar' => 'معارض الأزياء والجمال ومستحضرات التجميل',
                'sort_order' => 4,
            ],
            [
                'name' => 'Real Estate',
                'name_ar' => 'العقارات',
                'icon' => 'building',
                'description' => 'Real estate and construction exhibitions',
                'description_ar' => 'معارض العقارات والبناء',
                'sort_order' => 5,
            ],
            [
                'name' => 'Automotive',
                'name_ar' => 'السيارات',
                'icon' => 'car',
                'description' => 'Automotive and transportation exhibitions',
                'description_ar' => 'معارض السيارات والنقل',
                'sort_order' => 6,
            ],
            [
                'name' => 'Health & Medical',
                'name_ar' => 'الصحة والطب',
                'icon' => 'heart-pulse',
                'description' => 'Healthcare and medical exhibitions',
                'description_ar' => 'معارض الرعاية الصحية والطب',
                'sort_order' => 7,
            ],
            [
                'name' => 'Education & Training',
                'name_ar' => 'التعليم والتدريب',
                'icon' => 'graduation-cap',
                'description' => 'Education, training, and career exhibitions',
                'description_ar' => 'معارض التعليم والتدريب والتوظيف',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
