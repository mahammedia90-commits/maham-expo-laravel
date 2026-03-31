<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\VisitRequest;
use App\Models\RentalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedApiTest extends TestCase
{
    

    protected function setUp(): void
    {
        parent::setUp();
        $this->actAsUser();
    }

    // ==================== BUSINESS PROFILE ====================

    public function test_user_can_create_profile(): void
    {
        $response = $this->postJson('/api/profile', array_merge($this->authData(), [
            'company_name' => 'My Company',
            'company_name_ar' => 'شركتي',
            'commercial_registration_number' => '1234567890',
            'contact_phone' => '0500000000',
            'contact_email' => 'info@company.com',
            'business_type' => 'investor',
        ]));

        $response->assertStatus(201);
    }

    public function test_user_can_get_profile(): void
    {
        BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'My Company',
            'company_name_ar' => 'شركتي',
            'commercial_registration_number' => '1234567890',
            'contact_phone' => '0500000000',
            'contact_email' => 'info@company.com',
            'business_type' => 'investor',
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/profile?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_update_profile(): void
    {
        BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'My Company',
            'company_name_ar' => 'شركتي',
            'commercial_registration_number' => '1234567890',
            'contact_phone' => '0500000000',
            'contact_email' => 'info@company.com',
            'business_type' => 'investor',
            'status' => 'pending',
        ]);

        $response = $this->putJson('/api/profile', array_merge($this->authData(), [
            'company_name' => 'Updated Company',
        ]));

        $response->assertOk();
    }

    // ==================== FAVORITES ====================

    public function test_user_can_toggle_favorite(): void
    {
        $event = $this->createEvent();

        $response = $this->postJson('/api/favorites', array_merge($this->authData(), [
            'type' => 'event',
            'id' => $event->id,
        ]));

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_user_can_list_favorites(): void
    {
        $response = $this->getJson('/api/favorites?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_remove_favorite(): void
    {
        $event = $this->createEvent();
        $favorite = Favorite::create([
            'user_id' => $this->fakeUserId,
            'favoritable_type' => 'App\Models\Event',
            'favoritable_id' => $event->id,
        ]);

        $response = $this->deleteJson("/api/favorites/{$favorite->id}?" . http_build_query($this->authData()));
        $response->assertOk();
    }

    // ==================== NOTIFICATIONS ====================

    public function test_user_can_list_notifications(): void
    {
        $response = $this->getJson('/api/notifications?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_get_unread_count(): void
    {
        $response = $this->getJson('/api/notifications/unread-count?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    // ==================== VISIT REQUESTS ====================

    public function test_user_can_create_visit_request(): void
    {
        $event = $this->createEvent([
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(30),
        ]);

        $response = $this->postJson('/api/visit-requests', array_merge($this->authData(), [
            'event_id' => $event->id,
            'visit_date' => now()->addDays(5)->toDateString(),
            'visitors_count' => 2,
            'contact_phone' => '0500000000',
        ]));

        $response->assertStatus(201);
    }

    public function test_user_can_list_visit_requests(): void
    {
        $response = $this->getJson('/api/visit-requests?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_show_visit_request(): void
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

        $response = $this->getJson("/api/visit-requests/{$vr->id}?" . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_update_visit_request(): void
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

        $response = $this->putJson("/api/visit-requests/{$vr->id}", array_merge($this->authData(), [
            'visitors_count' => 3,
        ]));

        $response->assertOk();
    }

    public function test_user_can_cancel_visit_request(): void
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

        $response = $this->deleteJson("/api/visit-requests/{$vr->id}?" . http_build_query($this->authData()));
        $response->assertOk();
    }

    // ==================== RENTAL REQUESTS ====================

    public function test_user_can_create_rental_request(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'My Co',
            'company_name_ar' => 'شركتي',
            'commercial_registration_number' => '1234567890',
            'contact_phone' => '0500000000',
            'contact_email' => 'info@co.com',
            'business_type' => 'investor',
            'status' => 'approved',
        ]);

        $response = $this->postJson('/api/rental-requests', array_merge($this->authData(), [
            'space_id' => $space->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(35)->toDateString(),
        ]));

        $response->assertStatus(201);
    }

    public function test_user_can_list_rental_requests(): void
    {
        $response = $this->getJson('/api/rental-requests?' . http_build_query($this->authData()));
        $response->assertOk();
    }

    public function test_user_can_show_rental_request(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        $profile = BusinessProfile::create([
            'user_id' => $this->fakeUserId,
            'company_name' => 'My Co',
            'company_name_ar' => 'شركتي',
            'commercial_registration_number' => '1234567890',
            'contact_phone' => '0500000000',
            'contact_email' => 'info@co.com',
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

        $response = $this->getJson("/api/rental-requests/{$rr->id}?" . http_build_query($this->authData()));
        $response->assertOk();
    }
}
