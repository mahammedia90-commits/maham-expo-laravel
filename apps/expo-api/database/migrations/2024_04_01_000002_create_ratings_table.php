<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // المُقيّم
            $table->string('rateable_type'); // نوع العنصر المُقيّم (space, event, user)
            $table->uuid('rateable_id'); // معرف العنصر المُقيّم
            $table->enum('type', ['space', 'event', 'investor', 'merchant']);
            $table->unsignedTinyInteger('overall_rating'); // 1-5
            $table->unsignedTinyInteger('cleanliness_rating')->nullable(); // النظافة
            $table->unsignedTinyInteger('location_rating')->nullable(); // الموقع
            $table->unsignedTinyInteger('facilities_rating')->nullable(); // المرافق
            $table->unsignedTinyInteger('value_rating')->nullable(); // القيمة مقابل السعر
            $table->unsignedTinyInteger('communication_rating')->nullable(); // التواصل
            $table->text('comment')->nullable();
            $table->text('comment_ar')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->uuid('rental_request_id')->nullable(); // ربط بطلب الإيجار
            $table->timestamps();
            $table->softDeletes();

            $table->index(['rateable_type', 'rateable_id']);
            $table->index('user_id');
            $table->index('type');
            $table->unique(['user_id', 'rateable_type', 'rateable_id']); // تقييم واحد لكل مستخدم لكل عنصر
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
