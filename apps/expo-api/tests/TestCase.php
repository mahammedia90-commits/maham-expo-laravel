<?php

namespace Tests;

use App\Http\Middleware\AuthServiceMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckVerifiedProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\Section;
use App\Models\Space;
use App\Models\Service;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $fakeUserId = '00000000-0000-0000-0000-000000000001';

    /**
     * Mock auth middleware to bypass external auth service calls.
     */
    protected function actAsUser(array $roles = ['user'], ?string $userId = null): void
    {
        $userId = $userId ?? $this->fakeUserId;

        $this->withoutMiddleware([AuthServiceMiddleware::class, CheckRole::class, CheckVerifiedProfile::class]);

        // Add default auth data to request headers
        $this->withHeaders([
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Act as admin.
     */
    protected function actAsAdmin(?string $userId = null): void
    {
        $this->actAsUser(['admin', 'super-admin'], $userId);
    }

    /**
     * Get default auth merge data.
     */
    protected function authData(array $roles = ['user'], ?string $userId = null): array
    {
        $userId = $userId ?? $this->fakeUserId;
        return [
            'auth_user' => [
                'id' => $userId,
                'name' => 'Test User',
                'email' => 'test@user.com',
                'status' => 'active',
                'roles' => $roles,
                'permissions' => [],
            ],
            'auth_user_id' => $userId,
            'auth_user_roles' => $roles,
            'auth_user_permissions' => [],
        ];
    }

    protected function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Tech',
            'name_ar' => 'تقنية',
            'icon' => 'cpu',
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));
    }

    protected function createCity(array $overrides = []): City
    {
        return City::create(array_merge([
            'name' => 'Riyadh',
            'name_ar' => 'الرياض',
            'region' => 'Riyadh Region',
            'region_ar' => 'منطقة الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));
    }

    protected function createEvent(array $overrides = []): Event
    {
        $category = $overrides['category'] ?? $this->createCategory();
        $city = $overrides['city'] ?? $this->createCity();
        unset($overrides['category'], $overrides['city']);

        return Event::create(array_merge([
            'name' => 'Test Event',
            'name_ar' => 'فعالية تجريبية',
            'description' => 'A test event',
            'description_ar' => 'فعالية اختبارية',
            'category_id' => $category->id,
            'city_id' => $city->id,
            'address' => 'Riyadh Front',
            'address_ar' => 'واجهة الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(35),
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'organizer_name' => 'Maham',
            'organizer_phone' => '0500000000',
            'status' => 'published',
            'is_featured' => false,
        ], $overrides));
    }

    protected function createSection(Event $event, array $overrides = []): Section
    {
        return Section::create(array_merge([
            'event_id' => $event->id,
            'name' => 'Section A',
            'name_ar' => 'القسم أ',
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));
    }

    protected function createSpace(Event $event, ?Section $section = null, array $overrides = []): Space
    {
        $section = $section ?? $this->createSection($event);

        return Space::create(array_merge([
            'event_id' => $event->id,
            'section_id' => $section->id,
            'name' => 'Booth A-01',
            'name_ar' => 'كشك أ-01',
            'location_code' => 'A-01',
            'area_sqm' => 25,
            'price_per_day' => 500,
            'price_total' => 15000,
            'status' => 'available',
            'floor_number' => 1,
            'space_type' => 'booth',
            'payment_system' => 'full',
            'rental_duration' => 'full_event',
        ], $overrides));
    }

    protected function createService(array $overrides = []): Service
    {
        return Service::create(array_merge([
            'name' => 'Electricity',
            'name_ar' => 'كهرباء',
            'icon' => 'bolt',
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));
    }
}
