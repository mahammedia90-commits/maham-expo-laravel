<?php

namespace Tests\Feature;

use Tests\TestCase;

class EventTest extends TestCase
{
    public function test_list_events(): void
    {
        $response = $this->getJson('/api/v1/events');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data']);
    }

    public function test_featured_events(): void
    {
        $response = $this->getJson('/api/v1/events/featured');
        $response->assertStatus(200);
    }

    public function test_event_sections(): void
    {
        $response = $this->getJson('/api/v1/events/1/sections');
        $response->assertStatus(200);
    }

    public function test_event_spaces(): void
    {
        $response = $this->getJson('/api/v1/events/1/spaces');
        $response->assertStatus(200);
    }

    public function test_event_sponsor_packages(): void
    {
        $response = $this->getJson('/api/v1/events/1/sponsor-packages');
        $response->assertStatus(200);
    }

    public function test_statistics_public(): void
    {
        $response = $this->getJson('/api/v1/statistics');
        $response->assertStatus(200);
    }

    public function test_banners_public(): void
    {
        $response = $this->getJson('/api/v1/banners');
        $response->assertStatus(200);
    }

    public function test_faqs_public(): void
    {
        $response = $this->getJson('/api/v1/faqs');
        $response->assertStatus(200);
    }
}
