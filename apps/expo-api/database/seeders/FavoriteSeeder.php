<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Favorite;
use App\Models\Space;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::where('status', 'published')->get();
        $spaces = Space::where('status', 'available')->get();

        if ($events->isEmpty()) {
            $this->command->warn('No published events found. Run EventSeeder first.');
            return;
        }

        $userIds = [
            '00000000-0000-0000-0000-000000000010',
            '00000000-0000-0000-0000-000000000011',
            '00000000-0000-0000-0000-000000000012',
            '00000000-0000-0000-0000-000000000014',
        ];

        $favorites = [];

        // User 10 favorites (events and spaces)
        foreach ($events->take(3) as $event) {
            $favorites[] = [
                'user_id' => $userIds[0],
                'favoritable_type' => 'App\Models\Event',
                'favoritable_id' => $event->id,
            ];
        }
        foreach ($spaces->take(2) as $space) {
            $favorites[] = [
                'user_id' => $userIds[0],
                'favoritable_type' => 'App\Models\Space',
                'favoritable_id' => $space->id,
            ];
        }

        // User 11 favorites
        foreach ($events->take(2) as $event) {
            $favorites[] = [
                'user_id' => $userIds[1],
                'favoritable_type' => 'App\Models\Event',
                'favoritable_id' => $event->id,
            ];
        }

        // User 12 favorites
        if ($events->first()) {
            $favorites[] = [
                'user_id' => $userIds[2],
                'favoritable_type' => 'App\Models\Event',
                'favoritable_id' => $events->first()->id,
            ];
        }

        // User 14 favorites (fashion events and spaces)
        foreach ($events->skip(2)->take(2) as $event) {
            $favorites[] = [
                'user_id' => $userIds[3],
                'favoritable_type' => 'App\Models\Event',
                'favoritable_id' => $event->id,
            ];
        }
        foreach ($spaces->skip(2)->take(3) as $space) {
            $favorites[] = [
                'user_id' => $userIds[3],
                'favoritable_type' => 'App\Models\Space',
                'favoritable_id' => $space->id,
            ];
        }

        foreach ($favorites as $favorite) {
            Favorite::create($favorite);
        }

        $this->command->info('Created ' . count($favorites) . ' favorites.');
    }
}
