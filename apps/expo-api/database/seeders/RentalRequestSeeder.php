<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\RentalRequest;
use App\Models\Space;
use Illuminate\Database\Seeder;

class RentalRequestSeeder extends Seeder
{
    protected int $sequence = 0;

    public function run(): void
    {
        // Get approved business profiles
        $profiles = BusinessProfile::where('status', 'approved')->get();

        if ($profiles->isEmpty()) {
            $this->command->warn('No approved business profiles found. Run BusinessProfileSeeder first.');
            return;
        }

        // Get spaces from published events
        $spaces = Space::with('event')->whereHas('event', function ($q) {
            $q->where('status', 'published')
                ->where('end_date', '>=', now()->toDateString());
        })->get();

        if ($spaces->isEmpty()) {
            $this->command->warn('No spaces found. Run EventSeeder first.');
            return;
        }

        $rentalRequests = [];

        // Profile 1 (TechVentures - investor): Rental for tech event spaces
        $techProfile = $profiles->firstWhere('company_name', 'TechVentures Saudi');
        if ($techProfile) {
            $techSpaces = $spaces->filter(fn($s) => str_contains($s->location_code, 'AI-') || str_contains($s->location_code, 'CL-'));

            foreach ($techSpaces->take(2) as $space) {
                $event = $space->event;
                $startDate = $event->start_date->copy()->max(now()->addDay());
                $endDate = $event->end_date->copy();
                $days = max(1, $startDate->diffInDays($endDate) + 1);
                $totalPrice = $space->price_per_day ? $space->price_per_day * $days : $space->price_total;

                $rentalRequests[] = [
                    'request_number' => $this->nextRequestNumber(),
                    'space_id' => $space->id,
                    'user_id' => $techProfile->user_id,
                    'business_profile_id' => $techProfile->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'total_price' => $totalPrice,
                    'status' => 'approved',
                    'payment_status' => 'paid',
                    'paid_amount' => $totalPrice,
                    'first_payment_at' => now()->subDays(5),
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(5),
                    'notes' => 'Technology company booth for product demonstrations',
                ];
            }
        }

        // Profile 2 (Al-Salam Trading - merchant): Rental for food event
        $tradingProfile = $profiles->firstWhere('company_name', 'Al-Salam Trading');
        if ($tradingProfile) {
            $foodSpaces = $spaces->filter(fn($s) => str_contains($s->location_code, 'SC-') || str_contains($s->location_code, 'IN-'));

            foreach ($foodSpaces->take(2) as $space) {
                $event = $space->event;
                $startDate = $event->start_date->copy()->max(now()->addDay());
                $endDate = $event->end_date->copy();
                $days = max(1, $startDate->diffInDays($endDate) + 1);
                $totalPrice = $space->price_per_day ? $space->price_per_day * $days : $space->price_total;

                $rentalRequests[] = [
                    'request_number' => $this->nextRequestNumber(),
                    'space_id' => $space->id,
                    'user_id' => $tradingProfile->user_id,
                    'business_profile_id' => $tradingProfile->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'notes' => 'Food booth for traditional Saudi cuisine',
                ];
            }
        }

        // Profile 3 (Beauty World - merchant): Rental for fashion event
        $beautyProfile = $profiles->firstWhere('company_name', 'Beauty World SA');
        if ($beautyProfile) {
            $beautySpaces = $spaces->filter(fn($s) => str_contains($s->location_code, 'FD-') || str_contains($s->location_code, 'BC-'));

            foreach ($beautySpaces->take(1) as $space) {
                $event = $space->event;
                $startDate = $event->start_date->copy()->max(now()->addDay());
                $endDate = $event->end_date->copy();
                $days = max(1, $startDate->diffInDays($endDate) + 1);
                $totalPrice = $space->price_per_day ? $space->price_per_day * $days : $space->price_total;

                $rentalRequests[] = [
                    'request_number' => $this->nextRequestNumber(),
                    'space_id' => $space->id,
                    'user_id' => $beautyProfile->user_id,
                    'business_profile_id' => $beautyProfile->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'total_price' => $totalPrice,
                    'status' => 'approved',
                    'payment_status' => 'partial',
                    'paid_amount' => round($totalPrice * 0.5, 2),
                    'first_payment_at' => now()->subDays(2),
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(3),
                    'notes' => 'Cosmetics and beauty products display',
                ];
            }

            // Add a rejected rental request
            $rejectableSpace = $spaces->filter(fn($s) => str_contains($s->location_code, 'RP-'))->first();
            if ($rejectableSpace) {
                $event = $rejectableSpace->event;
                $startDate = $event->start_date->copy()->max(now()->addDay());
                $endDate = $event->end_date->copy();
                $days = max(1, $startDate->diffInDays($endDate) + 1);
                $totalPrice = $rejectableSpace->price_per_day ? $rejectableSpace->price_per_day * $days : $rejectableSpace->price_total;

                $rentalRequests[] = [
                    'request_number' => $this->nextRequestNumber(),
                    'space_id' => $rejectableSpace->id,
                    'user_id' => $beautyProfile->user_id,
                    'business_profile_id' => $beautyProfile->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'total_price' => $totalPrice,
                    'status' => 'rejected',
                    'payment_status' => 'pending',
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(1),
                    'rejection_reason' => 'Space type does not match business profile category.',
                ];
            }
        }

        foreach ($rentalRequests as $data) {
            RentalRequest::create($data);
        }

        $this->command->info('Created ' . count($rentalRequests) . ' rental requests.');
    }

    protected function nextRequestNumber(): string
    {
        $this->sequence++;
        $date = now()->format('Ymd');
        return sprintf('RR-%s-%05d', $date, $this->sequence);
    }
}
