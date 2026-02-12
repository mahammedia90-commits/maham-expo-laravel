<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\RentalRequest;
use App\Models\VisitRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actAsAdmin();
        Storage::fake('public');
    }

    // ==================== EVENTS ====================

    public function test_admin_can_list_events(): void
    {
        $this->createEvent();
        $response = $this->getJson('/api/admin/events');
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_admin_can_create_event(): void
    {
        $category = $this->createCategory();
        $city = $this->createCity();

        $response = $this->postJson('/api/admin/events', array_merge($this->authData(['admin']), [
            'name' => 'New Event',
            'name_ar' => 'فعالية جديدة',
            'description' => 'A new event',
            'description_ar' => 'فعالية جديدة',
            'category_id' => $category->id,
            'city_id' => $city->id,
            'address' => 'Riyadh',
            'address_ar' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(35)->toDateString(),
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'organizer_name' => 'Maham',
            'organizer_phone' => '0500000000',
            'status' => 'published',
            'images' => [UploadedFile::fake()->image('event.jpg')],
        ]));

        $response->assertStatus(201);
    }

    public function test_admin_can_show_event(): void
    {
        $event = $this->createEvent();
        $response = $this->getJson("/api/admin/events/{$event->id}");
        $response->assertOk();
    }

    public function test_admin_can_update_event(): void
    {
        $event = $this->createEvent();

        $response = $this->putJson("/api/admin/events/{$event->id}", array_merge($this->authData(['admin']), [
            'name' => 'Updated Event',
        ]));

        $response->assertOk();
    }

    public function test_admin_can_delete_event(): void
    {
        $event = $this->createEvent();

        $response = $this->deleteJson("/api/admin/events/{$event->id}");
        $response->assertOk();
    }

    // ==================== SECTIONS ====================

    public function test_admin_can_list_event_sections(): void
    {
        $event = $this->createEvent();
        $this->createSection($event);

        $response = $this->getJson("/api/admin/events/{$event->id}/sections");
        $response->assertOk();
    }

    public function test_admin_can_create_section(): void
    {
        $event = $this->createEvent();

        $response = $this->postJson("/api/admin/events/{$event->id}/sections", [
            'name' => 'New Section',
            'name_ar' => 'قسم جديد',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_can_show_section(): void
    {
        $event = $this->createEvent();
        $section = $this->createSection($event);

        $response = $this->getJson("/api/admin/sections/{$section->id}");
        $response->assertOk();
    }

    public function test_admin_can_update_section(): void
    {
        $event = $this->createEvent();
        $section = $this->createSection($event);

        $response = $this->putJson("/api/admin/sections/{$section->id}", [
            'name' => 'Updated Section',
        ]);

        $response->assertOk();
    }

    public function test_admin_can_delete_section(): void
    {
        $event = $this->createEvent();
        $section = $this->createSection($event);

        $response = $this->deleteJson("/api/admin/sections/{$section->id}");
        $response->assertOk();
    }

    // ==================== SPACES ====================

    public function test_admin_can_list_event_spaces(): void
    {
        $event = $this->createEvent();
        $this->createSpace($event);

        $response = $this->getJson("/api/admin/events/{$event->id}/spaces");
        $response->assertOk();
    }

    public function test_admin_can_create_space(): void
    {
        $event = $this->createEvent();
        $section = $this->createSection($event);

        $response = $this->postJson("/api/admin/events/{$event->id}/spaces", array_merge($this->authData(['admin']), [
            'name' => 'Booth B-01',
            'name_ar' => 'كشك ب-01',
            'location_code' => 'B-01',
            'area_sqm' => 30,
            'price_per_day' => 600,
            'price_total' => 18000,
            'status' => 'available',
            'floor_number' => 1,
            'section_id' => $section->id,
            'space_type' => 'booth',
            'payment_system' => 'full',
            'rental_duration' => 'full_event',
            'images' => [UploadedFile::fake()->image('space.jpg')],
        ]));

        $response->assertStatus(201);
    }

    public function test_admin_can_show_space(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        $response = $this->getJson("/api/admin/spaces/{$space->id}");
        $response->assertOk();
    }

    public function test_admin_can_update_space(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        $response = $this->putJson("/api/admin/spaces/{$space->id}", [
            'name' => 'Updated Space',
        ]);

        $response->assertOk();
    }

    public function test_admin_can_delete_space(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        $response = $this->deleteJson("/api/admin/spaces/{$space->id}");
        $response->assertOk();
    }

    // ==================== SERVICES ====================

    public function test_admin_can_list_services(): void
    {
        $this->createService();
        $response = $this->getJson('/api/admin/services');
        $response->assertOk();
    }

    public function test_admin_can_create_service(): void
    {
        $response = $this->postJson('/api/admin/services', [
            'name' => 'WiFi',
            'name_ar' => 'واي فاي',
            'icon' => 'wifi',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_can_show_service(): void
    {
        $service = $this->createService();
        $response = $this->getJson("/api/admin/services/{$service->id}");
        $response->assertOk();
    }

    public function test_admin_can_update_service(): void
    {
        $service = $this->createService();

        $response = $this->putJson("/api/admin/services/{$service->id}", [
            'name' => 'Updated Service',
        ]);

        $response->assertOk();
    }

    public function test_admin_can_delete_service(): void
    {
        $service = $this->createService();
        $response = $this->deleteJson("/api/admin/services/{$service->id}");
        $response->assertOk();
    }

    // ==================== VISIT REQUESTS (ADMIN) ====================

    public function test_admin_can_list_visit_requests(): void
    {
        $response = $this->getJson('/api/admin/visit-requests');
        $response->assertOk();
    }

    public function test_admin_can_show_visit_request(): void
    {
        $event = $this->createEvent();
        $vr = VisitRequest::create([
            'request_number' => 'VR-001',
            'event_id' => $event->id,
            'user_id' => $this->fakeUserId,
            'visit_date' => now()->addDays(10),
            'visitors_count' => 2,
            'contact_phone' => '0500000000',
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/admin/visit-requests/{$vr->id}");
        $response->assertOk();
    }

    public function test_admin_can_approve_visit_request(): void
    {
        $event = $this->createEvent();
        $vr = VisitRequest::create([
            'request_number' => 'VR-002',
            'event_id' => $event->id,
            'user_id' => $this->fakeUserId,
            'visit_date' => now()->addDays(10),
            'visitors_count' => 2,
            'contact_phone' => '0500000000',
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/visit-requests/{$vr->id}/approve", array_merge($this->authData(['admin']), [
            'notes' => 'Approved',
        ]));

        $response->assertOk();
    }

    public function test_admin_can_reject_visit_request(): void
    {
        $event = $this->createEvent();
        $vr = VisitRequest::create([
            'request_number' => 'VR-003',
            'event_id' => $event->id,
            'user_id' => $this->fakeUserId,
            'visit_date' => now()->addDays(10),
            'visitors_count' => 2,
            'contact_phone' => '0500000000',
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/visit-requests/{$vr->id}/reject", array_merge($this->authData(['admin']), [
            'reason' => 'No slots',
        ]));

        $response->assertOk();
    }

    // ==================== RENTAL REQUESTS (ADMIN) ====================

    public function test_admin_can_list_rental_requests(): void
    {
        $response = $this->getJson('/api/admin/rental-requests');
        $response->assertOk();
    }

    public function test_admin_can_approve_rental_request(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);
        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'Co',
            'company_name_ar' => 'شركة',
            'commercial_registration_number' => '123',
            'contact_phone' => '0500000000',
            'contact_email' => 'i@co.com',
            'business_type' => 'investor',
            'status' => 'approved',
        ]);

        $rr = RentalRequest::create([
            'request_number' => 'RR-001',
            'space_id' => $space->id,
            'user_id' => $this->fakeUserId,
            'business_profile_id' => $profile->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(35),
            'total_price' => 15000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/rental-requests/{$rr->id}/approve", array_merge($this->authData(['admin']), [
            'notes' => 'Approved',
        ]));

        $response->assertOk();
    }

    public function test_admin_can_reject_rental_request(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);
        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'Co',
            'company_name_ar' => 'شركة',
            'commercial_registration_number' => '123',
            'contact_phone' => '0500000000',
            'contact_email' => 'i@co.com',
            'business_type' => 'investor',
            'status' => 'approved',
        ]);

        $rr = RentalRequest::create([
            'request_number' => 'RR-002',
            'space_id' => $space->id,
            'user_id' => $this->fakeUserId,
            'business_profile_id' => $profile->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(35),
            'total_price' => 15000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/rental-requests/{$rr->id}/reject", array_merge($this->authData(['admin']), [
            'reason' => 'Space booked',
        ]));

        $response->assertOk();
    }

    // ==================== BUSINESS PROFILES (ADMIN) ====================

    public function test_admin_can_list_business_profiles(): void
    {
        $response = $this->getJson('/api/admin/profiles');
        $response->assertOk();
    }

    public function test_admin_can_show_business_profile(): void
    {
        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'Co',
            'company_name_ar' => 'شركة',
            'commercial_registration_number' => '123',
            'contact_phone' => '0500000000',
            'contact_email' => 'i@co.com',
            'business_type' => 'investor',
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/admin/profiles/{$profile->id}");
        $response->assertOk();
    }

    public function test_admin_can_approve_profile(): void
    {
        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'Co',
            'company_name_ar' => 'شركة',
            'commercial_registration_number' => '123',
            'contact_phone' => '0500000000',
            'contact_email' => 'i@co.com',
            'business_type' => 'investor',
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/profiles/{$profile->id}/approve", $this->authData(['admin']));
        $response->assertOk();
    }

    public function test_admin_can_reject_profile(): void
    {
        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'Co',
            'company_name_ar' => 'شركة',
            'commercial_registration_number' => '123',
            'contact_phone' => '0500000000',
            'contact_email' => 'i@co.com',
            'business_type' => 'investor',
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/admin/profiles/{$profile->id}/reject", array_merge($this->authData(['admin']), [
            'reason' => 'Incomplete docs',
        ]));

        $response->assertOk();
    }
}
