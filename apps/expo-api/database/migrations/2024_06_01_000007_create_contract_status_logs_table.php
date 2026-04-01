<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');

            // Action Details
            $table->string('action', 50);
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();

            // Change Data
            $table->json('changed_fields')->nullable();
            $table->json('metadata')->nullable();

            // Performer
            $table->uuid('performed_by')->nullable();
            $table->string('performed_by_name')->nullable();
            $table->string('performed_by_role')->nullable();

            // Request Info
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Audit
            $table->timestamp('created_at')->nullable();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('contract_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['contract_id', 'action']);
            $table->index(['contract_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_status_logs');
    }
};
