<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create customers table if it doesn't exist
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('type')->nullable(); // merchant, investor, sponsor, etc
                $table->string('sector')->nullable();
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->string('country')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('createdAt')->nullable();
                $table->timestamp('updatedAt')->nullable();
            });
        }
    }
}
