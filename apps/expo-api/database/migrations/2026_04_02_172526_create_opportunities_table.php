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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['investment', 'sponsorship', 'merchant', 'partnership']);
            $table->enum('status', ['open', 'in_review', 'closed'])->default('open');
            $table->decimal('value', 15, 2)->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
