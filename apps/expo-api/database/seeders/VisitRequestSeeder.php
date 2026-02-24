<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\VisitRequest;
use Illuminate\Database\Seeder;

class VisitRequestSeeder extends Seeder
{
    protected int $sequence = 0;

    public function run(): void
    {
        // Get published events (ongoing or upcoming)
        $events = Event::where('status', 'published')
            ->where('end_date', '>=', now()->toDateString())
            ->get();

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

        $visitRequests = [];

        // Create visit requests for ongoing/upcoming events
        foreach ($events->take(3) as $event) {
            $baseVisitDate = $event->start_date->copy();
            if ($baseVisitDate->lt(now())) {
                $baseVisitDate = now()->copy();
            }

            // Pending visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $event->id,
                'user_id' => $userIds[0],
                'visit_date' => $baseVisitDate->copy()->addDays(2)->toDateString(),
                'visit_time' => '10:00',
                'visitors_count' => 3,
                'contact_phone' => '0501234567',
                'notes' => 'Looking forward to exploring AI section',
                'status' => 'pending',
            ];

            // Approved visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $event->id,
                'user_id' => $userIds[1],
                'visit_date' => $baseVisitDate->copy()->addDays(3)->toDateString(),
                'visit_time' => '14:00',
                'visitors_count' => 2,
                'contact_phone' => '0567891234',
                'status' => 'approved',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(1),
            ];

            // Another pending visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $event->id,
                'user_id' => $userIds[2],
                'visit_date' => $baseVisitDate->copy()->addDays(5)->toDateString(),
                'visitors_count' => 5,
                'contact_phone' => '0559876543',
                'notes' => 'Group visit for market research',
                'status' => 'pending',
            ];
        }

        // Additional statuses for the first event
        if ($ongoingEvent = $events->first()) {
            $baseDate = $ongoingEvent->start_date->copy();
            if ($baseDate->lt(now())) {
                $baseDate = now()->copy();
            }

            // Completed visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $ongoingEvent->id,
                'user_id' => $userIds[3],
                'visit_date' => $baseDate->copy()->addDay()->toDateString(),
                'visit_time' => '11:00',
                'visitors_count' => 1,
                'contact_phone' => '0512345678',
                'status' => 'completed',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(2),
            ];

            // Cancelled visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $ongoingEvent->id,
                'user_id' => $userIds[0],
                'visit_date' => $baseDate->copy()->addDays(4)->toDateString(),
                'visitors_count' => 2,
                'contact_phone' => '0501234567',
                'status' => 'cancelled',
            ];

            // Rejected visit request
            $visitRequests[] = [
                'request_number' => $this->nextRequestNumber(),
                'event_id' => $ongoingEvent->id,
                'user_id' => $userIds[2],
                'visit_date' => $baseDate->copy()->addDays(6)->toDateString(),
                'visitors_count' => 8,
                'contact_phone' => '0559876543',
                'notes' => 'Large group visit',
                'status' => 'rejected',
                'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                'reviewed_at' => now()->subDays(1),
                'rejection_reason' => 'Maximum visitors count exceeded for the requested date.',
            ];
        }

        if (VisitRequest::count() > 0) {
            $this->command->info('Visit requests already seeded, skipping.');
            return;
        }

        foreach ($visitRequests as $data) {
            VisitRequest::create($data);
        }

        $this->command->info('Created ' . count($visitRequests) . ' visit requests.');
    }

    protected function nextRequestNumber(): string
    {
        $this->sequence++;
        $date = now()->format('Ymd');
        return sprintf('VR-%s-%05d', $date, $this->sequence);
    }
}
