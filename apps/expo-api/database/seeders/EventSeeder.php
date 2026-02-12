<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\Section;
use App\Models\Service;
use App\Models\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $cities = City::all();
        $services = Service::all();

        if ($categories->isEmpty() || $cities->isEmpty()) {
            $this->command->warn('Please run CategorySeeder and CitySeeder first.');
            return;
        }

        $events = $this->getEventsData($categories, $cities);

        foreach ($events as $eventData) {
            $sections = $eventData['sections'] ?? [];
            unset($eventData['sections']);

            $event = Event::create($eventData);

            foreach ($sections as $sectionData) {
                $spaces = $sectionData['spaces'] ?? [];
                unset($sectionData['spaces']);

                $sectionData['event_id'] = $event->id;
                $section = Section::create($sectionData);

                foreach ($spaces as $spaceData) {
                    $attachServices = $spaceData['services'] ?? [];
                    unset($spaceData['services']);

                    $spaceData['event_id'] = $event->id;
                    $spaceData['section_id'] = $section->id;
                    $space = Space::create($spaceData);

                    // Attach services with explicit UUIDs for pivot table
                    if (!empty($attachServices) && $services->isNotEmpty()) {
                        $serviceIds = $services->whereIn('name', $attachServices)->pluck('id')->toArray();
                        if (!empty($serviceIds)) {
                            $pivotData = [];
                            foreach ($serviceIds as $serviceId) {
                                $pivotData[$serviceId] = ['id' => Str::uuid()->toString()];
                            }
                            $space->services()->attach($pivotData);
                        }
                    }
                }
            }

            $this->command->info("Created event: {$event->name}");
        }
    }

    protected function getEventsData($categories, $cities): array
    {
        $riyadh = $cities->firstWhere('name', 'Riyadh');
        $jeddah = $cities->firstWhere('name', 'Jeddah');
        $dammam = $cities->firstWhere('name', 'Dammam');
        $makkah = $cities->firstWhere('name', 'Makkah');
        $khobar = $cities->firstWhere('name', 'Khobar');

        $tradeCat = $categories->firstWhere('name', 'Trade Exhibitions');
        $techCat = $categories->firstWhere('name', 'Technology & Innovation');
        $foodCat = $categories->firstWhere('name', 'Food & Beverages');
        $fashionCat = $categories->firstWhere('name', 'Fashion & Beauty');
        $realEstateCat = $categories->firstWhere('name', 'Real Estate');
        $autoCat = $categories->firstWhere('name', 'Automotive');
        $healthCat = $categories->firstWhere('name', 'Health & Medical');
        $eduCat = $categories->firstWhere('name', 'Education & Training');

        return [
            // ============ Event 1: Ongoing Featured Tech Exhibition ============
            [
                'name' => 'Saudi Tech Expo 2025',
                'name_ar' => 'معرض السعودية التقني 2025',
                'description' => 'The largest technology and innovation exhibition in the Kingdom. Featuring the latest in AI, IoT, cybersecurity, cloud computing and digital transformation solutions.',
                'description_ar' => 'أكبر معرض للتقنية والابتكار في المملكة. يضم أحدث حلول الذكاء الاصطناعي وإنترنت الأشياء والأمن السيبراني والحوسبة السحابية والتحول الرقمي.',
                'category_id' => $techCat->id,
                'city_id' => $riyadh->id,
                'address' => 'Riyadh Front Exhibition Center',
                'address_ar' => 'مركز معارض واجهة الرياض',
                'latitude' => 24.7549,
                'longitude' => 46.6528,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'opening_time' => '09:00',
                'closing_time' => '22:00',
                'organizer_name' => 'Saudi Events Co.',
                'organizer_phone' => '0112345678',
                'organizer_email' => 'info@sauditechexpo.sa',
                'website' => 'https://sauditechexpo.sa',
                'status' => 'published',
                'is_featured' => true,
                'features' => ['Free Parking', 'Prayer Room', 'Food Court', 'VIP Lounge'],
                'features_ar' => ['مواقف مجانية', 'مصلى', 'منطقة طعام', 'صالة كبار الزوار'],
                'sections' => [
                    [
                        'name' => 'AI & Machine Learning',
                        'name_ar' => 'الذكاء الاصطناعي وتعلم الآلة',
                        'description' => 'Dedicated zone for artificial intelligence companies',
                        'description_ar' => 'منطقة مخصصة لشركات الذكاء الاصطناعي',
                        'icon' => 'brain',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Booth AI-01',
                                'name_ar' => 'كشك ذ.ا-01',
                                'location_code' => 'AI-01',
                                'area_sqm' => 20,
                                'price_per_day' => 800,
                                'price_total' => 24000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture'],
                            ],
                            [
                                'name' => 'Booth AI-02',
                                'name_ar' => 'كشك ذ.ا-02',
                                'location_code' => 'AI-02',
                                'area_sqm' => 30,
                                'price_per_day' => 1200,
                                'price_total' => 36000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture', 'Signage & Branding'],
                            ],
                            [
                                'name' => 'Shop AI-03',
                                'name_ar' => 'محل ذ.ا-03',
                                'location_code' => 'AI-03',
                                'area_sqm' => 50,
                                'price_per_day' => 2000,
                                'price_total' => 60000,
                                'status' => 'reserved',
                                'floor_number' => 1,
                                'space_type' => 'shop',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture', 'Signage & Branding', 'Storage'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Cloud & Infrastructure',
                        'name_ar' => 'الحوسبة السحابية والبنية التحتية',
                        'description' => 'Cloud computing and IT infrastructure solutions',
                        'description_ar' => 'حلول الحوسبة السحابية والبنية التحتية',
                        'icon' => 'cloud',
                        'sort_order' => 2,
                        'spaces' => [
                            [
                                'name' => 'Booth CL-01',
                                'name_ar' => 'كشك سح-01',
                                'location_code' => 'CL-01',
                                'area_sqm' => 25,
                                'price_per_day' => 900,
                                'price_total' => 27000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning'],
                            ],
                            [
                                'name' => 'Hall CL-02',
                                'name_ar' => 'قاعة سح-02',
                                'location_code' => 'CL-02',
                                'area_sqm' => 100,
                                'price_per_day' => 5000,
                                'price_total' => 150000,
                                'status' => 'available',
                                'floor_number' => 2,
                                'space_type' => 'hall',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture', 'Security', 'Cleaning'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Cybersecurity',
                        'name_ar' => 'الأمن السيبراني',
                        'description' => 'Cybersecurity and data protection solutions',
                        'description_ar' => 'حلول الأمن السيبراني وحماية البيانات',
                        'icon' => 'shield',
                        'sort_order' => 3,
                        'spaces' => [
                            [
                                'name' => 'Booth CS-01',
                                'name_ar' => 'كشك أس-01',
                                'location_code' => 'CS-01',
                                'area_sqm' => 15,
                                'price_per_day' => 600,
                                'price_total' => 18000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'daily',
                                'rental_duration' => 'daily',
                                'services' => ['Electricity', 'Internet & Wi-Fi'],
                            ],
                            [
                                'name' => 'Booth CS-02',
                                'name_ar' => 'كشك أس-02',
                                'location_code' => 'CS-02',
                                'area_sqm' => 15,
                                'price_per_day' => 600,
                                'price_total' => 18000,
                                'status' => 'rented',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'daily',
                                'rental_duration' => 'weekly',
                                'services' => ['Electricity', 'Internet & Wi-Fi'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 2: Upcoming Food Festival ============
            [
                'name' => 'Saudi Food Festival 2025',
                'name_ar' => 'مهرجان الطعام السعودي 2025',
                'description' => 'A celebration of Saudi and international cuisines. Join top chefs and food enthusiasts for tastings, cooking shows, and culinary workshops.',
                'description_ar' => 'احتفال بالمأكولات السعودية والعالمية. انضم لأفضل الطهاة وعشاق الطعام للتذوق وعروض الطبخ وورش الطهي.',
                'category_id' => $foodCat->id,
                'city_id' => $jeddah->id,
                'address' => 'Jeddah Superdome',
                'address_ar' => 'سوبردوم جدة',
                'latitude' => 21.5433,
                'longitude' => 39.1728,
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(20),
                'opening_time' => '16:00',
                'closing_time' => '00:00',
                'organizer_name' => 'Jeddah Events',
                'organizer_phone' => '0126543210',
                'organizer_email' => 'events@jeddahfood.sa',
                'status' => 'published',
                'is_featured' => true,
                'features' => ['Free Parking', 'Kids Area', 'Live Music', 'Workshops'],
                'features_ar' => ['مواقف مجانية', 'منطقة أطفال', 'موسيقى حية', 'ورش عمل'],
                'sections' => [
                    [
                        'name' => 'Saudi Cuisine',
                        'name_ar' => 'المطبخ السعودي',
                        'description' => 'Traditional Saudi food vendors',
                        'description_ar' => 'مطاعم المأكولات السعودية التقليدية',
                        'icon' => 'utensils',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Food Booth SC-01',
                                'name_ar' => 'كشك طعام س-01',
                                'location_code' => 'SC-01',
                                'area_sqm' => 12,
                                'price_per_day' => 500,
                                'price_total' => 5500,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Water Supply', 'Cleaning'],
                            ],
                            [
                                'name' => 'Food Booth SC-02',
                                'name_ar' => 'كشك طعام س-02',
                                'location_code' => 'SC-02',
                                'area_sqm' => 12,
                                'price_per_day' => 500,
                                'price_total' => 5500,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Water Supply', 'Cleaning'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'International Cuisine',
                        'name_ar' => 'المطبخ العالمي',
                        'description' => 'International food vendors from around the world',
                        'description_ar' => 'مطاعم مأكولات عالمية من جميع أنحاء العالم',
                        'icon' => 'globe',
                        'sort_order' => 2,
                        'spaces' => [
                            [
                                'name' => 'Food Shop IN-01',
                                'name_ar' => 'محل طعام ع-01',
                                'location_code' => 'IN-01',
                                'area_sqm' => 20,
                                'price_per_day' => 700,
                                'price_total' => 7700,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'shop',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Water Supply', 'Cleaning', 'Furniture'],
                            ],
                            [
                                'name' => 'Outdoor Area IN-02',
                                'name_ar' => 'منطقة خارجية ع-02',
                                'location_code' => 'IN-02',
                                'area_sqm' => 40,
                                'price_per_day' => 1000,
                                'price_total' => 11000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'outdoor',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Water Supply'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 3: Real Estate Exhibition ============
            [
                'name' => 'Saudi Real Estate Show',
                'name_ar' => 'معرض العقارات السعودي',
                'description' => 'Premier real estate exhibition showcasing the latest projects, investment opportunities, and property developments across Saudi Arabia.',
                'description_ar' => 'المعرض العقاري الأول الذي يعرض أحدث المشاريع وفرص الاستثمار والتطورات العقارية في جميع أنحاء المملكة.',
                'category_id' => $realEstateCat->id,
                'city_id' => $riyadh->id,
                'address' => 'Riyadh International Convention Center',
                'address_ar' => 'مركز الرياض الدولي للمؤتمرات والمعارض',
                'latitude' => 24.6900,
                'longitude' => 46.6850,
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(20),
                'opening_time' => '10:00',
                'closing_time' => '21:00',
                'organizer_name' => 'Saudi Real Estate Co.',
                'organizer_phone' => '0119876543',
                'status' => 'published',
                'is_featured' => false,
                'features' => ['VIP Parking', 'Meeting Rooms', 'Business Center'],
                'features_ar' => ['مواقف VIP', 'غرف اجتماعات', 'مركز أعمال'],
                'sections' => [
                    [
                        'name' => 'Residential Projects',
                        'name_ar' => 'المشاريع السكنية',
                        'icon' => 'home',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Premium Booth RP-01',
                                'name_ar' => 'كشك مميز س-01',
                                'location_code' => 'RP-01',
                                'area_sqm' => 40,
                                'price_per_day' => 3000,
                                'price_total' => 18000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture', 'Signage & Branding'],
                            ],
                            [
                                'name' => 'Standard Booth RP-02',
                                'name_ar' => 'كشك عادي س-02',
                                'location_code' => 'RP-02',
                                'area_sqm' => 20,
                                'price_per_day' => 1500,
                                'price_total' => 9000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Commercial Projects',
                        'name_ar' => 'المشاريع التجارية',
                        'icon' => 'building',
                        'sort_order' => 2,
                        'spaces' => [
                            [
                                'name' => 'Office Space CP-01',
                                'name_ar' => 'مكتب ت-01',
                                'location_code' => 'CP-01',
                                'area_sqm' => 35,
                                'price_per_day' => 2500,
                                'price_total' => 15000,
                                'status' => 'available',
                                'floor_number' => 2,
                                'space_type' => 'office',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning', 'Furniture', 'Security'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 4: Fashion & Beauty (Ongoing) ============
            [
                'name' => 'Glamour Fashion Week',
                'name_ar' => 'أسبوع الموضة جلامور',
                'description' => 'Annual fashion and beauty exhibition featuring local and international designers, cosmetics brands, and beauty services.',
                'description_ar' => 'معرض الأزياء والجمال السنوي الذي يضم مصممين محليين ودوليين وعلامات تجارية للمستحضرات وخدمات التجميل.',
                'category_id' => $fashionCat->id,
                'city_id' => $jeddah->id,
                'address' => 'Red Sea Mall Exhibition Hall',
                'address_ar' => 'قاعة معارض الرد سي مول',
                'latitude' => 21.6200,
                'longitude' => 39.1100,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(5),
                'opening_time' => '10:00',
                'closing_time' => '23:00',
                'organizer_name' => 'Glamour Events',
                'organizer_phone' => '0561234567',
                'status' => 'published',
                'is_featured' => true,
                'features' => ['Fashion Shows', 'Beauty Workshops', 'VIP Lounge'],
                'features_ar' => ['عروض أزياء', 'ورش تجميل', 'صالة كبار الزوار'],
                'sections' => [
                    [
                        'name' => 'Fashion Designers',
                        'name_ar' => 'مصممو الأزياء',
                        'icon' => 'shirt',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Designer Booth FD-01',
                                'name_ar' => 'كشك مصمم أ-01',
                                'location_code' => 'FD-01',
                                'area_sqm' => 18,
                                'price_per_day' => 1000,
                                'price_total' => 7000,
                                'status' => 'rented',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Furniture', 'Signage & Branding'],
                            ],
                            [
                                'name' => 'Designer Booth FD-02',
                                'name_ar' => 'كشك مصمم أ-02',
                                'location_code' => 'FD-02',
                                'area_sqm' => 18,
                                'price_per_day' => 1000,
                                'price_total' => 7000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Furniture'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Beauty & Cosmetics',
                        'name_ar' => 'الجمال والمستحضرات',
                        'icon' => 'sparkles',
                        'sort_order' => 2,
                        'spaces' => [
                            [
                                'name' => 'Beauty Shop BC-01',
                                'name_ar' => 'محل تجميل ج-01',
                                'location_code' => 'BC-01',
                                'area_sqm' => 25,
                                'price_per_day' => 1200,
                                'price_total' => 8400,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'shop',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Water Supply', 'Furniture'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 5: Automotive Exhibition (Upcoming) ============
            [
                'name' => 'Saudi Auto Show 2025',
                'name_ar' => 'معرض السيارات السعودي 2025',
                'description' => 'The ultimate automotive exhibition showcasing the latest models, electric vehicles, and classic cars from world-renowned manufacturers.',
                'description_ar' => 'معرض السيارات الأول من نوعه في المملكة يعرض أحدث الموديلات والسيارات الكهربائية والسيارات الكلاسيكية من أشهر المصنعين.',
                'category_id' => $autoCat->id,
                'city_id' => $dammam->id,
                'address' => 'Dhahran Expo Center',
                'address_ar' => 'مركز معارض الظهران',
                'latitude' => 26.3000,
                'longitude' => 50.1400,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(40),
                'opening_time' => '10:00',
                'closing_time' => '22:00',
                'organizer_name' => 'Eastern Events',
                'organizer_phone' => '0138765432',
                'status' => 'published',
                'is_featured' => false,
                'features' => ['Test Drive Area', 'VIP Parking', 'Kids Zone', 'Food Court'],
                'features_ar' => ['منطقة تجربة القيادة', 'مواقف VIP', 'منطقة أطفال', 'منطقة طعام'],
                'sections' => [
                    [
                        'name' => 'New Models',
                        'name_ar' => 'الموديلات الجديدة',
                        'icon' => 'car',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Showroom NM-01',
                                'name_ar' => 'صالة عرض م-01',
                                'location_code' => 'NM-01',
                                'area_sqm' => 80,
                                'price_per_day' => 4000,
                                'price_total' => 44000,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'hall',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Security', 'Cleaning', 'Signage & Branding'],
                            ],
                            [
                                'name' => 'Booth NM-02',
                                'name_ar' => 'كشك م-02',
                                'location_code' => 'NM-02',
                                'area_sqm' => 30,
                                'price_per_day' => 1500,
                                'price_total' => 16500,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Furniture'],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Electric Vehicles',
                        'name_ar' => 'السيارات الكهربائية',
                        'icon' => 'bolt',
                        'sort_order' => 2,
                        'spaces' => [
                            [
                                'name' => 'EV Display EV-01',
                                'name_ar' => 'عرض كهربائي ك-01',
                                'location_code' => 'EV-01',
                                'area_sqm' => 60,
                                'price_per_day' => 3500,
                                'price_total' => 38500,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'hall',
                                'payment_system' => 'installment',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Air Conditioning', 'Security', 'Signage & Branding'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 6: Health Exhibition ============
            [
                'name' => 'Saudi Health Expo',
                'name_ar' => 'معرض الصحة السعودي',
                'description' => 'Healthcare and medical technology exhibition featuring hospitals, pharmaceutical companies, and medical device manufacturers.',
                'description_ar' => 'معرض الرعاية الصحية والتقنية الطبية يضم مستشفيات وشركات أدوية ومصنعي أجهزة طبية.',
                'category_id' => $healthCat->id,
                'city_id' => $riyadh->id,
                'address' => 'King Fahad Cultural Center',
                'address_ar' => 'مركز الملك فهد الثقافي',
                'latitude' => 24.7100,
                'longitude' => 46.7000,
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(45),
                'opening_time' => '09:00',
                'closing_time' => '18:00',
                'organizer_name' => 'Health Events SA',
                'organizer_phone' => '0112223344',
                'status' => 'published',
                'is_featured' => false,
                'sections' => [
                    [
                        'name' => 'Medical Devices',
                        'name_ar' => 'الأجهزة الطبية',
                        'icon' => 'heart-pulse',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Medical Booth MD-01',
                                'name_ar' => 'كشك طبي ط-01',
                                'location_code' => 'MD-01',
                                'area_sqm' => 25,
                                'price_per_day' => 1800,
                                'price_total' => 10800,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi', 'Air Conditioning'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 7: Draft Event (not published) ============
            [
                'name' => 'Education Summit 2025',
                'name_ar' => 'قمة التعليم 2025',
                'description' => 'Annual education and training summit featuring universities, training centers, and EdTech companies.',
                'description_ar' => 'قمة التعليم والتدريب السنوية تضم جامعات ومراكز تدريب وشركات تقنية التعليم.',
                'category_id' => $eduCat->id,
                'city_id' => $khobar ? $khobar->id : $riyadh->id,
                'address' => 'Khobar Convention Center',
                'address_ar' => 'مركز مؤتمرات الخبر',
                'latitude' => 26.2100,
                'longitude' => 50.2000,
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(63),
                'opening_time' => '08:00',
                'closing_time' => '17:00',
                'organizer_name' => 'EduTech SA',
                'organizer_phone' => '0139876543',
                'status' => 'draft',
                'is_featured' => false,
                'sections' => [
                    [
                        'name' => 'Universities',
                        'name_ar' => 'الجامعات',
                        'icon' => 'graduation-cap',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Booth UNI-01',
                                'name_ar' => 'كشك ج-01',
                                'location_code' => 'UNI-01',
                                'area_sqm' => 20,
                                'price_per_day' => 800,
                                'price_total' => 2400,
                                'status' => 'available',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Internet & Wi-Fi'],
                            ],
                        ],
                    ],
                ],
            ],

            // ============ Event 8: Ended Trade Exhibition ============
            [
                'name' => 'Riyadh Trade Fair 2024',
                'name_ar' => 'معرض الرياض التجاري 2024',
                'description' => 'The annual Riyadh trade fair bringing together local and international businesses.',
                'description_ar' => 'معرض الرياض التجاري السنوي الذي يجمع بين الشركات المحلية والدولية.',
                'category_id' => $tradeCat->id,
                'city_id' => $riyadh->id,
                'address' => 'Riyadh Exhibition Center',
                'address_ar' => 'مركز معارض الرياض',
                'latitude' => 24.7300,
                'longitude' => 46.7100,
                'start_date' => now()->subDays(45),
                'end_date' => now()->subDays(30),
                'opening_time' => '09:00',
                'closing_time' => '21:00',
                'organizer_name' => 'Trade Events SA',
                'organizer_phone' => '0114455667',
                'status' => 'ended',
                'is_featured' => false,
                'sections' => [
                    [
                        'name' => 'General Trade',
                        'name_ar' => 'التجارة العامة',
                        'icon' => 'store',
                        'sort_order' => 1,
                        'spaces' => [
                            [
                                'name' => 'Booth GT-01',
                                'name_ar' => 'كشك ت-01',
                                'location_code' => 'GT-01',
                                'area_sqm' => 15,
                                'price_per_day' => 400,
                                'price_total' => 6000,
                                'status' => 'unavailable',
                                'floor_number' => 1,
                                'space_type' => 'booth',
                                'payment_system' => 'full',
                                'rental_duration' => 'full_event',
                                'services' => ['Electricity', 'Furniture'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
