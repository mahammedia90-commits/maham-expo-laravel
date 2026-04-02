<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateInvestmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create investments table if it doesn't exist
        if (!Schema::hasTable('investments')) {
            Schema::create('investments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('company')->nullable();
                $table->string('sector')->nullable();
                $table->decimal('investment_amount', 15, 2)->default(0);
                $table->decimal('portfolio_value', 15, 2)->default(0);
                $table->decimal('roi_percentage', 5, 2)->default(0);
                $table->enum('status', ['active', 'pending', 'inactive'])->default('active');
                $table->timestamp('createdAt')->nullable();
                $table->timestamp('updatedAt')->nullable();
            });
        }
    }
}
