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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('stage_id')->constrained('pipeline_stages')->onDelete('cascade');
            $table->decimal('value', 15, 2);
            $table->integer('close_probability')->default(50);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('days_in_stage')->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->boolean('is_overdue')->default(false);
            $table->boolean('is_at_risk')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
