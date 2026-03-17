<?php

namespace Database\Seeders;

use App\Models\MemberType;
use Illuminate\Database\Seeder;

class MemberTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Manager',
                'name_ar' => 'مدير',
                'description' => 'Manages team operations and daily tasks',
                'description_ar' => 'يدير العمليات والمهام اليومية',
                'scope' => 'both',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cashier',
                'name_ar' => 'كاشير',
                'description' => 'Handles payments and financial transactions',
                'description_ar' => 'يتعامل مع المدفوعات والمعاملات المالية',
                'scope' => 'merchant',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sales Representative',
                'name_ar' => 'مندوب مبيعات',
                'description' => 'Responsible for sales and client relationships',
                'description_ar' => 'مسؤول عن المبيعات وعلاقات العملاء',
                'scope' => 'merchant',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Supervisor',
                'name_ar' => 'مشرف',
                'description' => 'Oversees operations and monitors staff',
                'description_ar' => 'يشرف على العمليات ويراقب الموظفين',
                'scope' => 'both',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Accountant',
                'name_ar' => 'محاسب',
                'description' => 'Manages financial records and reports',
                'description_ar' => 'يدير السجلات المالية والتقارير',
                'scope' => 'both',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Driver',
                'name_ar' => 'سائق',
                'description' => 'Handles transportation and delivery',
                'description_ar' => 'مسؤول عن النقل والتوصيل',
                'scope' => 'merchant',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Maintenance Staff',
                'name_ar' => 'فني صيانة',
                'description' => 'Responsible for maintenance and technical support',
                'description_ar' => 'مسؤول عن الصيانة والدعم الفني',
                'scope' => 'investor',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Security Guard',
                'name_ar' => 'حارس أمن',
                'description' => 'Provides security for spaces and premises',
                'description_ar' => 'يوفر الأمن للمساحات والمنشآت',
                'scope' => 'investor',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Other',
                'name_ar' => 'أخرى',
                'description' => 'Other team member types',
                'description_ar' => 'أنواع أخرى من أعضاء الفريق',
                'scope' => 'both',
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($types as $type) {
            MemberType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
