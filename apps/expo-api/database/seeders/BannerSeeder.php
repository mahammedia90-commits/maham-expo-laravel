<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Saudi Tech Expo 2025',
                'title_ar' => 'معرض السعودية التقني 2025',
                'description' => 'The largest technology exhibition in the Kingdom. Register now!',
                'description_ar' => 'أكبر معرض تقني في المملكة. سجل الآن!',
                'image' => 'banners/tech-expo-2025.jpg',
                'image_ar' => 'banners/tech-expo-2025-ar.jpg',
                'link_url' => '/events/saudi-tech-expo-2025',
                'position' => 'hero',
                'is_active' => true,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(30),
                'sort_order' => 1,
                'clicks_count' => 245,
                'impressions_count' => 12500,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'title' => 'Saudi Food Festival',
                'title_ar' => 'مهرجان الطعام السعودي',
                'description' => 'Celebrate the finest cuisines. Book your space today!',
                'description_ar' => 'احتفل بأفضل المأكولات. احجز مساحتك اليوم!',
                'image' => 'banners/food-festival-2025.jpg',
                'image_ar' => 'banners/food-festival-2025-ar.jpg',
                'link_url' => '/events/saudi-food-festival-2025',
                'position' => 'hero',
                'is_active' => true,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'sort_order' => 2,
                'clicks_count' => 180,
                'impressions_count' => 9800,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'title' => 'Become a Sponsor',
                'title_ar' => 'كن راعياً',
                'description' => 'Partner with Maham Expo and reach thousands of potential customers.',
                'description_ar' => 'شارك مع مهام إكسبو واصل لآلاف العملاء المحتملين.',
                'image' => 'banners/become-sponsor.jpg',
                'link_url' => '/sponsorship',
                'position' => 'sidebar',
                'is_active' => true,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'sort_order' => 1,
                'clicks_count' => 95,
                'impressions_count' => 5200,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'title' => 'Early Bird Discount',
                'title_ar' => 'خصم الحجز المبكر',
                'description' => 'Get 20% off on space rental when you book 30 days in advance.',
                'description_ar' => 'احصل على خصم 20% على استئجار المساحات عند الحجز قبل 30 يوماً.',
                'image' => 'banners/early-bird.jpg',
                'image_ar' => 'banners/early-bird-ar.jpg',
                'link_url' => '/promotions/early-bird',
                'position' => 'top',
                'is_active' => true,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(45),
                'sort_order' => 1,
                'clicks_count' => 320,
                'impressions_count' => 15000,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'title' => 'Glamour Fashion Week',
                'title_ar' => 'أسبوع الموضة جلامور',
                'description' => 'Experience the latest in fashion and beauty.',
                'description_ar' => 'اكتشف أحدث صيحات الموضة والجمال.',
                'image' => 'banners/fashion-week.jpg',
                'link_url' => '/events/glamour-fashion-week',
                'position' => 'hero',
                'is_active' => true,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(7),
                'sort_order' => 3,
                'clicks_count' => 150,
                'impressions_count' => 7800,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            [
                'title' => 'Download Our App',
                'title_ar' => 'حمل تطبيقنا',
                'description' => 'Manage your exhibitions on the go with our mobile app.',
                'description_ar' => 'أدر معارضك أثناء التنقل مع تطبيقنا.',
                'image' => 'banners/mobile-app.jpg',
                'image_ar' => 'banners/mobile-app-ar.jpg',
                'link_url' => '/download',
                'position' => 'footer',
                'is_active' => true,
                'sort_order' => 1,
                'clicks_count' => 420,
                'impressions_count' => 22000,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
            // Inactive banner (expired)
            [
                'title' => 'Ramadan Special Offer',
                'title_ar' => 'عرض رمضان الخاص',
                'description' => 'Special pricing during Ramadan season.',
                'description_ar' => 'أسعار خاصة خلال موسم رمضان.',
                'image' => 'banners/ramadan-offer.jpg',
                'link_url' => '/promotions/ramadan',
                'position' => 'hero',
                'is_active' => false,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(30),
                'sort_order' => 10,
                'clicks_count' => 890,
                'impressions_count' => 35000,
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ],
        ];

        if (Banner::count() > 0) {
            $this->command->info('Banners already seeded, skipping.');
            return;
        }

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        $this->command->info('Created ' . count($banners) . ' banners.');
    }
}
