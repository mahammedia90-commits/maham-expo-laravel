<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained('deals')->onDelete('cascade');
            $table->date('due_date');
            $table->enum('type', ['call', 'email', 'meeting', 'proposal', 'follow_up'])->default('call');
            $table->enum('status', ['pending', 'completed', 'cancelled', 'rescheduled'])->default('pending');
            $table->text('outcome')->nullable();
            $table->integer('duration')->nullable();
            $table->text('next_action')->nullable();
            $table->date('next_action_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
