<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('name_ar');
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable();
            $table->string('scope'); // 'merchant', 'investor', 'both'
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('scope');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_types');
    }
};
