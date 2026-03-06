<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();
            $table->string('user_id', 36)->index();

            // Polymorphic: Invoice, RentalContract, SponsorPayment, etc.
            $table->nullableUuidMorphs('payable');

            // Tap charge details
            $table->string('charge_id')->nullable()->unique()->comment('Tap charge ID (chg_xxx)');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('SAR');

            // Status tracking
            $table->string('status')->default('initiated')->index()
                ->comment('initiated, pending, captured, failed, cancelled, refunded');
            $table->string('payment_method')->nullable()->comment('VISA, MADA, KNET, etc.');
            $table->string('source')->nullable()->comment('Tap source ID');

            // Tap response data
            $table->string('gateway_reference')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('track_id')->nullable();
            $table->string('transaction_url')->nullable()->comment('Tap hosted payment page URL');

            // 3D Secure
            $table->boolean('three_d_secure')->default(true);

            // Webhook & redirect tracking
            $table->string('redirect_status')->nullable();
            $table->string('webhook_status')->nullable();

            // Error info
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            // Full Tap response (JSON)
            $table->json('tap_response')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
