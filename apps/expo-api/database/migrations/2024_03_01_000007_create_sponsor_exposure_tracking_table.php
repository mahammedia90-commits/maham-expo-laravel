<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_exposure_tracking', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('sponsor_contract_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('channel', ['app', 'website', 'screen', 'ticket', 'email', 'push_notification', 'social_media']);
            $table->integer('impressions_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->date('date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('sponsor_id');
            $table->index('event_id');
            $table->index('channel');
            $table->index('date');
            $table->unique(['sponsor_id', 'event_id', 'channel', 'date'], 'sponsor_exposure_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_exposure_tracking');
    }
};
