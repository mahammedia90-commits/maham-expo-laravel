<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('location_code'); // مثل B1A1
            $table->decimal('area_sqm', 10, 2); // المساحة بالمتر المربع
            $table->decimal('price_per_day', 12, 2)->nullable();
            $table->decimal('price_total', 12, 2); // السعر الإجمالي
            $table->json('images')->nullable();
            $table->json('amenities')->nullable(); // المرافق
            $table->json('amenities_ar')->nullable();
            $table->enum('status', ['available', 'reserved', 'rented', 'unavailable'])->default('available');
            $table->integer('floor_number')->nullable();
            $table->string('section')->nullable(); // القسم
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'status']);
            $table->unique(['event_id', 'location_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
