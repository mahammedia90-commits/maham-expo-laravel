<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_activities')) {
            Schema::create('user_activities', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable()->index();
                $table->string('action', 50)->index();         // view, search, click, share, filter, download, etc.
                $table->string('resource_type')->nullable();    // App\Models\Event, App\Models\Space, etc.
                $table->uuid('resource_id')->nullable();
                $table->enum('platform', ['web', 'mobile', 'api'])->default('web')->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->string('referrer')->nullable();
                $table->json('metadata')->nullable();           // extra context: search_query, filter_params, etc.
                $table->string('session_id', 100)->nullable()->index();
                $table->timestamps();

                $table->index(['resource_type', 'resource_id']);
                $table->index(['action', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
