<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_deliverables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_contract_id')->constrained()->cascadeOnDelete();
            $table->string('category')->comment('brand_assets, digital_media, stage, physical, hospitality, media_coverage');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('specs')->nullable()->comment('Technical specs required');
            $table->enum('status', ['not_started', 'pending', 'in_progress', 'review', 'completed', 'rejected'])->default('not_started');
            $table->date('deadline')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sponsor_contract_id');
            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_deliverables');
    }
};
