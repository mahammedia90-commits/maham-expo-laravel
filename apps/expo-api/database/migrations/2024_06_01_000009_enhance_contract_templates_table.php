<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contract_templates')) {
            Schema::table('contract_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('contract_templates', 'sub_type')) {
                    $table->string('sub_type')->nullable()->after('type');
                }
                if (!Schema::hasColumn('contract_templates', 'header_html')) {
                    $table->longText('header_html')->nullable();
                }
                if (!Schema::hasColumn('contract_templates', 'footer_html')) {
                    $table->longText('footer_html')->nullable();
                }
                if (!Schema::hasColumn('contract_templates', 'clause_blocks')) {
                    $table->json('clause_blocks')->nullable();
                }
                if (!Schema::hasColumn('contract_templates', 'requires_legal_review')) {
                    $table->boolean('requires_legal_review')->default(false);
                }
                if (!Schema::hasColumn('contract_templates', 'requires_finance_review')) {
                    $table->boolean('requires_finance_review')->default(false);
                }
                if (!Schema::hasColumn('contract_templates', 'default_payment_method')) {
                    $table->enum('default_payment_method', ['full', 'installments', 'milestone'])->nullable();
                }
                if (!Schema::hasColumn('contract_templates', 'default_payment_terms_days')) {
                    $table->unsignedInteger('default_payment_terms_days')->nullable();
                }
                if (!Schema::hasColumn('contract_templates', 'page_size')) {
                    $table->enum('page_size', ['A4', 'letter', 'legal'])->default('A4');
                }
                if (!Schema::hasColumn('contract_templates', 'orientation')) {
                    $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
                }
                if (!Schema::hasColumn('contract_templates', 'logo_position')) {
                    $table->enum('logo_position', ['left', 'center', 'right'])->default('center');
                }
                if (!Schema::hasColumn('contract_templates', 'created_by')) {
                    $table->uuid('created_by')->nullable();
                    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('contract_templates', 'updated_by')) {
                    $table->uuid('updated_by')->nullable();
                    $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
                }
            });
        } else {
            Schema::create('contract_templates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->enum('type', ['lease', 'sponsorship', 'partnership', 'service', 'employment']);
                $table->string('sub_type')->nullable();
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();

                // Content
                $table->longText('content_html')->nullable();
                $table->longText('content_html_ar')->nullable();
                $table->longText('header_html')->nullable();
                $table->longText('footer_html')->nullable();
                $table->json('clause_blocks')->nullable();
                $table->json('variables')->nullable();

                // Review Requirements
                $table->boolean('requires_legal_review')->default(false);
                $table->boolean('requires_finance_review')->default(false);

                // Payment Defaults
                $table->enum('default_payment_method', ['full', 'installments', 'milestone'])->nullable();
                $table->unsignedInteger('default_payment_terms_days')->nullable();

                // Layout
                $table->enum('page_size', ['A4', 'letter', 'legal'])->default('A4');
                $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
                $table->enum('logo_position', ['left', 'center', 'right'])->default('center');

                // Status
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('version')->default(1);

                // Audit
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Foreign Keys
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

                // Indexes
                $table->index('type');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contract_templates') && !Schema::hasColumn('contract_templates', 'name')) {
            // Table was created fresh by this migration
            Schema::dropIfExists('contract_templates');
        } else {
            // Table was altered - remove added columns
            Schema::table('contract_templates', function (Blueprint $table) {
                $columns = [
                    'sub_type', 'header_html', 'footer_html', 'clause_blocks',
                    'requires_legal_review', 'requires_finance_review',
                    'default_payment_method', 'default_payment_terms_days',
                    'page_size', 'orientation', 'logo_position',
                    'created_by', 'updated_by',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('contract_templates', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
