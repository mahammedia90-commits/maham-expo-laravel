<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('in_app_enabled')->default(true);

            // أنواع الإشعارات
            $table->boolean('notify_request_updates')->default(true); // تحديثات الطلبات
            $table->boolean('notify_payment_reminders')->default(true); // تذكيرات الدفع
            $table->boolean('notify_event_updates')->default(true); // تحديثات الفعاليات
            $table->boolean('notify_contract_milestones')->default(true); // مراحل العقود
            $table->boolean('notify_promotions')->default(true); // العروض والتسويق
            $table->boolean('notify_support_updates')->default(true); // تحديثات الدعم
            $table->boolean('notify_ratings')->default(true); // التقييمات

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
