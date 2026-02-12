<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            // القسم - FK بدل string
            $table->foreignUuid('section_id')->nullable()->after('section')->constrained()->nullOnDelete();

            // نوع المساحة
            $table->enum('space_type', ['booth', 'shop', 'office', 'hall', 'outdoor', 'other'])
                ->nullable()->after('section_id');

            // نظام الدفع
            $table->enum('payment_system', ['full', 'installment', 'daily', 'monthly'])
                ->nullable()->after('space_type');

            // مدة الإيجار
            $table->enum('rental_duration', ['daily', 'weekly', 'monthly', 'full_event'])
                ->nullable()->after('payment_system');

            // الموقع
            $table->decimal('latitude', 10, 8)->nullable()->after('rental_duration');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('address')->nullable()->after('longitude');
            $table->string('address_ar')->nullable()->after('address');

            // حذف عمود section القديم (string)
            $table->dropColumn('section');

            // Indexes
            $table->index('section_id');
            $table->index('space_type');
            $table->index('payment_system');
            $table->index('rental_duration');
        });
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['space_type']);
            $table->dropIndex(['payment_system']);
            $table->dropIndex(['rental_duration']);

            $table->dropColumn([
                'section_id',
                'space_type',
                'payment_system',
                'rental_duration',
                'latitude',
                'longitude',
                'address',
                'address_ar',
            ]);

            // إرجاع عمود section القديم
            $table->string('section')->nullable()->after('floor_number');
        });
    }
};
