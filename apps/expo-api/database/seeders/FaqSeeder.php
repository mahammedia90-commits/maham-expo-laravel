<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // General Category
            [
                'question' => 'What is Maham Expo?',
                'question_ar' => 'ما هي مهام إكسبو؟',
                'answer' => 'Maham Expo is a comprehensive exhibition and event management platform in Saudi Arabia. It connects investors, merchants, and sponsors with premium exhibition spaces and events across the Kingdom.',
                'answer_ar' => 'مهام إكسبو هي منصة شاملة لإدارة المعارض والفعاليات في المملكة العربية السعودية. تربط المستثمرين والتجار والرعاة بمساحات معارض وفعاليات متميزة في جميع أنحاء المملكة.',
                'category' => 'general',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 520,
                'helpful_count' => 85,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'question' => 'How do I create an account?',
                'question_ar' => 'كيف أنشئ حساباً؟',
                'answer' => 'You can create an account by downloading our mobile app or visiting our website. Click on "Register" and fill in your personal details. After email verification, you can complete your business profile.',
                'answer_ar' => 'يمكنك إنشاء حساب عن طريق تحميل تطبيقنا أو زيارة موقعنا. انقر على "تسجيل" وأكمل بياناتك الشخصية. بعد التحقق من البريد الإلكتروني، يمكنك إكمال ملفك التجاري.',
                'category' => 'general',
                'is_active' => true,
                'sort_order' => 2,
                'views_count' => 380,
                'helpful_count' => 62,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],

            // Space Category
            [
                'question' => 'How do I rent an exhibition space?',
                'question_ar' => 'كيف أستأجر مساحة معرض؟',
                'answer' => 'First, complete your business profile verification. Then browse available events and spaces. Select your preferred space and submit a rental request. Once approved, complete the payment to confirm your booking.',
                'answer_ar' => 'أولاً، أكمل التحقق من ملفك التجاري. ثم تصفح الفعاليات والمساحات المتاحة. اختر المساحة المفضلة وقدم طلب استئجار. بعد الموافقة، أكمل الدفع لتأكيد حجزك.',
                'category' => 'space',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 890,
                'helpful_count' => 145,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'question' => 'What types of spaces are available?',
                'question_ar' => 'ما أنواع المساحات المتاحة؟',
                'answer' => 'We offer various space types: Booths (small display areas), Shops (enclosed retail spaces), Offices (meeting and business spaces), Halls (large event areas), and Outdoor spaces. Each comes with customizable services.',
                'answer_ar' => 'نقدم أنواعاً مختلفة من المساحات: أكشاك (مناطق عرض صغيرة)، محلات (مساحات تجارية مغلقة)، مكاتب (مساحات اجتماعات وأعمال)، قاعات (مناطق فعاليات كبيرة)، ومساحات خارجية. كل منها يأتي مع خدمات قابلة للتخصيص.',
                'category' => 'space',
                'is_active' => true,
                'sort_order' => 2,
                'views_count' => 650,
                'helpful_count' => 98,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],

            // Billing Category
            [
                'question' => 'What payment methods are accepted?',
                'question_ar' => 'ما طرق الدفع المقبولة؟',
                'answer' => 'We accept bank transfers, credit/debit cards (Visa, Mastercard, mada), and SADAD payments. Payment plans including full payment, installments, daily, and monthly options are available depending on the space.',
                'answer_ar' => 'نقبل التحويلات البنكية، بطاقات الائتمان/الخصم (فيزا، ماستركارد، مدى)، ومدفوعات سداد. خطط الدفع تشمل الدفع الكامل، الأقساط، اليومي، والشهري حسب المساحة.',
                'category' => 'billing',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 720,
                'helpful_count' => 110,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'question' => 'Can I get a refund if I cancel my booking?',
                'question_ar' => 'هل يمكنني استرداد المبلغ إذا ألغيت حجزي؟',
                'answer' => 'Refund policies vary by event. Generally, cancellations made 14+ days before the event receive a full refund. Cancellations within 7-14 days receive a 50% refund. No refunds for cancellations within 7 days of the event.',
                'answer_ar' => 'تختلف سياسات الاسترداد حسب الفعالية. بشكل عام، الإلغاءات قبل 14+ يوماً من الفعالية تحصل على استرداد كامل. الإلغاءات خلال 7-14 يوماً تحصل على استرداد 50%. لا استرداد للإلغاءات خلال 7 أيام من الفعالية.',
                'category' => 'billing',
                'is_active' => true,
                'sort_order' => 2,
                'views_count' => 430,
                'helpful_count' => 72,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],

            // Event Category
            [
                'question' => 'How do I visit an exhibition?',
                'question_ar' => 'كيف أزور معرضاً؟',
                'answer' => 'Browse published events on our platform, select the event you want to visit, and submit a visit request with your preferred date and number of visitors. You will receive a confirmation once approved.',
                'answer_ar' => 'تصفح الفعاليات المنشورة على منصتنا، اختر الفعالية التي تريد زيارتها، وقدم طلب زيارة مع التاريخ المفضل وعدد الزوار. ستتلقى تأكيداً بعد الموافقة.',
                'category' => 'event',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 560,
                'helpful_count' => 88,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'question' => 'Can I organize my own event on Maham Expo?',
                'question_ar' => 'هل يمكنني تنظيم فعاليتي الخاصة على مهام إكسبو؟',
                'answer' => 'Yes! Contact our team to discuss organizing your event. We provide full support including venue management, space allocation, sponsor management, and visitor registration services.',
                'answer_ar' => 'نعم! تواصل مع فريقنا لمناقشة تنظيم فعاليتك. نقدم دعماً كاملاً يشمل إدارة المكان وتخصيص المساحات وإدارة الرعاة وخدمات تسجيل الزوار.',
                'category' => 'event',
                'is_active' => true,
                'sort_order' => 2,
                'views_count' => 290,
                'helpful_count' => 45,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],

            // Technical Category
            [
                'question' => 'What services are included with space rental?',
                'question_ar' => 'ما الخدمات المشمولة مع استئجار المساحة؟',
                'answer' => 'Available services include: Electricity, Water Supply, Internet & Wi-Fi, Air Conditioning, Security, Cleaning, Parking, Storage, Signage & Branding, and Furniture. Services vary by space and can be customized.',
                'answer_ar' => 'الخدمات المتاحة تشمل: الكهرباء، إمدادات المياه، الإنترنت والواي فاي، التكييف، الأمن، التنظيف، المواقف، التخزين، اللافتات والعلامات التجارية، والأثاث. تختلف الخدمات حسب المساحة ويمكن تخصيصها.',
                'category' => 'technical',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 480,
                'helpful_count' => 75,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],

            // Contract Category
            [
                'question' => 'How does the rental contract process work?',
                'question_ar' => 'كيف تعمل عملية عقد الاستئجار؟',
                'answer' => 'After your rental request is approved, a contract is generated. Both parties (merchant/investor) review and sign the contract digitally. Once signed, the contract becomes active and invoices are generated for payment.',
                'answer_ar' => 'بعد الموافقة على طلب الاستئجار، يتم إنشاء عقد. يقوم الطرفان (التاجر/المستثمر) بمراجعة العقد وتوقيعه رقمياً. بمجرد التوقيع، يصبح العقد نشطاً ويتم إنشاء الفواتير للدفع.',
                'category' => 'contract',
                'is_active' => true,
                'sort_order' => 1,
                'views_count' => 340,
                'helpful_count' => 55,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
        ];

        if (Faq::count() > 0) {
            $this->command->info('FAQs already seeded, skipping.');
            return;
        }

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        $this->command->info('Created ' . count($faqs) . ' FAQs.');
    }
}
