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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->integer('leads_assigned')->default(0);
            $table->integer('leads_contacted')->default(0);
            $table->integer('followups_completed')->default(0);
            $table->integer('meetings_held')->default(0);
            $table->integer('proposals_sent')->default(0);
            $table->integer('deals_closed')->default(0);
            $table->decimal('revenue_generated', 15, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('avg_deal_value', 15, 2)->default(0);
            $table->decimal('response_time_hours', 8, 2)->default(0);
            $table->integer('daily_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
