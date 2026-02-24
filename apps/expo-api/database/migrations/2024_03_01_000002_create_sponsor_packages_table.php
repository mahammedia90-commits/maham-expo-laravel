<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('tier', ['platinum', 'gold', 'silver', 'bronze', 'media_partner', 'strategic_partner']);
            $table->decimal('price', 12, 2);
            $table->integer('max_sponsors')->nullable()->comment('Maximum sponsors for this tier, null = unlimited');
            $table->json('benefits')->nullable()->comment('Array of benefit descriptions');
            $table->integer('display_screens_count')->default(0);
            $table->integer('banners_count')->default(0);
            $table->integer('vip_invitations_count')->default(0);
            $table->decimal('booth_area_sqm', 8, 2)->nullable();
            $table->json('logo_placement')->nullable()->comment('Logo placement locations');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('event_id');
            $table->index('tier');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_packages');
    }
};
