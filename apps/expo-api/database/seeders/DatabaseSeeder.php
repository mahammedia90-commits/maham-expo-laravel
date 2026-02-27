<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * Order matters — each seeder depends on data from the ones above it.
     */
    public function run(): void
    {
        $this->call([
            // 1. Base data (no dependencies)
            CategorySeeder::class,
            CitySeeder::class,
            ServiceSeeder::class,
            FaqSeeder::class,
            PageSeeder::class,

            // 2. Events (depends: Categories, Cities → also creates Sections & Spaces)
            EventSeeder::class,

            // 3. Business profiles (depends: users from auth service)
            BusinessProfileSeeder::class,

            // 4. Requests (depends: Events, Spaces, BusinessProfiles)
            VisitRequestSeeder::class,
            RentalRequestSeeder::class,

            // 5. Contracts (depends: approved RentalRequests)
            RentalContractSeeder::class,

            // 6. Sponsors ecosystem (depends: Events → Packages, Sponsors, Contracts, Payments, Benefits, Assets, Tracking)
            SponsorSeeder::class,

            // 7. Invoices (depends: RentalContracts, SponsorContracts)
            InvoiceSeeder::class,

            // 8. User engagement (depends: Events, Spaces, Users)
            FavoriteSeeder::class,
            RatingSeeder::class,
            NotificationSeeder::class,
            NotificationPreferenceSeeder::class,

            // 9. Banners & Content (depends: admin user)
            BannerSeeder::class,

            // 10. Support (depends: Users → also creates TicketReplies)
            SupportTicketSeeder::class,

            // 11. Analytics (depends: Events, Spaces, Pages)
            PageViewSeeder::class,
        ]);
    }
}
