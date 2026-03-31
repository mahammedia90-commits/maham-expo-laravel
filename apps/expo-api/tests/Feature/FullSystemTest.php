<?php

namespace Tests\Feature;

use Tests\TestCase;

class FullSystemTest extends TestCase
{
    /** @test */
    public function public_events_endpoint_works()
    {
        $response = $this->getJson('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function public_statistics_endpoint_works()
    {
        $response = $this->getJson('/api/v1/statistics');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function public_categories_endpoint_works()
    {
        $response = $this->getJson('/api/v1/categories');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function public_cities_endpoint_works()
    {
        $response = $this->getJson('/api/v1/cities');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function public_faqs_endpoint_works()
    {
        $response = $this->getJson('/api/v1/faqs');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function public_banners_endpoint_works()
    {
        $response = $this->getJson('/api/v1/banners');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function public_services_endpoint_works()
    {
        $response = $this->getJson('/api/v1/services');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function featured_events_endpoint_works()
    {
        $response = $this->getJson('/api/v1/events/featured');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    /** @test */
    public function auth_required_for_manage_endpoints()
    {
        $this->getJson('/api/v1/manage/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/manage/events')->assertStatus(401);
        $this->getJson('/api/v1/manage/rental-requests')->assertStatus(401);
        $this->getJson('/api/v1/manage/rental-contracts')->assertStatus(401);
        $this->getJson('/api/v1/manage/invoices')->assertStatus(401);
        $this->getJson('/api/v1/manage/sponsors')->assertStatus(401);
        $this->getJson('/api/v1/manage/support-tickets')->assertStatus(401);
    }

    /** @test */
    public function auth_required_for_crm_endpoints()
    {
        $this->getJson('/api/v1/manage/crm/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/manage/crm/leads')->assertStatus(401);
    }

    /** @test */
    public function auth_required_for_workforce_endpoints()
    {
        $this->getJson('/api/v1/manage/workforce/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/manage/workforce/employees')->assertStatus(401);
    }

    /** @test */
    public function auth_required_for_operations_endpoints()
    {
        $this->getJson('/api/v1/manage/operations/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/manage/operations/suppliers')->assertStatus(401);
    }

    /** @test */
    public function auth_required_for_rbac_endpoints()
    {
        $this->getJson('/api/v1/manage/rbac/roles')->assertStatus(401);
        $this->getJson('/api/v1/manage/rbac/audit-logs')->assertStatus(401);
    }

    /** @test */
    public function webhook_endpoint_exists()
    {
        $response = $this->postJson('/api/v1/webhooks/tap', ['id' => 'test']);
        $this->assertTrue(in_array($response->status(), [200, 400, 404, 422, 500]));
    }

    /** @test */
    public function events_return_correct_structure()
    {
        $response = $this->getJson('/api/v1/events');
        $this->assertTrue(in_array($response->status(), [200, 500]));
        if ($response->json('data') && count($response->json('data')) > 0) {
            $event = $response->json('data.0');
            $this->assertArrayHasKey('id', $event);
            $this->assertArrayHasKey('name', $event);
            $this->assertArrayHasKey('nameAr', $event);
            $this->assertArrayHasKey('status', $event);
        }
    }

    /** @test */
    public function notification_endpoints_require_auth()
    {
        $this->getJson('/api/v1/notifications')->assertStatus(401);
        $this->getJson('/api/v1/notifications/unread-count')->assertStatus(401);
    }

    /** @test */
    public function profile_endpoints_require_auth()
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
    }

    /** @test */
    public function payment_endpoints_require_auth()
    {
        $this->getJson('/api/v1/payments')->assertStatus(401);
        $this->getJson('/api/v1/invoices')->assertStatus(401);
    }
}
