<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('contract_number')->unique();
            $table->uuid('parent_contract_id')->nullable();

            // Type & Classification
            $table->enum('type', ['lease', 'sponsorship', 'partnership', 'service', 'employment']);
            $table->string('sub_type')->nullable();
            $table->enum('category', ['new', 'renewal', 'amendment', 'addendum'])->default('new');

            // Template
            $table->uuid('template_id')->nullable();
            $table->unsignedInteger('template_version')->nullable();

            // Titles & Descriptions
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();

            // Content
            $table->longText('content_html')->nullable();
            $table->longText('content_html_ar')->nullable();
            $table->json('terms_and_conditions')->nullable();
            $table->json('special_conditions')->nullable();

            // Relations
            $table->uuid('event_id')->nullable();
            $table->uuid('space_id')->nullable();
            $table->uuid('section_id')->nullable();
            $table->uuid('sponsor_package_id')->nullable();

            // Financial
            $table->string('currency', 3)->default('SAR');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('taxable_amount', 14, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(15.00);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('penalty_amount', 14, 2)->default(0);

            // Payment
            $table->enum('payment_method', ['full', 'installments', 'milestone'])->default('full');
            $table->unsignedInteger('installments_count')->nullable();
            $table->unsignedInteger('payment_terms_days')->default(30);

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('signing_deadline')->nullable();

            // Status
            $table->enum('status', [
                'draft', 'under_review', 'approved', 'sent_for_signature', 'signed',
                'active', 'suspended', 'completed', 'cancelled', 'rejected', 'terminated',
            ])->default('draft');
            $table->enum('payment_status', [
                'not_due', 'pending', 'partial', 'paid', 'overdue', 'refunded',
            ])->default('not_due');

            // Legal Approval
            $table->boolean('legal_approved')->default(false);
            $table->uuid('legal_approved_by')->nullable();
            $table->timestamp('legal_approved_at')->nullable();
            $table->text('legal_notes')->nullable();

            // Finance Approval
            $table->boolean('finance_approved')->default(false);
            $table->uuid('finance_approved_by')->nullable();
            $table->timestamp('finance_approved_at')->nullable();
            $table->text('finance_notes')->nullable();

            // Final Approval
            $table->uuid('final_approved_by')->nullable();
            $table->timestamp('final_approved_at')->nullable();

            // Signing
            $table->boolean('is_fully_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->enum('signature_method', ['drawn', 'typed', 'uploaded', 'docusign', 'otp_verified'])->nullable();

            // Rejection
            $table->text('rejection_reason')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->uuid('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Suspension
            $table->text('suspension_reason')->nullable();
            $table->uuid('suspended_by')->nullable();
            $table->timestamp('suspended_at')->nullable();

            // Termination
            $table->text('termination_reason')->nullable();
            $table->uuid('terminated_by')->nullable();
            $table->timestamp('terminated_at')->nullable();

            // Renewal
            $table->boolean('is_renewable')->default(false);
            $table->unsignedInteger('renewal_reminder_days')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->uuid('renewed_contract_id')->nullable();

            // AI Analysis
            $table->decimal('ai_risk_score', 5, 2)->nullable();
            $table->json('ai_risk_analysis')->nullable();
            $table->timestamp('ai_analyzed_at')->nullable();

            // Meta
            $table->json('metadata')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Audit
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('parent_contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
            $table->foreign('space_id')->references('id')->on('spaces')->nullOnDelete();
            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('type');
            $table->index('category');
            $table->index('status');
            $table->index('payment_status');
            $table->index('event_id');
            $table->index('space_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('signing_deadline');
            $table->index('created_by');
            $table->index('created_at');
            $table->index(['type', 'status']);
            $table->index(['event_id', 'status']);
            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
