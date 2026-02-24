<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('user_id')->nullable()->comment('Sponsor user ID from Auth Service');
            $table->string('name');
            $table->string('name_ar');
            $table->string('company_name')->nullable();
            $table->string('company_name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('logo')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['pending', 'approved', 'active', 'suspended', 'inactive'])->default('pending');
            $table->string('created_by')->nullable();
            $table->string('created_from')->nullable()->comment('web, mobile, api');
            $table->timestamps();
            $table->softDeletes();

            $table->index('event_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
