<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing fields to spaces table
        Schema::table('spaces', function (Blueprint $table) {
            if (!Schema::hasColumn('spaces', 'allowed_business_type')) {
                $table->string('allowed_business_type')->nullable()->after('rental_duration');
            }
            if (!Schema::hasColumn('spaces', 'investor_conditions')) {
                $table->text('investor_conditions')->nullable()->after('allowed_business_type');
            }
            if (!Schema::hasColumn('spaces', 'investor_conditions_ar')) {
                $table->text('investor_conditions_ar')->nullable()->after('investor_conditions');
            }
            if (!Schema::hasColumn('spaces', 'operational_notes')) {
                $table->text('operational_notes')->nullable()->after('investor_conditions_ar');
            }
            if (!Schema::hasColumn('spaces', 'operational_notes_ar')) {
                $table->text('operational_notes_ar')->nullable()->after('operational_notes');
            }
        });

        // Add missing fields to events table
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'expected_visitors')) {
                $table->unsignedInteger('expected_visitors')->nullable()->after('views_count');
            }
            if (!Schema::hasColumn('events', 'investment_opportunity_rating')) {
                $table->decimal('investment_opportunity_rating', 3, 1)->nullable()->after('expected_visitors');
            }
            if (!Schema::hasColumn('events', 'images_360')) {
                $table->json('images_360')->nullable()->after('images');
            }
            if (!Schema::hasColumn('events', 'promotional_video')) {
                $table->text('promotional_video')->nullable()->after('images_360');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $columns = ['allowed_business_type', 'investor_conditions', 'investor_conditions_ar', 'operational_notes', 'operational_notes_ar'];
            $existing = array_filter($columns, fn($col) => Schema::hasColumn('spaces', $col));
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $columns = ['expected_visitors', 'investment_opportunity_rating', 'images_360', 'promotional_video'];
            $existing = array_filter($columns, fn($col) => Schema::hasColumn('events', $col));
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
