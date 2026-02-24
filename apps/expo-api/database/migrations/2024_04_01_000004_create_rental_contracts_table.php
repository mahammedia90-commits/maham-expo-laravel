<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('contract_number')->unique(); // RC-YYYYMMDD-00001
            $table->foreignUuid('rental_request_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->uuid('space_id');
            $table->uuid('merchant_id'); // التاجر (المستأجر)
            $table->uuid('investor_id'); // المستثمر (المؤجر)
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['draft', 'pending', 'active', 'expired', 'cancelled', 'terminated'])->default('draft');
            $table->text('terms')->nullable();
            $table->text('terms_ar')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->uuid('signed_by_merchant')->nullable();
            $table->uuid('signed_by_investor')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('documents')->nullable(); // مستندات مرفقة
            $table->timestamps();
            $table->softDeletes();

            $table->index('rental_request_id');
            $table->index('event_id');
            $table->index('merchant_id');
            $table->index('investor_id');
            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_contracts');
    }
};
