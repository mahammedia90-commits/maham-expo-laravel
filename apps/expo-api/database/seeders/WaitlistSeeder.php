<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WaitlistSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Waitlist::create(['user_id' => 1, 'event_id' => 1, 'space_id' => 5, 'position' => 1, 'status' => 'waiting']);
        \App\Models\Waitlist::create(['user_id' => 2, 'event_id' => 1, 'space_id' => 5, 'position' => 2, 'status' => 'waiting']);
        \App\Models\Waitlist::create(['user_id' => 3, 'event_id' => 2, 'space_id' => 8, 'position' => 1, 'status' => 'approved']);
    }
}
