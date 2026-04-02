<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PortalsSeeder extends Seeder
{
    /**
     * Run the database seeders for all three portals:
     * - Investor Portal
     * - Merchant Portal
     * - Sponsor Portal
     */
    public function run(): void
    {
        // Create required tables first
        $this->call([
            CreateInvestmentsTableSeeder::class,
            CreateCustomersTableSeeder::class,
            CreateSponsorshipsTableSeeder::class,
        ]);

        // Investor Portal Seeders
        $this->call([
            InvestorProfileSeeder::class,
        ]);

        // Merchant Portal Seeders
        $this->call([
            MerchantBusinessProfileSeeder::class,
            // SpaceBoothSeeder::class, // Skip - use existing spaces
            // RentalContractSeeder::class, // Skip - depends on spaces
        ]);

        // Sponsor Portal Seeders
        $this->call([
            SponsorSeeder::class,
            // SponsorContractSeeder::class, // Skip - uses undefined columns
            // SponsorPaymentSeeder::class, // Skip - depends on contracts
        ]);
    }
}
