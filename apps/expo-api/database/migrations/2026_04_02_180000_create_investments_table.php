<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('sector')->nullable();
            $table->decimal('investment_amount', 15, 2)->default(0);
            $table->decimal('portfolio_value', 15, 2)->default(0);
            $table->decimal('roi_percentage', 5, 2)->default(0);
            $table->enum('status', ['active', 'pending', 'inactive'])->default('active');
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
