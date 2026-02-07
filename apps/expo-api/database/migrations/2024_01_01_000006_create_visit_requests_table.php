<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_number')->unique();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->uuid('user_id'); // من Auth Service
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->integer('visitors_count')->default(1);
            $table->text('notes')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['event_id', 'visit_date']);
            $table->index('status');
            $table->index('request_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_requests');
    }
};
