<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Sponsor;
use App\Models\SponsorAsset;
use App\Models\SponsorBenefit;
use App\Models\SponsorContract;
use App\Models\SponsorExposureTracking;
use App\Models\SponsorPackage;
use App\Models\SponsorPayment;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        if (Sponsor::count() > 0) {
            $this->command->info('Sponsors already seeded, skipping.');
            return;
        }

        $events = Event::where('status', 'published')->get();
        if ($events->isEmpty()) {
            $this->command->warn('No published events found. Run EventSeeder first.');
            return;
        }

        $techExpo = $events->firstWhere('name', 'Saudi Tech Expo 2025');
        $foodFestival = $events->firstWhere('name', 'Saudi Food Festival 2025');
        $fashionWeek = $events->firstWhere('name', 'Glamour Fashion Week');

        // ============================================================
        // Step 1: Create Sponsor Packages for each event
        // ============================================================
        $this->createPackages($techExpo, $foodFestival, $fashionWeek);

        // ============================================================
        // Step 2: Create Sponsors
        // ============================================================
        $sponsors = $this->createSponsors($techExpo, $foodFestival, $fashionWeek);

        // ============================================================
        // Step 3: Create Contracts, Payments, Benefits for each sponsor
        // ============================================================
        $this->createContractsAndRelations($sponsors, $techExpo, $foodFestival, $fashionWeek);

        // ============================================================
        // Step 4: Create Sponsor Assets
        // ============================================================
        $this->createAssets($sponsors, $techExpo, $foodFestival);

        // ============================================================
        // Step 5: Create Exposure Tracking data
        // ============================================================
        $this->createExposureTracking($sponsors, $techExpo, $foodFestival);

        $this->command->info('Sponsors ecosystem seeded successfully.');
    }

    protected function createPackages(?Event $techExpo, ?Event $foodFestival, ?Event $fashionWeek): void
    {
        $packages = [];

        if ($techExpo) {
            $packages = array_merge($packages, [
                [
                    'event_id' => $techExpo->id,
                    'name' => 'Platinum Sponsor',
                    'name_ar' => 'الراعي البلاتيني',
                    'description' => 'Premium sponsorship with maximum brand visibility across all event channels.',
                    'description_ar' => 'رعاية متميزة مع أقصى ظهور للعلامة التجارية عبر جميع قنوات الفعالية.',
                    'tier' => 'platinum',
                    'price' => 500000.00,
                    'max_sponsors' => 1,
                    'benefits' => ['Main stage branding', 'VIP lounge access', 'Keynote speaking slot', 'Full-page program ad', 'Digital screens rotation', 'Push notifications'],
                    'display_screens_count' => 10,
                    'banners_count' => 20,
                    'vip_invitations_count' => 50,
                    'booth_area_sqm' => 100,
                    'logo_placement' => ['main_stage', 'entrance', 'website', 'app', 'tickets', 'social_media', 'email'],
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'event_id' => $techExpo->id,
                    'name' => 'Gold Sponsor',
                    'name_ar' => 'الراعي الذهبي',
                    'description' => 'High-visibility sponsorship with premium placement and brand exposure.',
                    'description_ar' => 'رعاية بظهور عالٍ مع موضع متميز وانتشار للعلامة التجارية.',
                    'tier' => 'gold',
                    'price' => 250000.00,
                    'max_sponsors' => 3,
                    'benefits' => ['Stage branding', 'VIP access', 'Half-page program ad', 'Digital screens'],
                    'display_screens_count' => 5,
                    'banners_count' => 10,
                    'vip_invitations_count' => 25,
                    'booth_area_sqm' => 50,
                    'logo_placement' => ['entrance', 'website', 'app', 'tickets'],
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'event_id' => $techExpo->id,
                    'name' => 'Silver Sponsor',
                    'name_ar' => 'الراعي الفضي',
                    'description' => 'Standard sponsorship package with solid brand presence.',
                    'description_ar' => 'باقة رعاية قياسية مع حضور جيد للعلامة التجارية.',
                    'tier' => 'silver',
                    'price' => 100000.00,
                    'max_sponsors' => 5,
                    'benefits' => ['Banner placement', 'Quarter-page program ad', 'Digital screen mentions'],
                    'display_screens_count' => 2,
                    'banners_count' => 5,
                    'vip_invitations_count' => 10,
                    'booth_area_sqm' => 25,
                    'logo_placement' => ['website', 'app'],
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                [
                    'event_id' => $techExpo->id,
                    'name' => 'Media Partner',
                    'name_ar' => 'الشريك الإعلامي',
                    'description' => 'Media partnership with cross-promotional opportunities.',
                    'description_ar' => 'شراكة إعلامية مع فرص ترويج متبادل.',
                    'tier' => 'media_partner',
                    'price' => 50000.00,
                    'max_sponsors' => null,
                    'benefits' => ['Logo on media wall', 'Press access', 'Media kit'],
                    'display_screens_count' => 1,
                    'banners_count' => 3,
                    'vip_invitations_count' => 5,
                    'logo_placement' => ['website', 'media_wall'],
                    'is_active' => true,
                    'sort_order' => 5,
                ],
            ]);
        }

        if ($foodFestival) {
            $packages = array_merge($packages, [
                [
                    'event_id' => $foodFestival->id,
                    'name' => 'Gold Sponsor',
                    'name_ar' => 'الراعي الذهبي',
                    'description' => 'Premium food festival sponsorship with cooking show integration.',
                    'description_ar' => 'رعاية متميزة لمهرجان الطعام مع تكامل عروض الطبخ.',
                    'tier' => 'gold',
                    'price' => 150000.00,
                    'max_sponsors' => 2,
                    'benefits' => ['Cooking show branding', 'Food court naming', 'VIP tasting area'],
                    'display_screens_count' => 5,
                    'banners_count' => 8,
                    'vip_invitations_count' => 20,
                    'booth_area_sqm' => 40,
                    'logo_placement' => ['entrance', 'food_court', 'website', 'app'],
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'event_id' => $foodFestival->id,
                    'name' => 'Bronze Sponsor',
                    'name_ar' => 'الراعي البرونزي',
                    'description' => 'Entry-level sponsorship for food festival.',
                    'description_ar' => 'رعاية مبتدئة لمهرجان الطعام.',
                    'tier' => 'bronze',
                    'price' => 30000.00,
                    'max_sponsors' => 10,
                    'benefits' => ['Banner in food court', 'Logo on website'],
                    'display_screens_count' => 1,
                    'banners_count' => 2,
                    'vip_invitations_count' => 5,
                    'logo_placement' => ['website'],
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ]);
        }

        foreach ($packages as $package) {
            SponsorPackage::create($package);
        }

        $this->command->info('Created ' . count($packages) . ' sponsor packages.');
    }

    protected function createSponsors(?Event $techExpo, ?Event $foodFestival, ?Event $fashionWeek): array
    {
        $sponsorsData = [];
        $sponsors = [];

        if ($techExpo) {
            $sponsorsData = array_merge($sponsorsData, [
                [
                    'event_id' => $techExpo->id,
                    'user_id' => '00000000-0000-0000-0000-000000000020',
                    'name' => 'Saudi Digital Solutions',
                    'name_ar' => 'الحلول الرقمية السعودية',
                    'company_name' => 'Saudi Digital Solutions Co.',
                    'company_name_ar' => 'شركة الحلول الرقمية السعودية',
                    'description' => 'Leading digital transformation company in the Middle East.',
                    'description_ar' => 'شركة رائدة في التحول الرقمي في الشرق الأوسط.',
                    'logo' => 'sponsors/saudi-digital.png',
                    'contact_person' => 'Ahmed Al-Rashid',
                    'contact_email' => 'sponsor@saudidigital.sa',
                    'contact_phone' => '0501112233',
                    'website' => 'https://saudidigital.sa',
                    'status' => 'active',
                    'created_by' => '00000000-0000-0000-0000-000000000099',
                    'created_from' => 'admin',
                ],
                [
                    'event_id' => $techExpo->id,
                    'user_id' => '00000000-0000-0000-0000-000000000021',
                    'name' => 'CloudFirst Arabia',
                    'name_ar' => 'كلاود فيرست العربية',
                    'company_name' => 'CloudFirst Arabia Ltd.',
                    'company_name_ar' => 'شركة كلاود فيرست العربية المحدودة',
                    'description' => 'Cloud computing and infrastructure services provider.',
                    'description_ar' => 'مزود خدمات الحوسبة السحابية والبنية التحتية.',
                    'logo' => 'sponsors/cloudfirst.png',
                    'contact_person' => 'Khalid Al-Otaibi',
                    'contact_email' => 'partnerships@cloudfirst.sa',
                    'contact_phone' => '0502223344',
                    'website' => 'https://cloudfirst.sa',
                    'status' => 'active',
                    'created_by' => '00000000-0000-0000-0000-000000000099',
                    'created_from' => 'admin',
                ],
                [
                    'event_id' => $techExpo->id,
                    'user_id' => '00000000-0000-0000-0000-000000000022',
                    'name' => 'CyberShield SA',
                    'name_ar' => 'سايبر شيلد السعودية',
                    'company_name' => 'CyberShield Security Co.',
                    'company_name_ar' => 'شركة سايبر شيلد للأمن',
                    'description' => 'Cybersecurity and data protection solutions.',
                    'description_ar' => 'حلول الأمن السيبراني وحماية البيانات.',
                    'logo' => 'sponsors/cybershield.png',
                    'contact_person' => 'Sara Al-Ghamdi',
                    'contact_email' => 'sponsor@cybershield.sa',
                    'contact_phone' => '0503334455',
                    'status' => 'pending',
                    'created_by' => '00000000-0000-0000-0000-000000000099',
                    'created_from' => 'admin',
                ],
            ]);
        }

        if ($foodFestival) {
            $sponsorsData = array_merge($sponsorsData, [
                [
                    'event_id' => $foodFestival->id,
                    'user_id' => '00000000-0000-0000-0000-000000000023',
                    'name' => 'Almarai',
                    'name_ar' => 'المراعي',
                    'company_name' => 'Almarai Company',
                    'company_name_ar' => 'شركة المراعي',
                    'description' => 'The largest dairy food company in the Middle East.',
                    'description_ar' => 'أكبر شركة أغذية ألبان في الشرق الأوسط.',
                    'logo' => 'sponsors/almarai.png',
                    'contact_person' => 'Faisal Al-Harbi',
                    'contact_email' => 'sponsorship@almarai.com',
                    'contact_phone' => '0504445566',
                    'website' => 'https://almarai.com',
                    'status' => 'active',
                    'created_by' => '00000000-0000-0000-0000-000000000099',
                    'created_from' => 'admin',
                ],
                [
                    'event_id' => $foodFestival->id,
                    'user_id' => '00000000-0000-0000-0000-000000000024',
                    'name' => 'Saudia Dairy & Foodstuff',
                    'name_ar' => 'السعودية للألبان والأغذية',
                    'company_name' => 'SADAFCO',
                    'company_name_ar' => 'سدافكو',
                    'description' => 'Saudi manufacturer of dairy and food products.',
                    'description_ar' => 'شركة سعودية لصناعة منتجات الألبان والغذاء.',
                    'logo' => 'sponsors/sadafco.png',
                    'contact_person' => 'Noura Al-Zahrani',
                    'contact_email' => 'events@sadafco.com',
                    'contact_phone' => '0505556677',
                    'status' => 'approved',
                    'created_by' => '00000000-0000-0000-0000-000000000099',
                    'created_from' => 'admin',
                ],
            ]);
        }

        foreach ($sponsorsData as $data) {
            $sponsors[] = Sponsor::create($data);
        }

        $this->command->info('Created ' . count($sponsors) . ' sponsors.');

        return $sponsors;
    }

    protected function createContractsAndRelations(array $sponsors, ?Event $techExpo, ?Event $foodFestival, ?Event $fashionWeek): void
    {
        $contractCount = 0;
        $paymentCount = 0;
        $benefitCount = 0;

        // Sponsor 1: Saudi Digital (Platinum) - Active contract, fully paid
        $saudiDigital = collect($sponsors)->firstWhere('name', 'Saudi Digital Solutions');
        if ($saudiDigital && $techExpo) {
            $platinumPkg = SponsorPackage::where('event_id', $techExpo->id)->where('tier', 'platinum')->first();
            if ($platinumPkg) {
                $contract = SponsorContract::create([
                    'sponsor_id' => $saudiDigital->id,
                    'sponsor_package_id' => $platinumPkg->id,
                    'event_id' => $techExpo->id,
                    'start_date' => $techExpo->start_date,
                    'end_date' => $techExpo->end_date,
                    'total_amount' => 500000.00,
                    'paid_amount' => 500000.00,
                    'payment_status' => 'paid',
                    'status' => 'active',
                    'terms' => 'Platinum sponsorship agreement for Saudi Tech Expo 2025. Full branding rights across all event channels.',
                    'terms_ar' => 'اتفاقية الرعاية البلاتينية لمعرض السعودية التقني 2025. حقوق كاملة للعلامة التجارية عبر جميع قنوات الفعالية.',
                    'signed_at' => now()->subDays(20),
                    'signed_by' => $saudiDigital->user_id,
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(20),
                ]);
                $contractCount++;

                // Payments for Saudi Digital (2 installments - both paid)
                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 250000.00,
                    'due_date' => now()->subDays(15),
                    'paid_at' => now()->subDays(14),
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => 'TXN-SD-001',
                    'status' => 'paid',
                    'notes' => 'First installment - platinum sponsorship',
                ]);
                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 250000.00,
                    'due_date' => now()->subDays(5),
                    'paid_at' => now()->subDays(4),
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => 'TXN-SD-002',
                    'status' => 'paid',
                    'notes' => 'Second installment - platinum sponsorship',
                ]);
                $paymentCount += 2;

                // Benefits for platinum sponsor
                $benefits = [
                    ['benefit_type' => 'screen', 'description' => 'Main entrance digital screen (10 screens)', 'description_ar' => 'شاشة رقمية عند المدخل الرئيسي (10 شاشات)', 'quantity' => 10, 'delivered_quantity' => 10, 'status' => 'delivered', 'delivery_notes' => 'All screens activated'],
                    ['benefit_type' => 'banner', 'description' => 'Event hall banners (20 locations)', 'description_ar' => 'لافتات قاعة الفعالية (20 موقع)', 'quantity' => 20, 'delivered_quantity' => 18, 'status' => 'in_progress', 'delivery_notes' => '18 of 20 banners installed'],
                    ['benefit_type' => 'booth', 'description' => 'Premium exhibition booth (100 sqm)', 'description_ar' => 'كشك معرض متميز (100 متر مربع)', 'quantity' => 1, 'delivered_quantity' => 1, 'status' => 'delivered'],
                    ['benefit_type' => 'vip_invitation', 'description' => 'VIP event invitations (50 cards)', 'description_ar' => 'دعوات VIP للفعالية (50 بطاقة)', 'quantity' => 50, 'delivered_quantity' => 50, 'status' => 'delivered'],
                    ['benefit_type' => 'notification', 'description' => 'Push notification to all attendees', 'description_ar' => 'إشعار لجميع الحضور', 'quantity' => 3, 'delivered_quantity' => 1, 'status' => 'in_progress'],
                    ['benefit_type' => 'logo', 'description' => 'Logo placement on all event materials', 'description_ar' => 'وضع الشعار على جميع مواد الفعالية', 'quantity' => 1, 'delivered_quantity' => 1, 'status' => 'delivered'],
                ];

                foreach ($benefits as $benefit) {
                    SponsorBenefit::create(array_merge($benefit, [
                        'sponsor_contract_id' => $contract->id,
                    ]));
                    $benefitCount++;
                }
            }
        }

        // Sponsor 2: CloudFirst (Gold) - Active contract, partially paid
        $cloudFirst = collect($sponsors)->firstWhere('name', 'CloudFirst Arabia');
        if ($cloudFirst && $techExpo) {
            $goldPkg = SponsorPackage::where('event_id', $techExpo->id)->where('tier', 'gold')->first();
            if ($goldPkg) {
                $contract = SponsorContract::create([
                    'sponsor_id' => $cloudFirst->id,
                    'sponsor_package_id' => $goldPkg->id,
                    'event_id' => $techExpo->id,
                    'start_date' => $techExpo->start_date,
                    'end_date' => $techExpo->end_date,
                    'total_amount' => 250000.00,
                    'paid_amount' => 125000.00,
                    'payment_status' => 'partial',
                    'status' => 'active',
                    'terms' => 'Gold sponsorship agreement for Saudi Tech Expo 2025.',
                    'terms_ar' => 'اتفاقية الرعاية الذهبية لمعرض السعودية التقني 2025.',
                    'signed_at' => now()->subDays(15),
                    'signed_by' => $cloudFirst->user_id,
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(15),
                ]);
                $contractCount++;

                // Payments: 1 paid, 1 pending
                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 125000.00,
                    'due_date' => now()->subDays(10),
                    'paid_at' => now()->subDays(9),
                    'payment_method' => 'credit_card',
                    'transaction_reference' => 'TXN-CF-001',
                    'status' => 'paid',
                ]);
                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 125000.00,
                    'due_date' => now()->addDays(10),
                    'status' => 'pending',
                    'notes' => 'Second installment due',
                ]);
                $paymentCount += 2;

                // Benefits for gold
                $benefits = [
                    ['benefit_type' => 'screen', 'description' => 'Digital screens in cloud section (5 screens)', 'description_ar' => 'شاشات رقمية في قسم السحابة (5 شاشات)', 'quantity' => 5, 'delivered_quantity' => 5, 'status' => 'delivered'],
                    ['benefit_type' => 'banner', 'description' => 'Section banners (10 locations)', 'description_ar' => 'لافتات القسم (10 مواقع)', 'quantity' => 10, 'delivered_quantity' => 6, 'status' => 'in_progress'],
                    ['benefit_type' => 'booth', 'description' => 'Gold booth (50 sqm)', 'description_ar' => 'كشك ذهبي (50 متر مربع)', 'quantity' => 1, 'delivered_quantity' => 1, 'status' => 'delivered'],
                    ['benefit_type' => 'vip_invitation', 'description' => 'VIP invitations (25 cards)', 'description_ar' => 'دعوات VIP (25 بطاقة)', 'quantity' => 25, 'delivered_quantity' => 0, 'status' => 'pending'],
                ];

                foreach ($benefits as $benefit) {
                    SponsorBenefit::create(array_merge($benefit, [
                        'sponsor_contract_id' => $contract->id,
                    ]));
                    $benefitCount++;
                }
            }
        }

        // Sponsor 4: Almarai (Gold for Food Festival) - Active, paid
        $almarai = collect($sponsors)->firstWhere('name', 'Almarai');
        if ($almarai && $foodFestival) {
            $goldFoodPkg = SponsorPackage::where('event_id', $foodFestival->id)->where('tier', 'gold')->first();
            if ($goldFoodPkg) {
                $contract = SponsorContract::create([
                    'sponsor_id' => $almarai->id,
                    'sponsor_package_id' => $goldFoodPkg->id,
                    'event_id' => $foodFestival->id,
                    'start_date' => $foodFestival->start_date,
                    'end_date' => $foodFestival->end_date,
                    'total_amount' => 150000.00,
                    'paid_amount' => 150000.00,
                    'payment_status' => 'paid',
                    'status' => 'active',
                    'terms' => 'Gold sponsorship for Saudi Food Festival 2025. Includes cooking show branding and food court naming rights.',
                    'terms_ar' => 'رعاية ذهبية لمهرجان الطعام السعودي 2025. تشمل العلامة التجارية على عروض الطبخ وحقوق تسمية منطقة الطعام.',
                    'signed_at' => now()->subDays(12),
                    'signed_by' => $almarai->user_id,
                    'reviewed_by' => '00000000-0000-0000-0000-000000000099',
                    'reviewed_at' => now()->subDays(12),
                ]);
                $contractCount++;

                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 150000.00,
                    'due_date' => now()->subDays(10),
                    'paid_at' => now()->subDays(8),
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => 'TXN-AL-001',
                    'status' => 'paid',
                ]);
                $paymentCount++;

                $benefits = [
                    ['benefit_type' => 'screen', 'description' => 'Food court digital displays', 'description_ar' => 'شاشات عرض رقمية في منطقة الطعام', 'quantity' => 5, 'delivered_quantity' => 0, 'status' => 'pending'],
                    ['benefit_type' => 'banner', 'description' => 'Event entrance and cooking show banners', 'description_ar' => 'لافتات مدخل الفعالية وعروض الطبخ', 'quantity' => 8, 'delivered_quantity' => 0, 'status' => 'pending'],
                    ['benefit_type' => 'booth', 'description' => 'Tasting booth (40 sqm)', 'description_ar' => 'كشك تذوق (40 متر مربع)', 'quantity' => 1, 'delivered_quantity' => 0, 'status' => 'pending'],
                    ['benefit_type' => 'vip_invitation', 'description' => 'VIP tasting invitations', 'description_ar' => 'دعوات تذوق VIP', 'quantity' => 20, 'delivered_quantity' => 0, 'status' => 'pending'],
                ];

                foreach ($benefits as $benefit) {
                    SponsorBenefit::create(array_merge($benefit, [
                        'sponsor_contract_id' => $contract->id,
                    ]));
                    $benefitCount++;
                }
            }
        }

        // Sponsor 5: SADAFCO (Bronze for Food) - Pending contract
        $sadafco = collect($sponsors)->firstWhere('name', 'Saudia Dairy & Foodstuff');
        if ($sadafco && $foodFestival) {
            $bronzePkg = SponsorPackage::where('event_id', $foodFestival->id)->where('tier', 'bronze')->first();
            if ($bronzePkg) {
                $contract = SponsorContract::create([
                    'sponsor_id' => $sadafco->id,
                    'sponsor_package_id' => $bronzePkg->id,
                    'event_id' => $foodFestival->id,
                    'start_date' => $foodFestival->start_date,
                    'end_date' => $foodFestival->end_date,
                    'total_amount' => 30000.00,
                    'paid_amount' => 0,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'terms' => 'Bronze sponsorship for Saudi Food Festival 2025.',
                    'terms_ar' => 'رعاية برونزية لمهرجان الطعام السعودي 2025.',
                    'notes' => 'Awaiting internal approval from SADAFCO management.',
                ]);
                $contractCount++;

                SponsorPayment::create([
                    'sponsor_contract_id' => $contract->id,
                    'amount' => 30000.00,
                    'due_date' => now()->addDays(15),
                    'status' => 'pending',
                ]);
                $paymentCount++;
            }
        }

        $this->command->info("Created {$contractCount} sponsor contracts, {$paymentCount} payments, {$benefitCount} benefits.");
    }

    protected function createAssets(array $sponsors, ?Event $techExpo, ?Event $foodFestival): void
    {
        $assetCount = 0;

        $saudiDigital = collect($sponsors)->firstWhere('name', 'Saudi Digital Solutions');
        if ($saudiDigital && $techExpo) {
            $assets = [
                [
                    'sponsor_id' => $saudiDigital->id,
                    'event_id' => $techExpo->id,
                    'type' => 'logo',
                    'file_path' => 'sponsor-assets/saudi-digital/logo-primary.png',
                    'file_name' => 'logo-primary.png',
                    'file_size' => 245760,
                    'mime_type' => 'image/png',
                    'is_approved' => true,
                    'approved_by' => '00000000-0000-0000-0000-000000000099',
                    'approved_at' => now()->subDays(18),
                    'sort_order' => 1,
                ],
                [
                    'sponsor_id' => $saudiDigital->id,
                    'event_id' => $techExpo->id,
                    'type' => 'banner',
                    'file_path' => 'sponsor-assets/saudi-digital/banner-main.jpg',
                    'file_name' => 'banner-main.jpg',
                    'file_size' => 1048576,
                    'mime_type' => 'image/jpeg',
                    'is_approved' => true,
                    'approved_by' => '00000000-0000-0000-0000-000000000099',
                    'approved_at' => now()->subDays(17),
                    'sort_order' => 2,
                ],
                [
                    'sponsor_id' => $saudiDigital->id,
                    'event_id' => $techExpo->id,
                    'type' => 'video',
                    'file_path' => 'sponsor-assets/saudi-digital/promo-video.mp4',
                    'file_name' => 'promo-video.mp4',
                    'file_size' => 52428800,
                    'mime_type' => 'video/mp4',
                    'is_approved' => true,
                    'approved_by' => '00000000-0000-0000-0000-000000000099',
                    'approved_at' => now()->subDays(16),
                    'sort_order' => 3,
                ],
                [
                    'sponsor_id' => $saudiDigital->id,
                    'event_id' => $techExpo->id,
                    'type' => 'booth_design',
                    'file_path' => 'sponsor-assets/saudi-digital/booth-design.pdf',
                    'file_name' => 'booth-design.pdf',
                    'file_size' => 5242880,
                    'mime_type' => 'application/pdf',
                    'is_approved' => true,
                    'approved_by' => '00000000-0000-0000-0000-000000000099',
                    'approved_at' => now()->subDays(15),
                    'sort_order' => 4,
                ],
            ];

            foreach ($assets as $asset) {
                SponsorAsset::create($asset);
                $assetCount++;
            }
        }

        $cloudFirst = collect($sponsors)->firstWhere('name', 'CloudFirst Arabia');
        if ($cloudFirst && $techExpo) {
            $assets = [
                [
                    'sponsor_id' => $cloudFirst->id,
                    'event_id' => $techExpo->id,
                    'type' => 'logo',
                    'file_path' => 'sponsor-assets/cloudfirst/logo.png',
                    'file_name' => 'cloudfirst-logo.png',
                    'file_size' => 184320,
                    'mime_type' => 'image/png',
                    'is_approved' => true,
                    'approved_by' => '00000000-0000-0000-0000-000000000099',
                    'approved_at' => now()->subDays(13),
                    'sort_order' => 1,
                ],
                [
                    'sponsor_id' => $cloudFirst->id,
                    'event_id' => $techExpo->id,
                    'type' => 'banner',
                    'file_path' => 'sponsor-assets/cloudfirst/banner-cloud.jpg',
                    'file_name' => 'banner-cloud.jpg',
                    'file_size' => 819200,
                    'mime_type' => 'image/jpeg',
                    'is_approved' => false,
                    'rejection_reason' => 'Banner resolution does not meet minimum requirements (1920x1080). Please upload a higher quality version.',
                    'sort_order' => 2,
                ],
            ];

            foreach ($assets as $asset) {
                SponsorAsset::create($asset);
                $assetCount++;
            }
        }

        $almarai = collect($sponsors)->firstWhere('name', 'Almarai');
        if ($almarai && $foodFestival) {
            SponsorAsset::create([
                'sponsor_id' => $almarai->id,
                'event_id' => $foodFestival->id,
                'type' => 'logo',
                'file_path' => 'sponsor-assets/almarai/logo.png',
                'file_name' => 'almarai-logo.png',
                'file_size' => 204800,
                'mime_type' => 'image/png',
                'is_approved' => true,
                'approved_by' => '00000000-0000-0000-0000-000000000099',
                'approved_at' => now()->subDays(10),
                'sort_order' => 1,
            ]);
            $assetCount++;
        }

        $this->command->info("Created {$assetCount} sponsor assets.");
    }

    protected function createExposureTracking(array $sponsors, ?Event $techExpo, ?Event $foodFestival): void
    {
        $trackingCount = 0;

        $saudiDigital = collect($sponsors)->firstWhere('name', 'Saudi Digital Solutions');
        $cloudFirst = collect($sponsors)->firstWhere('name', 'CloudFirst Arabia');
        $almarai = collect($sponsors)->firstWhere('name', 'Almarai');

        $saudiDigitalContract = $saudiDigital ? SponsorContract::where('sponsor_id', $saudiDigital->id)->first() : null;
        $cloudFirstContract = $cloudFirst ? SponsorContract::where('sponsor_id', $cloudFirst->id)->first() : null;
        $almaraiContract = $almarai ? SponsorContract::where('sponsor_id', $almarai->id)->first() : null;

        $channels = ['app', 'website', 'screen', 'ticket', 'email', 'push_notification', 'social_media'];

        // Saudi Digital exposure (platinum - high numbers)
        if ($saudiDigital && $techExpo) {
            for ($i = 5; $i >= 0; $i--) {
                foreach (['app', 'website', 'screen', 'email', 'social_media'] as $channel) {
                    $baseImpressions = match ($channel) {
                        'app' => rand(800, 1500),
                        'website' => rand(2000, 4000),
                        'screen' => rand(3000, 6000),
                        'email' => rand(500, 1200),
                        'social_media' => rand(1500, 3000),
                        default => rand(100, 500),
                    };

                    SponsorExposureTracking::create([
                        'sponsor_id' => $saudiDigital->id,
                        'event_id' => $techExpo->id,
                        'sponsor_contract_id' => $saudiDigitalContract?->id,
                        'channel' => $channel,
                        'impressions_count' => $baseImpressions,
                        'clicks_count' => intval($baseImpressions * (rand(2, 8) / 100)),
                        'date' => now()->subDays($i)->toDateString(),
                        'metadata' => ['source' => 'organic', 'region' => 'riyadh'],
                    ]);
                    $trackingCount++;
                }
            }
        }

        // CloudFirst exposure (gold - moderate numbers)
        if ($cloudFirst && $techExpo) {
            for ($i = 4; $i >= 0; $i--) {
                foreach (['app', 'website', 'screen'] as $channel) {
                    $baseImpressions = match ($channel) {
                        'app' => rand(400, 800),
                        'website' => rand(1000, 2000),
                        'screen' => rand(1500, 3000),
                        default => rand(100, 300),
                    };

                    SponsorExposureTracking::create([
                        'sponsor_id' => $cloudFirst->id,
                        'event_id' => $techExpo->id,
                        'sponsor_contract_id' => $cloudFirstContract?->id,
                        'channel' => $channel,
                        'impressions_count' => $baseImpressions,
                        'clicks_count' => intval($baseImpressions * (rand(1, 5) / 100)),
                        'date' => now()->subDays($i)->toDateString(),
                    ]);
                    $trackingCount++;
                }
            }
        }

        // Almarai exposure (food festival)
        if ($almarai && $foodFestival) {
            for ($i = 2; $i >= 0; $i--) {
                foreach (['app', 'website'] as $channel) {
                    SponsorExposureTracking::create([
                        'sponsor_id' => $almarai->id,
                        'event_id' => $foodFestival->id,
                        'sponsor_contract_id' => $almaraiContract?->id,
                        'channel' => $channel,
                        'impressions_count' => rand(500, 1500),
                        'clicks_count' => rand(15, 60),
                        'date' => now()->subDays($i)->toDateString(),
                    ]);
                    $trackingCount++;
                }
            }
        }

        $this->command->info("Created {$trackingCount} exposure tracking records.");
    }
}
