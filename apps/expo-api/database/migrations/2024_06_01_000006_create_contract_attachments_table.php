<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');

            // File Details
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();

            // Classification
            $table->enum('category', [
                'contract_pdf', 'signed_copy', 'supporting_doc', 'id_copy',
                'commercial_reg', 'vat_cert', 'amendment', 'legal_opinion',
                'correspondence', 'invoice', 'payment_receipt', 'other',
            ])->default('other');

            // Description
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('version_number')->nullable();

            // Audit
            $table->uuid('uploaded_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('contract_id');
            $table->index('category');
            $table->index(['contract_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_attachments');
    }
};
