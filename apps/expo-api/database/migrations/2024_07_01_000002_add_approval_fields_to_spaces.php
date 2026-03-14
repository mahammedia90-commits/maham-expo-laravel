<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            // ── Approval Workflow ──
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->after('status');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->foreignUuid('approved_by')->nullable()->after('rejection_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropIndex(['approval_status']);
            $table->dropColumn([
                'approval_status',
                'rejection_reason',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};
