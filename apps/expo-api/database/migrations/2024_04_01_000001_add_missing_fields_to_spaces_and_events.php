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
            $table->string('allowed_business_type')->nullable()->after('rental_duration'); // نوع النشاط التجاري المسموح
            $table->text('investor_conditions')->nullable()->after('allowed_business_type'); // شروط المستثمر
            $table->text('investor_conditions_ar')->nullable()->after('investor_conditions');
            $table->text('operational_notes')->nullable()->after('investor_conditions_ar'); // ملاحظات تشغيلية
            $table->text('operational_notes_ar')->nullable()->after('operational_notes');
        });

        // Add missing fields to events table
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('expected_visitors')->nullable()->after('views_count'); // عدد الزوار المتوقع
            $table->decimal('investment_opportunity_rating', 3, 1)->nullable()->after('expected_visitors'); // تقييم فرصة الاستثمار (0.0 - 5.0)
            $table->json('images_360')->nullable()->after('images'); // صور 360
            $table->text('promotional_video')->nullable()->after('images_360'); // فيديو ترويجي
        });
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn(['allowed_business_type', 'investor_conditions', 'investor_conditions_ar', 'operational_notes', 'operational_notes_ar']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['expected_visitors', 'investment_opportunity_rating', 'images_360', 'promotional_video']);
        });
    }
};
