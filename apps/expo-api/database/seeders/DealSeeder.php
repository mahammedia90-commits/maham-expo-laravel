<?php namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Deal;
class DealSeeder extends Seeder
{
    public function run(): void
    {
        Deal::create(['lead_id' => 2, 'stage_id' => 1, 'value' => 150000, 'close_probability' => 70, 'assigned_to' => null, 'days_in_stage' => 3, 'is_at_risk' => false]);
        Deal::create(['lead_id' => 3, 'stage_id' => 2, 'value' => 75000, 'close_probability' => 45, 'assigned_to' => null, 'days_in_stage' => 8, 'is_at_risk' => true]);
        Deal::create(['lead_id' => 4, 'stage_id' => 3, 'value' => 250000, 'close_probability' => 85, 'assigned_to' => null, 'days_in_stage' => 15, 'is_at_risk' => false]);
    }
}
