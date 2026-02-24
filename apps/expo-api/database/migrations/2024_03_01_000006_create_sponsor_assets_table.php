<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['logo', 'banner', 'booth_design', 'video', 'document']);
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->nullable()->comment('File size in bytes');
            $table->string('mime_type')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sponsor_id');
            $table->index('event_id');
            $table->index('type');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_assets');
    }
};
