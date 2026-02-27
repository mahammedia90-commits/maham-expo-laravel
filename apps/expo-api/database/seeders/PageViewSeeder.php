<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Page;
use App\Models\PageView;
use App\Models\Space;
use Illuminate\Database\Seeder;

class PageViewSeeder extends Seeder
{
    public function run(): void
    {
        if (PageView::count() > 0) {
            $this->command->info('Page views already seeded, skipping.');
            return;
        }

        $viewCount = 0;

        // User IDs for generating views
        $userIds = [
            '00000000-0000-0000-0000-000000000010',
            '00000000-0000-0000-0000-000000000011',
            '00000000-0000-0000-0000-000000000012',
            '00000000-0000-0000-0000-000000000013',
            '00000000-0000-0000-0000-000000000014',
            '00000000-0000-0000-0000-000000000015',
            null, // Anonymous visitors
        ];

        $platforms = ['web', 'mobile', 'api'];

        $userAgents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Safari/605.1',
            'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0 Mobile',
            'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
        ];

        $referrers = [
            'https://google.com',
            'https://twitter.com',
            'https://instagram.com',
            null,
            'https://mahamexpo.sa',
        ];

        // ===== Event Page Views =====
        $events = Event::where('status', 'published')->get();

        foreach ($events as $event) {
            // Generate 5-15 views per event over the last 30 days
            $numViews = rand(5, 15);
            for ($i = 0; $i < $numViews; $i++) {
                PageView::create([
                    'user_id' => $userIds[array_rand($userIds)],
                    'viewable_type' => Event::class,
                    'viewable_id' => $event->id,
                    'platform' => $platforms[array_rand($platforms)],
                    'ip_address' => '192.168.1.' . rand(1, 254),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'referrer' => $referrers[array_rand($referrers)],
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
                $viewCount++;
            }
        }

        // ===== Space Page Views =====
        $spaces = Space::whereIn('status', ['available', 'rented', 'reserved'])->take(10)->get();

        foreach ($spaces as $space) {
            // Generate 3-8 views per space
            $numViews = rand(3, 8);
            for ($i = 0; $i < $numViews; $i++) {
                PageView::create([
                    'user_id' => $userIds[array_rand($userIds)],
                    'viewable_type' => Space::class,
                    'viewable_id' => $space->id,
                    'platform' => $platforms[array_rand($platforms)],
                    'ip_address' => '10.0.0.' . rand(1, 254),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'referrer' => $referrers[array_rand($referrers)],
                    'created_at' => now()->subDays(rand(0, 20))->subHours(rand(0, 23)),
                ]);
                $viewCount++;
            }
        }

        // ===== Static Page Views =====
        $pages = Page::where('is_active', true)->get();

        foreach ($pages as $page) {
            // Generate 2-10 views per page
            $numViews = rand(2, 10);
            for ($i = 0; $i < $numViews; $i++) {
                PageView::create([
                    'user_id' => $userIds[array_rand($userIds)],
                    'viewable_type' => Page::class,
                    'viewable_id' => $page->id,
                    'platform' => $platforms[array_rand($platforms)],
                    'ip_address' => '172.16.0.' . rand(1, 254),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'referrer' => $referrers[array_rand($referrers)],
                    'created_at' => now()->subDays(rand(0, 60))->subHours(rand(0, 23)),
                ]);
                $viewCount++;
            }
        }

        $this->command->info("Created {$viewCount} page views.");
    }
}
