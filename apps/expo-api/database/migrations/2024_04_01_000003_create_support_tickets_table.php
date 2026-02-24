<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique(); // TK-YYYYMMDD-00001
            $table->uuid('user_id');
            $table->string('subject');
            $table->string('subject_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->enum('category', ['general', 'technical', 'billing', 'space', 'event', 'contract', 'complaint', 'suggestion'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_reply', 'resolved', 'closed'])->default('open');
            $table->uuid('assigned_to')->nullable(); // المسؤول
            $table->uuid('related_id')->nullable(); // معرف العنصر المرتبط (event, space, contract)
            $table->string('related_type')->nullable(); // نوع العنصر المرتبط
            $table->json('attachments')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('assigned_to');
            $table->index(['status', 'priority']);
            $table->index('category');
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->uuid('user_id');
            $table->text('message');
            $table->text('message_ar')->nullable();
            $table->boolean('is_staff_reply')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};
