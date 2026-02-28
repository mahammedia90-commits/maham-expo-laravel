<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onesignal_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('subscription_id')->unique()->comment('OneSignal subscription ID');
            $table->string('push_token')->nullable()->comment('Device push token (APNs/FCM)');
            $table->string('type')->default('AndroidPush')->comment('Subscription type: AndroidPush, iOSPush, WebPush, etc.');
            $table->string('device_model')->nullable();
            $table->string('device_os')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'enabled']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onesignal_subscriptions');
    }
};
