<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sponsor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('contact_name');
            $table->string('contact_name_ar')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('industry')->nullable();
            $table->string('industry_ar')->nullable();
            $table->enum('interest_level', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost'])->default('new');
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->string('source')->nullable()->comment('Where the lead came from');
            $table->date('captured_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sponsor_id');
            $table->index('event_id');
            $table->index('interest_level');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_leads');
    }
};
