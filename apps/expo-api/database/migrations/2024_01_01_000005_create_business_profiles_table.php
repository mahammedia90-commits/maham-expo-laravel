<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique(); // من Auth Service
            $table->string('company_name');
            $table->string('company_name_ar')->nullable();
            $table->string('commercial_registration_number')->nullable();
            $table->string('commercial_registration_image')->nullable();
            $table->string('national_id_number')->nullable();
            $table->string('national_id_image')->nullable();
            $table->string('company_logo')->nullable();
            $table->text('company_address')->nullable();
            $table->text('company_address_ar')->nullable();
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->enum('business_type', ['investor', 'merchant'])->default('merchant');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
