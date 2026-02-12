<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            // User 10 notifications (TechVentures)
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'title' => 'Profile Approved',
                'title_ar' => 'تم اعتماد الملف التجاري',
                'body' => 'Your business profile has been approved. You can now submit rental requests.',
                'body_ar' => 'تم اعتماد ملفك التجاري. يمكنك الآن تقديم طلبات استئجار.',
                'type' => 'profile_approved',
                'data' => ['profile_status' => 'approved'],
                'read_at' => now()->subDays(8),
            ],
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'title' => 'Rental Request Approved',
                'title_ar' => 'تم الموافقة على طلب الاستئجار',
                'body' => 'Your rental request for Saudi Tech Expo 2025 has been approved.',
                'body_ar' => 'تمت الموافقة على طلب استئجارك لمعرض السعودية التقني 2025.',
                'type' => 'rental_approved',
                'data' => ['request_type' => 'rental', 'status' => 'approved'],
                'read_at' => now()->subDays(4),
            ],
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'title' => 'New Event Published',
                'title_ar' => 'فعالية جديدة',
                'body' => 'Saudi Auto Show 2025 has been published. Check it out!',
                'body_ar' => 'تم نشر معرض السيارات السعودي 2025. اطلع عليه!',
                'type' => 'event_published',
                'data' => ['event_name' => 'Saudi Auto Show 2025'],
                'read_at' => null, // unread
            ],

            // User 11 notifications (Al-Salam Trading)
            [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'title' => 'Visit Request Approved',
                'title_ar' => 'تم الموافقة على طلب الزيارة',
                'body' => 'Your visit request has been approved. Please arrive on time.',
                'body_ar' => 'تمت الموافقة على طلب زيارتك. يرجى الحضور في الموعد.',
                'type' => 'visit_approved',
                'data' => ['request_type' => 'visit', 'status' => 'approved'],
                'read_at' => now()->subDays(1),
            ],
            [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'title' => 'Payment Reminder',
                'title_ar' => 'تذكير بالدفع',
                'body' => 'Please complete your rental payment before the deadline.',
                'body_ar' => 'يرجى إتمام دفع الاستئجار قبل الموعد النهائي.',
                'type' => 'payment_reminder',
                'data' => ['payment_status' => 'pending'],
                'read_at' => null, // unread
            ],

            // User 12 notifications (Fresh Foods - pending)
            [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'title' => 'Profile Under Review',
                'title_ar' => 'الملف التجاري قيد المراجعة',
                'body' => 'Your business profile is being reviewed. We will notify you once it is processed.',
                'body_ar' => 'ملفك التجاري قيد المراجعة. سنقوم بإخطارك بمجرد المعالجة.',
                'type' => 'profile_pending',
                'data' => ['profile_status' => 'pending'],
                'read_at' => null, // unread
            ],
            [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'title' => 'Welcome to Maham Expo',
                'title_ar' => 'مرحباً بك في مهام إكسبو',
                'body' => 'Welcome! Complete your business profile to start renting exhibition spaces.',
                'body_ar' => 'مرحباً! أكمل ملفك التجاري لبدء استئجار مساحات المعارض.',
                'type' => 'welcome',
                'data' => null,
                'read_at' => now()->subDays(5),
            ],

            // User 13 notifications (Quick Invest - rejected)
            [
                'user_id' => '00000000-0000-0000-0000-000000000013',
                'title' => 'Profile Rejected',
                'title_ar' => 'تم رفض الملف التجاري',
                'body' => 'Your business profile has been rejected. Please update your documents and resubmit.',
                'body_ar' => 'تم رفض ملفك التجاري. يرجى تحديث مستنداتك وإعادة التقديم.',
                'type' => 'profile_rejected',
                'data' => ['profile_status' => 'rejected', 'reason' => 'Expired registration'],
                'read_at' => null, // unread
            ],

            // User 14 notifications (Beauty World)
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'title' => 'Rental Request Approved',
                'title_ar' => 'تم الموافقة على طلب الاستئجار',
                'body' => 'Your rental request for Glamour Fashion Week has been approved. Please complete the payment.',
                'body_ar' => 'تمت الموافقة على طلب استئجارك لأسبوع الموضة جلامور. يرجى إتمام الدفع.',
                'type' => 'rental_approved',
                'data' => ['request_type' => 'rental', 'status' => 'approved'],
                'read_at' => now()->subDays(2),
            ],
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'title' => 'Rental Request Rejected',
                'title_ar' => 'تم رفض طلب الاستئجار',
                'body' => 'Your rental request for Saudi Real Estate Show has been rejected.',
                'body_ar' => 'تم رفض طلب استئجارك لمعرض العقارات السعودي.',
                'type' => 'rental_rejected',
                'data' => ['request_type' => 'rental', 'status' => 'rejected'],
                'read_at' => null, // unread
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Created ' . count($notifications) . ' notifications.');
    }
}
