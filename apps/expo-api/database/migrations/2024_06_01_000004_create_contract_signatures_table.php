<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->uuid('party_id');
            $table->unsignedInteger('version_number')->nullable();

            // Signature
            $table->enum('signature_type', ['drawn', 'typed', 'uploaded', 'docusign', 'otp_verified']);
            $table->text('signature_data')->nullable();
            $table->string('signature_hash', 64)->nullable();

            // Verification
            $table->enum('verification_method', ['otp', 'email', 'biometric', 'in_person'])->nullable();
            $table->string('verification_code')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Device & Location
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->json('geo_location')->nullable();

            // Legal
            $table->timestamp('legal_timestamp')->nullable();

            // Documents
            $table->string('signed_document_url')->nullable();
            $table->string('certificate_url')->nullable();

            // Audit
            $table->timestamp('created_at')->nullable();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('party_id')->references('id')->on('contract_parties')->cascadeOnDelete();

            // Indexes
            $table->index('contract_id');
            $table->index('party_id');
            $table->index(['contract_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_signatures');
    }
};
