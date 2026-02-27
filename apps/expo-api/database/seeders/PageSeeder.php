<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Maham Expo',
                'title_ar' => 'عن مهام إكسبو',
                'content' => '<h2>About Us</h2><p>Maham Expo is the leading exhibition and event management platform in the Kingdom of Saudi Arabia. We connect investors, merchants, and sponsors with premium exhibition spaces across major Saudi cities.</p><p>Our mission is to simplify the exhibition industry by providing a comprehensive digital platform that handles everything from space rental to sponsor management.</p><h3>Our Services</h3><ul><li>Exhibition space rental and management</li><li>Event organization and planning</li><li>Sponsor partnership management</li><li>Visitor registration and tracking</li></ul>',
                'content_ar' => '<h2>من نحن</h2><p>مهام إكسبو هي المنصة الرائدة لإدارة المعارض والفعاليات في المملكة العربية السعودية. نربط المستثمرين والتجار والرعاة بمساحات معارض متميزة في المدن السعودية الكبرى.</p><p>مهمتنا هي تبسيط صناعة المعارض من خلال توفير منصة رقمية شاملة تتعامل مع كل شيء من تأجير المساحات إلى إدارة الرعاة.</p><h3>خدماتنا</h3><ul><li>تأجير وإدارة مساحات المعارض</li><li>تنظيم وتخطيط الفعاليات</li><li>إدارة شراكات الرعاة</li><li>تسجيل وتتبع الزوار</li></ul>',
                'type' => 'about',
                'is_active' => true,
                'sort_order' => 1,
                'meta' => ['seo_title' => 'About Maham Expo - Exhibition Management Platform', 'seo_description' => 'Learn about Maham Expo, the leading exhibition management platform in Saudi Arabia.'],
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'title_ar' => 'الشروط والأحكام',
                'content' => '<h2>Terms and Conditions</h2><p>Last updated: January 2025</p><h3>1. Acceptance of Terms</h3><p>By accessing and using the Maham Expo platform, you agree to be bound by these Terms and Conditions.</p><h3>2. User Accounts</h3><p>Users must provide accurate and complete information when creating an account. You are responsible for maintaining the confidentiality of your account credentials.</p><h3>3. Space Rental</h3><p>All space rentals are subject to availability and approval by the event organizer. Rental fees must be paid according to the agreed payment schedule.</p><h3>4. Cancellation Policy</h3><p>Cancellations must be submitted at least 14 days before the event start date for a full refund. Late cancellations may incur fees.</p><h3>5. Sponsor Agreements</h3><p>Sponsor contracts are binding once signed by both parties. Benefits will be delivered as specified in the contract.</p>',
                'content_ar' => '<h2>الشروط والأحكام</h2><p>آخر تحديث: يناير 2025</p><h3>1. قبول الشروط</h3><p>بالوصول إلى منصة مهام إكسبو واستخدامها، فإنك توافق على الالتزام بهذه الشروط والأحكام.</p><h3>2. حسابات المستخدمين</h3><p>يجب على المستخدمين تقديم معلومات دقيقة وكاملة عند إنشاء الحساب. أنت مسؤول عن الحفاظ على سرية بيانات اعتماد حسابك.</p><h3>3. استئجار المساحات</h3><p>جميع عمليات الاستئجار تخضع للتوفر وموافقة منظم الفعالية. يجب دفع رسوم الاستئجار وفقاً لجدول الدفع المتفق عليه.</p><h3>4. سياسة الإلغاء</h3><p>يجب تقديم طلبات الإلغاء قبل 14 يوماً على الأقل من تاريخ بدء الفعالية لاسترداد كامل المبلغ.</p><h3>5. اتفاقيات الرعاية</h3><p>عقود الرعاية ملزمة بمجرد توقيعها من قبل الطرفين. سيتم تقديم المزايا كما هو محدد في العقد.</p>',
                'type' => 'terms',
                'is_active' => true,
                'sort_order' => 2,
                'meta' => ['seo_title' => 'Terms and Conditions - Maham Expo'],
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'content' => '<h2>Privacy Policy</h2><p>Last updated: January 2025</p><h3>1. Information We Collect</h3><p>We collect information you provide directly, including name, email, phone number, company details, and business registration documents.</p><h3>2. How We Use Your Information</h3><p>Your information is used to process rental requests, manage sponsorships, send notifications, and improve our services.</p><h3>3. Data Protection</h3><p>We implement industry-standard security measures to protect your personal data. All data is encrypted in transit and at rest.</p><h3>4. Third-Party Sharing</h3><p>We do not sell your personal information. We may share data with event organizers and sponsors as necessary to fulfill services.</p><h3>5. Your Rights</h3><p>You have the right to access, correct, or delete your personal data at any time through your account settings.</p>',
                'content_ar' => '<h2>سياسة الخصوصية</h2><p>آخر تحديث: يناير 2025</p><h3>1. المعلومات التي نجمعها</h3><p>نجمع المعلومات التي تقدمها مباشرة، بما في ذلك الاسم والبريد الإلكتروني ورقم الهاتف وتفاصيل الشركة ووثائق السجل التجاري.</p><h3>2. كيف نستخدم معلوماتك</h3><p>تُستخدم معلوماتك لمعالجة طلبات الاستئجار وإدارة الرعايات وإرسال الإشعارات وتحسين خدماتنا.</p><h3>3. حماية البيانات</h3><p>نطبق إجراءات أمنية معيارية لحماية بياناتك الشخصية. جميع البيانات مشفرة أثناء النقل وفي حالة السكون.</p><h3>4. المشاركة مع أطراف ثالثة</h3><p>لا نبيع معلوماتك الشخصية. قد نشارك البيانات مع منظمي الفعاليات والرعاة حسب الضرورة لتقديم الخدمات.</p><h3>5. حقوقك</h3><p>لديك الحق في الوصول إلى بياناتك الشخصية أو تصحيحها أو حذفها في أي وقت من خلال إعدادات حسابك.</p>',
                'type' => 'privacy',
                'is_active' => true,
                'sort_order' => 3,
                'meta' => ['seo_title' => 'Privacy Policy - Maham Expo'],
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'slug' => 'faq',
                'title' => 'Frequently Asked Questions',
                'title_ar' => 'الأسئلة الشائعة',
                'content' => '<h2>FAQ</h2><p>Find answers to the most commonly asked questions about our platform and services.</p>',
                'content_ar' => '<h2>الأسئلة الشائعة</h2><p>اعثر على إجابات للأسئلة الأكثر شيوعاً حول منصتنا وخدماتنا.</p>',
                'type' => 'faq',
                'is_active' => true,
                'sort_order' => 4,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'content' => '<h2>Contact Us</h2><p>We would love to hear from you. Reach out to us through any of the following channels:</p><ul><li>Email: support@mahamexpo.sa</li><li>Phone: +966 11 234 5678</li><li>Address: King Fahad Road, Riyadh, Saudi Arabia</li></ul><p>Our support team is available Sunday to Thursday, 9:00 AM to 6:00 PM (AST).</p>',
                'content_ar' => '<h2>اتصل بنا</h2><p>نود أن نسمع منك. تواصل معنا من خلال أي من القنوات التالية:</p><ul><li>البريد الإلكتروني: support@mahamexpo.sa</li><li>الهاتف: 5678 234 11 966+</li><li>العنوان: طريق الملك فهد، الرياض، المملكة العربية السعودية</li></ul><p>فريق الدعم متاح من الأحد إلى الخميس، من 9:00 صباحاً إلى 6:00 مساءً.</p>',
                'type' => 'contact',
                'is_active' => true,
                'sort_order' => 5,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'slug' => 'exhibitor-guide',
                'title' => 'Exhibitor Guide',
                'title_ar' => 'دليل العارضين',
                'content' => '<h2>Exhibitor Guide</h2><p>Welcome to the Maham Expo exhibitor guide. This document will help you prepare for your exhibition experience.</p><h3>Before the Event</h3><ul><li>Complete your business profile verification</li><li>Submit your rental request early</li><li>Review space specifications and services</li></ul><h3>During the Event</h3><ul><li>Set up your booth during designated hours</li><li>Follow all safety regulations</li><li>Engage with visitors professionally</li></ul>',
                'content_ar' => '<h2>دليل العارضين</h2><p>مرحباً بك في دليل العارضين لمهام إكسبو. ستساعدك هذه الوثيقة في التحضير لتجربتك في المعرض.</p><h3>قبل الفعالية</h3><ul><li>أكمل التحقق من ملفك التجاري</li><li>قدم طلب الاستئجار مبكراً</li><li>راجع مواصفات المساحة والخدمات</li></ul><h3>أثناء الفعالية</h3><ul><li>قم بتجهيز كشكك خلال الساعات المحددة</li><li>اتبع جميع أنظمة السلامة</li><li>تفاعل مع الزوار بشكل احترافي</li></ul>',
                'type' => 'custom',
                'is_active' => true,
                'sort_order' => 6,
                'meta' => ['seo_title' => 'Exhibitor Guide - Maham Expo'],
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
        ];

        if (Page::count() > 0) {
            $this->command->info('Pages already seeded, skipping.');
            return;
        }

        foreach ($pages as $page) {
            Page::create($page);
        }

        $this->command->info('Created ' . count($pages) . ' pages.');
    }
}
