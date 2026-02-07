<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الخدمات المتصلة
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('token')->unique(); // Service Token للمصادقة
            $table->string('secret')->nullable(); // للتوقيع
            $table->json('allowed_ips')->nullable(); // IPs المسموح بها
            $table->json('allowed_permissions')->nullable(); // الصلاحيات المسموح بالتحقق منها
            $table->string('webhook_url')->nullable(); // للإشعارات
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('token');
            $table->index('status');
        });

        // جدول سجل استخدام الخدمات
        Schema::create('service_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->string('action'); // verify_token, get_user, check_permission
            $table->uuid('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('response_time')->nullable(); // بالميلي ثانية
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');

            $table->index(['service_id', 'created_at']);
            $table->index('action');
        });

        // جدول سجل تدقيق المستخدمين
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('target_user_id')->nullable(); // المستخدم المتأثر
            $table->string('action'); // login, logout, password_change, etc
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('created_at');
        });

        // جدول الـ Refresh Tokens
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('token')->unique();
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable(); // web, mobile, api
            $table->string('ip_address')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id', 'revoked']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('service_logs');
        Schema::dropIfExists('services');
    }
};
