<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Events: Optimize search queries (name columns used in LIKE search)
        Schema::table('events', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('city_id');
            $table->index('views_count');
        });

        // Spaces: Add index for service_id FK used in filtering
        Schema::table('service_space', function (Blueprint $table) {
            $table->index('service_id');
        });

        // Business Profiles: Optimize admin search by business_type
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->index('business_type');
            $table->index('created_at');
        });

        // Visit Requests: Optimize user queries
        Schema::table('visit_requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });

        // Rental Requests: Optimize user and space queries
        Schema::table('rental_requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('space_id');
            $table->index('business_profile_id');
            $table->index('created_at');
        });

        // Favorites: Optimize polymorphic queries
        Schema::table('favorites', function (Blueprint $table) {
            $table->index('created_at');
        });

        // Notifications: Optimize unread count queries
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['views_count']);
        });

        Schema::table('service_space', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
        });

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropIndex(['business_type']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('visit_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['space_id']);
            $table->dropIndex(['business_profile_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
