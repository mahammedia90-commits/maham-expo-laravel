<?php

namespace Database\Seeders;

use App\Models\RentalContract;
use App\Models\RentalRequest;
use Illuminate\Database\Seeder;

class RentalContractSeeder extends Seeder
{
    public function run(): void
    {
        if (RentalContract::count() > 0) {
            $this->command->info('Rental contracts already seeded, skipping.');
            return;
        }

        // Get approved rental requests
        $approvedRequests = RentalRequest::where('status', 'approved')
            ->with(['space', 'space.event'])
            ->get();

        if ($approvedRequests->isEmpty()) {
            $this->command->warn('No approved rental requests found. Run RentalRequestSeeder first.');
            return;
        }

        $contractCount = 0;

        foreach ($approvedRequests as $request) {
            $space = $request->space;
            $event = $space?->event;

            if (!$space || !$event) {
                continue;
            }

            // Determine contract status based on payment status
            $contractStatus = match ($request->payment_status) {
                'paid' => 'active',
                'partial' => 'pending',
                default => 'draft',
            };

            $isSigned = $request->payment_status === 'paid';

            $contract = RentalContract::create([
                'rental_request_id' => $request->id,
                'event_id' => $event->id,
                'space_id' => $space->id,
                'merchant_id' => $request->user_id,
                'investor_id' => '00000000-0000-0000-0000-000000000010', // TechVentures as investor
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_amount' => $request->total_price,
                'paid_amount' => $request->paid_amount ?? 0,
                'payment_status' => $request->payment_status,
                'status' => $contractStatus,
                'terms' => "Rental contract for space {$space->name} at {$event->name}. This contract governs the rental of exhibition space for the duration of the event. Both parties agree to comply with event regulations and safety standards.",
                'terms_ar' => "عقد استئجار للمساحة {$space->name_ar} في {$event->name_ar}. يحكم هذا العقد استئجار مساحة المعرض طوال مدة الفعالية. يوافق الطرفان على الامتثال لأنظمة الفعالية ومعايير السلامة.",
                'signed_at' => $isSigned ? now()->subDays(4) : null,
                'signed_by_merchant' => $isSigned ? $request->user_id : null,
                'signed_by_investor' => $isSigned ? '00000000-0000-0000-0000-000000000010' : null,
                'approved_by' => $contractStatus === 'active' ? '00000000-0000-0000-0000-000000000099' : null,
                'approved_at' => $contractStatus === 'active' ? now()->subDays(4) : null,
                'admin_notes' => $contractStatus === 'active' ? 'Contract reviewed and approved. All terms are in order.' : null,
            ]);

            $contractCount++;
            $this->command->info("Created contract: {$contract->contract_number} ({$contractStatus})");
        }

        $this->command->info("Created {$contractCount} rental contracts.");
    }
}
