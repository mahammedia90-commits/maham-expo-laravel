<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_payment_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');

            // Installment Details
            $table->unsignedInteger('installment_number');
            $table->string('label')->nullable();
            $table->string('label_ar')->nullable();

            // Amounts
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            // Schedule
            $table->date('due_date');
            $table->unsignedInteger('grace_period_days')->default(7);

            // Status
            $table->enum('status', ['upcoming', 'due', 'paid', 'partial', 'overdue', 'cancelled'])->default('upcoming');
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->timestamp('paid_at')->nullable();

            // Penalty
            $table->decimal('penalty_rate', 5, 2)->default(2);
            $table->decimal('penalty_amount', 14, 2)->default(0);
            $table->timestamp('penalty_applied_at')->nullable();

            // Invoice
            $table->uuid('invoice_id')->nullable();
            $table->string('invoice_number')->nullable();

            // Meta
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();

            // Unique Constraint
            $table->unique(['contract_id', 'installment_number']);

            // Indexes
            $table->index('contract_id');
            $table->index('status');
            $table->index('due_date');
            $table->index(['contract_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_payment_plans');
    }
};
