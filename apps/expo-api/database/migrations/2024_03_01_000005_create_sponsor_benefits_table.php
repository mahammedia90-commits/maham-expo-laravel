<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_benefits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_contract_id')->constrained()->cascadeOnDelete();
            $table->enum('benefit_type', ['screen', 'banner', 'booth', 'vip_invitation', 'logo', 'notification', 'email', 'custom']);
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('delivered_quantity')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'delivered', 'cancelled'])->default('pending');
            $table->text('delivery_notes')->nullable();
            $table->timestamps();

            $table->index('sponsor_contract_id');
            $table->index('benefit_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_benefits');
    }
};
