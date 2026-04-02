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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company')->nullable();
            $table->string('phone', 20);
            $table->string('phone_whatsapp', 20)->nullable();
            $table->string('email')->unique();
            $table->string('city')->nullable();
            $table->string('sector')->nullable();
            $table->enum('lead_type', ['investor', 'sponsor', 'merchant', 'partner'])->default('investor');
            $table->string('source')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('ai_score')->default(50);
            $table->enum('status', ['active', 'inactive', 'qualified', 'converted', 'lost'])->default('active');
            $table->text('next_action')->nullable();
            $table->date('next_action_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
