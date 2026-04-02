<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateMissingTables extends Seeder
{
    public function run(): void
    {
        // Create waitlist table if it doesn't exist
        if (!Schema::hasTable('waitlist')) {
            Schema::create('waitlist', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('space_id')->nullable();
                $table->integer('position');
                $table->enum('status', ['waiting', 'approved', 'rejected', 'expired'])->default('waiting');
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();
            });
            echo "✓ Waitlist table created\n";
        }

        // Create opportunities table if it doesn't exist
        if (!Schema::hasTable('opportunities')) {
            Schema::create('opportunities', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['investment', 'sponsorship', 'merchant', 'partnership']);
                $table->enum('status', ['open', 'in_review', 'closed'])->default('open');
                $table->decimal('value', 15, 2)->nullable();
                $table->unsignedBigInteger('event_id')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->timestamp('deadline')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            echo "✓ Opportunities table created\n";
        }
    }
}
