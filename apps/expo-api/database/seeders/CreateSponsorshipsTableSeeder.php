<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateSponsorshipsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create sponsorships table if it doesn't exist
        if (!Schema::hasTable('sponsorships')) {
            Schema::create('sponsorships', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('company_type')->nullable();
                $table->string('sponsorship_tier')->nullable();
                $table->decimal('sponsorship_amount', 15, 2)->default(0);
                $table->date('contract_start_date')->nullable();
                $table->date('contract_end_date')->nullable();
                $table->string('logo_url')->nullable();
                $table->string('status')->default('active');
                $table->string('contact_person')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->timestamp('createdAt')->nullable();
                $table->timestamp('updatedAt')->nullable();
            });
        }
    }
}
