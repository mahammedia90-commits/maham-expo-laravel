<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->unsignedInteger('version_number');

            // Content Snapshot
            $table->json('content_snapshot')->nullable();
            $table->longText('content_html')->nullable();
            $table->longText('content_html_ar')->nullable();
            $table->json('terms_snapshot')->nullable();

            // Change Details
            $table->text('change_summary')->nullable();
            $table->text('change_summary_ar')->nullable();
            $table->json('changed_fields')->nullable();
            $table->json('diff_data')->nullable();

            // PDF
            $table->string('pdf_url')->nullable();
            $table->string('pdf_url_ar')->nullable();

            // Audit
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->nullable();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            // Unique Constraint
            $table->unique(['contract_id', 'version_number']);

            // Indexes
            $table->index('contract_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_versions');
    }
};
