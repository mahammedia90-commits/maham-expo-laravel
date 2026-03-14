<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            // ── Step 1: Personal Data ──
            $table->string('full_name')->nullable()->after('user_id');
            $table->date('date_of_birth')->nullable()->after('national_id_image');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('city')->nullable()->after('nationality');

            // ── Step 2: Company Data ──
            $table->string('business_activity_type')->nullable()->after('website');
            $table->year('establishment_year')->nullable()->after('business_activity_type');
            $table->string('vat_number', 15)->nullable()->after('establishment_year');
            $table->string('national_address')->nullable()->after('vat_number');
            $table->string('employee_count')->nullable()->after('national_address');

            // ── Step 3: Bank Account ──
            $table->string('bank_name')->nullable()->after('employee_count');
            $table->string('iban', 34)->nullable()->after('bank_name');
            $table->string('account_holder_name')->nullable()->after('iban');
            $table->string('account_number')->nullable()->after('account_holder_name');

            // ── Step 4: Document Uploads ──
            $table->string('vat_certificate_image')->nullable()->after('account_number');
            $table->string('authorization_letter_image')->nullable()->after('vat_certificate_image');
            $table->string('national_address_doc')->nullable()->after('authorization_letter_image');
            $table->string('bank_letter_image')->nullable()->after('national_address_doc');
            $table->string('company_profile_doc')->nullable()->after('bank_letter_image');
            $table->string('product_catalog_doc')->nullable()->after('company_profile_doc');

            // ── Step 5: Legal Declaration ──
            $table->boolean('legal_declaration_accepted')->default(false)->after('product_catalog_doc');
            $table->timestamp('legal_declaration_accepted_at')->nullable()->after('legal_declaration_accepted');

            // ── KYC Tracking ──
            $table->unsignedTinyInteger('kyc_step')->default(1)->after('legal_declaration_accepted_at');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_step');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'date_of_birth', 'nationality', 'city',
                'business_activity_type', 'establishment_year', 'vat_number',
                'national_address', 'employee_count',
                'bank_name', 'iban', 'account_holder_name', 'account_number',
                'vat_certificate_image', 'authorization_letter_image',
                'national_address_doc', 'bank_letter_image',
                'company_profile_doc', 'product_catalog_doc',
                'legal_declaration_accepted', 'legal_declaration_accepted_at',
                'kyc_step', 'kyc_submitted_at',
            ]);
        });
    }
};
