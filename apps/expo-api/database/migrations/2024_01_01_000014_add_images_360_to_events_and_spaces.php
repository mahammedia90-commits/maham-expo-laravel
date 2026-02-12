<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة صور 360 للفعاليات
        Schema::table('events', function (Blueprint $table) {
            $table->json('images_360')->nullable()->after('images');
        });

        // إضافة صور 360 للمساحات
        Schema::table('spaces', function (Blueprint $table) {
            $table->json('images_360')->nullable()->after('images');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('images_360');
        });

        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn('images_360');
        });
    }
};
