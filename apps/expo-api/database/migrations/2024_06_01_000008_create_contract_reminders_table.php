<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_reminders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');

            // Reminder Type
            $table->enum('type', [
                'signature_pending', 'payment_due', 'payment_overdue',
                'expiry_warning', 'renewal_reminder', 'review_pending', 'custom',
            ]);

            // Schedule
            $table->timestamp('remind_at');
            $table->timestamp('reminded_at')->nullable();
            $table->boolean('is_sent')->default(false);

            // Target
            $table->uuid('target_user_id')->nullable();
            $table->string('target_role')->nullable();

            // Content
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('message')->nullable();
            $table->text('message_ar')->nullable();

            // Recurrence
            $table->boolean('is_recurring')->default(false);
            $table->unsignedInteger('recurrence_days')->nullable();
            $table->unsignedInteger('max_reminders')->default(3);
            $table->unsignedInteger('reminder_count')->default(0);

            // Audit
            $table->timestamp('created_at')->nullable();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('contract_id');
            $table->index('type');
            $table->index('remind_at');
            $table->index('is_sent');
            $table->index(['is_sent', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_reminders');
    }
};
