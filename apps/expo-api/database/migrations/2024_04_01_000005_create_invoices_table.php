<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique(); // INV-YYYYMMDD-00001
            $table->uuid('user_id'); // صاحب الفاتورة
            $table->string('invoiceable_type'); // نوع المصدر (rental_contract, sponsor_contract)
            $table->uuid('invoiceable_id'); // معرف المصدر
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0); // الضريبة
            $table->decimal('discount_amount', 12, 2)->default(0); // الخصم
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'issued', 'paid', 'partially_paid', 'overdue', 'cancelled', 'refunded'])->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->json('items')->nullable(); // بنود الفاتورة
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['invoiceable_type', 'invoiceable_id']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
