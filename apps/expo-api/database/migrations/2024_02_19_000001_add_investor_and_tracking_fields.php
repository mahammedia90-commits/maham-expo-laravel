<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add investor_id to spaces
        Schema::table('spaces', function (Blueprint $table) {
            if (!Schema::hasColumn('spaces', 'investor_id')) {
                $table->uuid('investor_id')->nullable()->after('event_id');
                $table->index('investor_id');
            }
            if (!Schema::hasColumn('spaces', 'created_from')) {
                $table->enum('created_from', ['web', 'mobile', 'api'])->default('web')->after('status');
                $table->index('created_from');
            }
            if (!Schema::hasColumn('spaces', 'created_by')) {
                $table->uuid('created_by')->nullable()->after('created_from');
            }
        });

        // Add tracking fields to events (created_by and views_count already exist in create_events migration)
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'created_from')) {
                $table->enum('created_from', ['web', 'mobile', 'api'])->default('web')->after('status');
                $table->index('created_from');
            }
        });

        // Add tracking fields to sections
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'created_from')) {
                $table->enum('created_from', ['web', 'mobile', 'api'])->default('web')->after('event_id');
                $table->index('created_from');
            }
            if (!Schema::hasColumn('sections', 'created_by')) {
                $table->uuid('created_by')->nullable()->after('created_from');
            }
        });

        // Add investor approval fields to rental_requests
        Schema::table('rental_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_requests', 'investor_status')) {
                $table->enum('investor_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
                $table->index('investor_status');
            }
            if (!Schema::hasColumn('rental_requests', 'investor_reviewed_by')) {
                $table->uuid('investor_reviewed_by')->nullable()->after('investor_status');
            }
            if (!Schema::hasColumn('rental_requests', 'investor_reviewed_at')) {
                $table->timestamp('investor_reviewed_at')->nullable()->after('investor_reviewed_by');
            }
            if (!Schema::hasColumn('rental_requests', 'investor_notes')) {
                $table->text('investor_notes')->nullable()->after('investor_reviewed_at');
            }
        });

        // Add investor approval fields to visit_requests
        Schema::table('visit_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('visit_requests', 'investor_status')) {
                $table->enum('investor_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
                $table->index('investor_status');
            }
            if (!Schema::hasColumn('visit_requests', 'investor_reviewed_by')) {
                $table->uuid('investor_reviewed_by')->nullable()->after('investor_status');
            }
            if (!Schema::hasColumn('visit_requests', 'investor_reviewed_at')) {
                $table->timestamp('investor_reviewed_at')->nullable()->after('investor_reviewed_by');
            }
            if (!Schema::hasColumn('visit_requests', 'investor_notes')) {
                $table->text('investor_notes')->nullable()->after('investor_reviewed_at');
            }
        });

        // Create analytics tracking table
        if (!Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable();
                $table->string('viewable_type'); // Event, Space, Section
                $table->uuid('viewable_id');
                $table->enum('platform', ['web', 'mobile', 'api'])->default('web');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('referrer')->nullable();
                $table->timestamps();

                $table->index(['viewable_type', 'viewable_id']);
                $table->index('user_id');
                $table->index('platform');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            if (Schema::hasColumn('spaces', 'investor_id')) {
                $table->dropIndex(['investor_id']);
                $table->dropColumn('investor_id');
            }
            if (Schema::hasColumn('spaces', 'created_from')) {
                $table->dropIndex(['created_from']);
                $table->dropColumn('created_from');
            }
            if (Schema::hasColumn('spaces', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });

        // Only drop created_from (created_by and views_count belong to create_events migration)
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'created_from')) {
                $table->dropIndex(['created_from']);
                $table->dropColumn('created_from');
            }
        });

        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'created_from')) {
                $table->dropIndex(['created_from']);
                $table->dropColumn('created_from');
            }
            if (Schema::hasColumn('sections', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            if (Schema::hasColumn('rental_requests', 'investor_status')) {
                $table->dropIndex(['investor_status']);
                $table->dropColumn(['investor_status', 'investor_reviewed_by', 'investor_reviewed_at', 'investor_notes']);
            }
        });

        Schema::table('visit_requests', function (Blueprint $table) {
            if (Schema::hasColumn('visit_requests', 'investor_status')) {
                $table->dropIndex(['investor_status']);
                $table->dropColumn(['investor_status', 'investor_reviewed_by', 'investor_reviewed_at', 'investor_notes']);
            }
        });

        Schema::dropIfExists('page_views');
    }
};
