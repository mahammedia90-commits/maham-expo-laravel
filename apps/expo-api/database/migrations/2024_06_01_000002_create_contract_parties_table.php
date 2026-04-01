<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_parties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');

            // Party Classification
            $table->enum('party_type', ['first_party', 'second_party', 'third_party', 'guarantor', 'witness']);
            $table->enum('party_role', ['owner', 'tenant', 'sponsor', 'investor', 'merchant', 'admin', 'legal_rep']);

            // Identity
            $table->uuid('user_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_name_ar')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_name_ar')->nullable();

            // Contact & Legal
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable();
            $table->string('commercial_reg')->nullable();
            $table->string('vat_number')->nullable();
            $table->text('address')->nullable();
            $table->text('address_ar')->nullable();

            // Signing
            $table->boolean('is_signer')->default(false);
            $table->unsignedInteger('signing_order')->nullable();
            $table->boolean('has_signed')->default(false);
            $table->timestamp('signed_at')->nullable();

            // Signature Details
            $table->text('signature_data')->nullable();
            $table->string('signature_ip')->nullable();
            $table->string('signature_device')->nullable();

            // Signing Token
            $table->string('signing_token')->unique()->nullable();
            $table->timestamp('signing_token_expires_at')->nullable();

            // Meta
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('contract_id');
            $table->index('party_type');
            $table->index('user_id');
            $table->index(['contract_id', 'party_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_parties');
    }
};
