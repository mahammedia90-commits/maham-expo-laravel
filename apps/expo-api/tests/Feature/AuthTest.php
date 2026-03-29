<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    public function test_health_check(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200)->assertJson(['status' => 'ok']);
    }

    public function test_register_validation(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);
        $response->assertStatus(422);
    }

    public function test_login_requires_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);
        $response->assertStatus(422);
    }

    public function test_otp_send_requires_phone(): void
    {
        $response = $this->postJson('/api/v1/auth/otp/send', []);
        $response->assertStatus(422);
    }

    public function test_public_events_accessible(): void
    {
        $response = $this->getJson('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data']);
    }

    public function test_public_categories_accessible(): void
    {
        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(200);
    }

    public function test_public_cities_accessible(): void
    {
        $response = $this->getJson('/api/v1/cities');
        $response->assertStatus(200);
    }

    public function test_protected_route_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/my/dashboard');
        $response->assertStatus(401);
    }

    public function test_notifications_require_auth(): void
    {
        $response = $this->getJson('/api/v1/notifications');
        $response->assertStatus(401);
    }

    public function test_profile_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/profile');
        $response->assertStatus(401);
    }
}
