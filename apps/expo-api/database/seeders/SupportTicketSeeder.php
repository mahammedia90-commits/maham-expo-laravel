<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        if (SupportTicket::count() > 0) {
            $this->command->info('Support tickets already seeded, skipping.');
            return;
        }

        $staffId = '00000000-0000-0000-0000-000000000099';
        $supervisorId = '00000000-0000-0000-0000-000000000098';

        $tickets = [
            // ===== Ticket 1: Resolved billing issue =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'subject' => 'Payment not reflected in my account',
                'subject_ar' => 'الدفع لم ينعكس في حسابي',
                'description' => 'I made a bank transfer payment for my rental request RR-20250220-00001 two days ago but the payment status still shows as pending. Transaction reference: TXN-20250218-001.',
                'description_ar' => 'قمت بتحويل بنكي لطلب الاستئجار RR-20250220-00001 منذ يومين لكن حالة الدفع لا تزال معلقة. رقم العملية: TXN-20250218-001.',
                'category' => 'billing',
                'priority' => 'high',
                'status' => 'resolved',
                'assigned_to' => $staffId,
                'resolved_at' => now()->subDays(1),
                'resolved_by' => $staffId,
                'replies' => [
                    [
                        'user_id' => $staffId,
                        'message' => 'Thank you for reaching out. I can see your transfer was received but not yet processed in our system. I have manually updated your payment status. Please check your dashboard now.',
                        'message_ar' => 'شكراً لتواصلك. يمكنني رؤية أن التحويل تم استلامه لكن لم تتم معالجته في نظامنا بعد. قمت بتحديث حالة الدفع يدوياً. يرجى التحقق من لوحة التحكم الآن.',
                        'is_staff_reply' => true,
                    ],
                    [
                        'user_id' => '00000000-0000-0000-0000-000000000010',
                        'message' => 'I can see the payment is now showing as paid. Thank you for the quick resolution!',
                        'message_ar' => 'يمكنني رؤية أن الدفع يظهر الآن كمدفوع. شكراً للحل السريع!',
                        'is_staff_reply' => false,
                    ],
                ],
            ],

            // ===== Ticket 2: Open technical issue =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'subject' => 'Cannot upload commercial registration document',
                'subject_ar' => 'لا أستطيع رفع وثيقة السجل التجاري',
                'description' => 'When I try to upload my commercial registration image in the business profile section, I get an error "File too large". The file is only 3MB and the limit says 10MB.',
                'description_ar' => 'عندما أحاول رفع صورة السجل التجاري في قسم الملف التجاري، أحصل على خطأ "الملف كبير جداً". حجم الملف 3 ميجابايت فقط والحد المسموح 10 ميجابايت.',
                'category' => 'technical',
                'priority' => 'medium',
                'status' => 'in_progress',
                'assigned_to' => $supervisorId,
                'replies' => [
                    [
                        'user_id' => $supervisorId,
                        'message' => 'We are investigating this issue. Could you please tell us what browser and device you are using? Also, what is the format of the file (PDF, JPG, PNG)?',
                        'message_ar' => 'نحن نحقق في هذه المشكلة. هل يمكنك إخبارنا بالمتصفح والجهاز الذي تستخدمه؟ وأيضاً ما هو تنسيق الملف (PDF, JPG, PNG)؟',
                        'is_staff_reply' => true,
                    ],
                    [
                        'user_id' => '00000000-0000-0000-0000-000000000011',
                        'message' => 'I am using Chrome on iPhone 15. The file is a PDF document.',
                        'message_ar' => 'أستخدم كروم على آيفون 15. الملف وثيقة PDF.',
                        'is_staff_reply' => false,
                    ],
                    [
                        'user_id' => $supervisorId,
                        'message' => 'Thank you for the information. We have identified a bug with PDF uploads on mobile Safari/Chrome. Our development team is working on a fix. In the meantime, could you try converting the PDF to JPG and uploading that instead?',
                        'message_ar' => 'شكراً للمعلومات. اكتشفنا خطأ في رفع ملفات PDF على Safari/Chrome للهاتف. فريق التطوير يعمل على إصلاحه. في هذه الأثناء، هل يمكنك تحويل PDF إلى JPG ورفعها بدلاً من ذلك؟',
                        'is_staff_reply' => true,
                    ],
                ],
            ],

            // ===== Ticket 3: Open space inquiry =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'subject' => 'Can I get a bigger space for the food festival?',
                'subject_ar' => 'هل يمكنني الحصول على مساحة أكبر لمهرجان الطعام؟',
                'description' => 'I submitted a rental request for Food Booth SC-01 but I realized I need a larger space. Is it possible to switch to a bigger booth or combine two booths? My business requires at least 25sqm.',
                'description_ar' => 'قدمت طلب استئجار لكشك الطعام SC-01 لكنني أدركت أنني بحاجة لمساحة أكبر. هل يمكن التبديل لكشك أكبر أو دمج كشكين؟ عملي يحتاج 25 متر مربع على الأقل.',
                'category' => 'space',
                'priority' => 'low',
                'status' => 'open',
                'replies' => [],
            ],

            // ===== Ticket 4: Closed complaint =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'subject' => 'Slow response time for rental approval',
                'subject_ar' => 'بطء وقت الاستجابة للموافقة على الاستئجار',
                'description' => 'I submitted my rental request 5 days ago and it is still pending. The event starts in 2 weeks and I need to prepare my booth. Please expedite the approval process.',
                'description_ar' => 'قدمت طلب الاستئجار منذ 5 أيام ولا يزال معلقاً. الفعالية تبدأ خلال أسبوعين وأحتاج لتجهيز كشكي. يرجى تسريع عملية الموافقة.',
                'category' => 'complaint',
                'priority' => 'high',
                'status' => 'closed',
                'assigned_to' => $staffId,
                'resolved_at' => now()->subDays(3),
                'resolved_by' => $staffId,
                'closed_at' => now()->subDays(2),
                'replies' => [
                    [
                        'user_id' => $staffId,
                        'message' => 'We apologize for the delay. Your rental request has been prioritized and is now approved. You should see the updated status in your dashboard. We are working on improving our response times.',
                        'message_ar' => 'نعتذر عن التأخير. تم تحديد أولوية طلب الاستئجار وتمت الموافقة عليه الآن. يجب أن ترى الحالة المحدثة في لوحة التحكم. نعمل على تحسين أوقات الاستجابة.',
                        'is_staff_reply' => true,
                    ],
                    [
                        'user_id' => '00000000-0000-0000-0000-000000000014',
                        'message' => 'Thank you for approving it. I appreciate the quick follow-up on this ticket.',
                        'message_ar' => 'شكراً للموافقة. أقدر المتابعة السريعة على هذا التذكرة.',
                        'is_staff_reply' => false,
                    ],
                ],
            ],

            // ===== Ticket 5: Waiting reply - event question =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'subject' => 'Question about event setup and teardown hours',
                'subject_ar' => 'سؤال عن ساعات التجهيز والإزالة',
                'description' => 'I rented a booth at Saudi Tech Expo. What are the designated setup and teardown hours? Can I access the venue one day before the event starts to set up my display?',
                'description_ar' => 'استأجرت كشكاً في معرض السعودية التقني. ما هي ساعات التجهيز والإزالة المحددة؟ هل يمكنني الوصول للمكان قبل يوم من بدء الفعالية لتجهيز عرضي؟',
                'category' => 'event',
                'priority' => 'medium',
                'status' => 'waiting_reply',
                'assigned_to' => $staffId,
                'replies' => [
                    [
                        'user_id' => $staffId,
                        'message' => 'Setup hours are from 7:00 AM to 8:30 AM daily (before opening). Teardown is from 10:00 PM to 11:30 PM (after closing). For the day before the event, exhibitors can access the venue from 8:00 AM to 10:00 PM for initial setup. Would you like to reserve a setup slot?',
                        'message_ar' => 'ساعات التجهيز من 7:00 صباحاً إلى 8:30 صباحاً يومياً (قبل الافتتاح). الإزالة من 10:00 مساءً إلى 11:30 مساءً (بعد الإغلاق). ليوم ما قبل الفعالية، يمكن للعارضين الوصول من 8:00 صباحاً إلى 10:00 مساءً للتجهيز الأولي. هل ترغب في حجز موعد تجهيز؟',
                        'is_staff_reply' => true,
                    ],
                ],
            ],

            // ===== Ticket 6: Suggestion =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000015',
                'subject' => 'Feature request: Space comparison tool',
                'subject_ar' => 'طلب ميزة: أداة مقارنة المساحات',
                'description' => 'It would be very helpful to have a side-by-side comparison tool for spaces. When browsing spaces for an event, I want to compare prices, area, services, and location easily. This would help investors make faster decisions.',
                'description_ar' => 'سيكون من المفيد جداً وجود أداة مقارنة جنباً إلى جنب للمساحات. عند تصفح المساحات لفعالية، أريد مقارنة الأسعار والمساحة والخدمات والموقع بسهولة. هذا سيساعد المستثمرين على اتخاذ قرارات أسرع.',
                'category' => 'suggestion',
                'priority' => 'low',
                'status' => 'open',
                'replies' => [],
            ],

            // ===== Ticket 7: Urgent contract issue =====
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'subject' => 'Contract discrepancy - wrong total amount',
                'subject_ar' => 'تناقض في العقد - المبلغ الإجمالي خاطئ',
                'description' => 'My rental contract shows a total amount of SAR 8,400 but the space listing shows SAR 7,000 for the full event duration. I believe there is a pricing error that needs to be corrected before I sign.',
                'description_ar' => 'عقد الاستئجار يظهر مبلغ إجمالي 8,400 ريال لكن قائمة المساحة تظهر 7,000 ريال لمدة الفعالية الكاملة. أعتقد أن هناك خطأ في التسعير يحتاج للتصحيح قبل التوقيع.',
                'category' => 'contract',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'assigned_to' => $staffId,
                'replies' => [
                    [
                        'user_id' => $staffId,
                        'message' => 'Thank you for flagging this. I am reviewing the pricing details with our finance team. The difference might be due to additional services added to the space. I will get back to you within 24 hours with a detailed breakdown.',
                        'message_ar' => 'شكراً للإبلاغ عن هذا. أراجع تفاصيل التسعير مع فريقنا المالي. قد يكون الفرق بسبب خدمات إضافية أضيفت للمساحة. سأعود إليك خلال 24 ساعة مع تفصيل مفصل.',
                        'is_staff_reply' => true,
                    ],
                ],
            ],
        ];

        $ticketCount = 0;
        $replyCount = 0;

        foreach ($tickets as $ticketData) {
            $replies = $ticketData['replies'] ?? [];
            unset($ticketData['replies']);

            $ticket = SupportTicket::create($ticketData);
            $ticketCount++;

            foreach ($replies as $replyData) {
                TicketReply::create(array_merge($replyData, [
                    'ticket_id' => $ticket->id,
                ]));
                $replyCount++;
            }
        }

        $this->command->info("Created {$ticketCount} support tickets with {$replyCount} replies.");
    }
}
