<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('city_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->string('address_ar')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->json('images')->nullable();
            $table->json('features')->nullable();
            $table->json('features_ar')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('organizer_phone')->nullable();
            $table->string('organizer_email')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['draft', 'published', 'ended', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date', 'end_date']);
            $table->index('is_featured');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
