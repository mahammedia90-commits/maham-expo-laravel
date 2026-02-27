<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Rating;
use App\Models\RentalRequest;
use App\Models\Space;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        if (Rating::count() > 0) {
            $this->command->info('Ratings already seeded, skipping.');
            return;
        }

        $events = Event::where('status', 'published')->get();
        $spaces = Space::whereIn('status', ['available', 'rented', 'reserved'])->get();
        $rentalRequests = RentalRequest::where('status', 'approved')->get();

        if ($events->isEmpty()) {
            $this->command->warn('No events found. Run EventSeeder first.');
            return;
        }

        $ratings = [];

        // ===== Event Ratings =====

        // User 10 rates Saudi Tech Expo (5 stars - excellent)
        $techExpo = $events->firstWhere('name', 'Saudi Tech Expo 2025');
        if ($techExpo) {
            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'rateable_type' => 'App\Models\Event',
                'rateable_id' => $techExpo->id,
                'type' => 'event',
                'overall_rating' => 5,
                'cleanliness_rating' => 5,
                'location_rating' => 5,
                'facilities_rating' => 4,
                'value_rating' => 4,
                'communication_rating' => 5,
                'comment' => 'Excellent technology exhibition! Great organization and facilities. The AI section was particularly impressive.',
                'comment_ar' => 'معرض تقني ممتاز! تنظيم ومرافق رائعة. قسم الذكاء الاصطناعي كان مميزاً بشكل خاص.',
                'is_approved' => true,
            ];
        }

        // User 11 rates Saudi Tech Expo (4 stars)
        if ($techExpo) {
            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'rateable_type' => 'App\Models\Event',
                'rateable_id' => $techExpo->id,
                'type' => 'event',
                'overall_rating' => 4,
                'cleanliness_rating' => 4,
                'location_rating' => 5,
                'facilities_rating' => 4,
                'value_rating' => 3,
                'communication_rating' => 4,
                'comment' => 'Good event with great location. Parking could be improved. Wide variety of exhibitors.',
                'comment_ar' => 'فعالية جيدة بموقع رائع. يمكن تحسين المواقف. تنوع كبير في العارضين.',
                'is_approved' => true,
            ];
        }

        // User 14 rates Glamour Fashion Week (5 stars)
        $fashionWeek = $events->firstWhere('name', 'Glamour Fashion Week');
        if ($fashionWeek) {
            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'rateable_type' => 'App\Models\Event',
                'rateable_id' => $fashionWeek->id,
                'type' => 'event',
                'overall_rating' => 5,
                'cleanliness_rating' => 5,
                'location_rating' => 4,
                'facilities_rating' => 5,
                'value_rating' => 5,
                'communication_rating' => 5,
                'comment' => 'Amazing fashion week! The beauty workshops were outstanding. Highly recommend for anyone in the fashion industry.',
                'comment_ar' => 'أسبوع أزياء مذهل! ورش التجميل كانت رائعة. أنصح به بشدة لأي شخص في صناعة الأزياء.',
                'is_approved' => true,
            ];
        }

        // User 12 rates Food Festival (3 stars - pending approval)
        $foodFestival = $events->firstWhere('name', 'Saudi Food Festival 2025');
        if ($foodFestival) {
            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'rateable_type' => 'App\Models\Event',
                'rateable_id' => $foodFestival->id,
                'type' => 'event',
                'overall_rating' => 3,
                'cleanliness_rating' => 3,
                'location_rating' => 4,
                'facilities_rating' => 3,
                'value_rating' => 3,
                'communication_rating' => 2,
                'comment' => 'Average event. Food variety was good but the venue was too crowded. Communication could be better.',
                'comment_ar' => 'فعالية متوسطة. تنوع الطعام كان جيداً لكن المكان كان مزدحماً جداً. يمكن تحسين التواصل.',
                'is_approved' => false,
            ];
        }

        // ===== Space Ratings =====

        // User 10 rates AI booth (space rating linked to rental)
        $aiSpaces = $spaces->filter(fn($s) => str_contains($s->location_code ?? '', 'AI-'));
        if ($aiSpaces->isNotEmpty()) {
            $aiSpace = $aiSpaces->first();
            $relatedRental = $rentalRequests->firstWhere('space_id', $aiSpace->id);

            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'rateable_type' => 'App\Models\Space',
                'rateable_id' => $aiSpace->id,
                'type' => 'space',
                'overall_rating' => 5,
                'cleanliness_rating' => 5,
                'location_rating' => 5,
                'facilities_rating' => 4,
                'value_rating' => 4,
                'communication_rating' => 5,
                'comment' => 'Perfect booth location with excellent foot traffic. All services were reliable. Great value for money.',
                'comment_ar' => 'موقع كشك مثالي مع حركة مرور ممتازة. جميع الخدمات كانت موثوقة. قيمة ممتازة مقابل المال.',
                'is_approved' => true,
                'rental_request_id' => $relatedRental?->id,
            ];
        }

        // User 14 rates Fashion booth
        $fashionSpaces = $spaces->filter(fn($s) => str_contains($s->location_code ?? '', 'FD-'));
        if ($fashionSpaces->isNotEmpty()) {
            $fashionSpace = $fashionSpaces->first();
            $relatedRental = $rentalRequests->firstWhere('space_id', $fashionSpace->id);

            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'rateable_type' => 'App\Models\Space',
                'rateable_id' => $fashionSpace->id,
                'type' => 'space',
                'overall_rating' => 4,
                'cleanliness_rating' => 5,
                'location_rating' => 4,
                'facilities_rating' => 4,
                'value_rating' => 3,
                'communication_rating' => 4,
                'comment' => 'Great space for displaying fashion products. Clean and well-maintained. A bit pricey but worth it.',
                'comment_ar' => 'مساحة رائعة لعرض منتجات الأزياء. نظيفة ومصانة جيداً. السعر مرتفع قليلاً لكنها تستحق.',
                'is_approved' => true,
                'rental_request_id' => $relatedRental?->id,
            ];
        }

        // User 11 rates Food booth
        $foodSpaces = $spaces->filter(fn($s) => str_contains($s->location_code ?? '', 'SC-'));
        if ($foodSpaces->isNotEmpty()) {
            $foodSpace = $foodSpaces->first();
            $relatedRental = $rentalRequests->firstWhere('space_id', $foodSpace->id);

            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'rateable_type' => 'App\Models\Space',
                'rateable_id' => $foodSpace->id,
                'type' => 'space',
                'overall_rating' => 4,
                'cleanliness_rating' => 4,
                'location_rating' => 5,
                'facilities_rating' => 3,
                'value_rating' => 4,
                'communication_rating' => 4,
                'comment' => 'Good location in the food section. Water and electricity connections were reliable. Storage area could be larger.',
                'comment_ar' => 'موقع جيد في قسم الطعام. توصيلات المياه والكهرباء كانت موثوقة. مساحة التخزين يمكن أن تكون أكبر.',
                'is_approved' => true,
                'rental_request_id' => $relatedRental?->id,
            ];
        }

        // User 10 rates Real Estate booth (2 stars - not approved yet)
        $reSpaces = $spaces->filter(fn($s) => str_contains($s->location_code ?? '', 'RP-'));
        if ($reSpaces->isNotEmpty()) {
            $reSpace = $reSpaces->first();

            $ratings[] = [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'rateable_type' => 'App\Models\Space',
                'rateable_id' => $reSpace->id,
                'type' => 'space',
                'overall_rating' => 2,
                'cleanliness_rating' => 3,
                'location_rating' => 2,
                'facilities_rating' => 2,
                'value_rating' => 1,
                'communication_rating' => 3,
                'comment' => 'Overpriced for the space provided. Wi-Fi was unreliable. Location within the venue was not ideal.',
                'comment_ar' => 'سعر مبالغ فيه للمساحة المقدمة. الواي فاي كان غير مستقر. الموقع داخل المعرض لم يكن مثالياً.',
                'is_approved' => false,
            ];
        }

        foreach ($ratings as $rating) {
            Rating::create($rating);
        }

        $this->command->info('Created ' . count($ratings) . ' ratings.');
    }
}
