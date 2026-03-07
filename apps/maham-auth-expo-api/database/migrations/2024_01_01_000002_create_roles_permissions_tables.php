<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الصلاحيات
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // مثل: users.create, orders.view
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('group')->nullable(); // لتجميع الصلاحيات: users, orders, etc
            $table->boolean('is_system')->default(false); // صلاحيات النظام لا يمكن حذفها
            $table->timestamps();

            $table->index('group');
            $table->index('name');
        });

        // جدول الأدوار
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(false); // أدوار النظام لا يمكن حذفها
            $table->integer('level')->default(0); // مستوى الدور (للترتيب الهرمي)
            $table->timestamps();

            $table->index('name');
            $table->index('level');
        });

        // جدول ربط الأدوار بالصلاحيات
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->timestamps();

            $table->primary(['role_id', 'permission_id']);
            
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
        });

        // جدول ربط المستخدمين بالأدوار
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->uuid('assigned_by')->nullable(); // من قام بالإسناد
            $table->timestamp('expires_at')->nullable(); // للأدوار المؤقتة
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });

        // جدول الصلاحيات المباشرة للمستخدم (بدون دور)
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('permission_id');
            $table->boolean('is_granted')->default(true); // true = منح، false = سحب
            $table->uuid('assigned_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->primary(['user_id', 'permission_id']);
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
