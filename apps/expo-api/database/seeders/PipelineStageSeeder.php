<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['name_ar' => 'جديد', 'name_en' => 'New', 'order' => 1, 'sla_hours' => 24, 'color' => '#3498db'],
            ['name_ar' => 'قيد المراجعة', 'name_en' => 'In Review', 'order' => 2, 'sla_hours' => 48, 'color' => '#f39c12'],
            ['name_ar' => 'معتمد', 'name_en' => 'Approved', 'order' => 3, 'sla_hours' => 72, 'color' => '#27ae60'],
            ['name_ar' => 'مكتمل', 'name_en' => 'Completed', 'order' => 4, 'sla_hours' => 0, 'color' => '#2ecc71'],
        ];

        foreach ($stages as $stage) {
            \App\Models\PipelineStage::create($stage);
        }
    }
}
