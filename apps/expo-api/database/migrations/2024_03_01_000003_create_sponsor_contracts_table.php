<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('sponsor_package_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['draft', 'pending', 'active', 'completed', 'cancelled'])->default('draft');
            $table->text('terms')->nullable();
            $table->text('terms_ar')->nullable();
            $table->dateTime('signed_at')->nullable();
            $table->string('signed_by')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sponsor_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_contracts');
    }
};
