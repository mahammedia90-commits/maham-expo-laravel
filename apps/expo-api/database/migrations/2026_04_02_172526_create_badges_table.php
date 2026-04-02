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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en');
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum', 'diamond'])->default('bronze');
            $table->string('criteria')->nullable();
            $table->boolean('auto_grant')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
