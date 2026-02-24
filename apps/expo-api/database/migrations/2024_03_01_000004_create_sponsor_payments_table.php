<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_contract_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sponsor_contract_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_payments');
    }
};
