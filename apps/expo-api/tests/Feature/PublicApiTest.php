<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    // ==================== HEALTH CHECK ====================

    public function test_health_check(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertOk()->assertJsonPath('status', 'ok');
    }

    // ==================== CATEGORIES ====================

    public function test_can_list_categories(): void
    {
        $this->createCategory(['name' => 'Tech', 'name_ar' => 'تقنية']);
        $this->createCategory(['name' => 'Food', 'name_ar' => 'طعام', 'sort_order' => 2]);

        $response = $this->getJson('/api/categories');
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_can_show_category(): void
    {
        $category = $this->createCategory();

        $response = $this->getJson("/api/categories/{$category->id}");
        $response->assertOk()->assertJsonPath('success', true);
    }

    // ==================== CITIES ====================

    public function test_can_list_cities(): void
    {
        $this->createCity(['name' => 'Riyadh', 'name_ar' => 'الرياض']);
        $this->createCity(['name' => 'Jeddah', 'name_ar' => 'جدة', 'sort_order' => 2]);

        $response = $this->getJson('/api/cities');
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_can_show_city(): void
    {
        $city = $this->createCity();

        $response = $this->getJson("/api/cities/{$city->id}");
        $response->assertOk()->assertJsonPath('success', true);
    }

    // ==================== EVENTS ====================

    public function test_can_list_events(): void
    {
        $this->createEvent(['status' => 'published']);

        $response = $this->getJson('/api/events');
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_events_list_only_shows_published(): void
    {
        $category = $this->createCategory();
        $city = $this->createCity();
        $this->createEvent(['status' => 'published', 'category' => $category, 'city' => $city]);
        $this->createEvent(['status' => 'draft', 'name' => 'Draft Event', 'category' => $category, 'city' => $city]);

        $response = $this->getJson('/api/events');
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_filter_events_by_city(): void
    {
        $city1 = $this->createCity(['name' => 'Riyadh']);
        $city2 = $this->createCity(['name' => 'Jeddah', 'name_ar' => 'جدة', 'sort_order' => 2]);
        $category = $this->createCategory();

        $this->createEvent(['city' => $city1, 'category' => $category]);
        $this->createEvent(['city' => $city2, 'category' => $category, 'name' => 'Jeddah Event']);

        $response = $this->getJson("/api/events?city_id={$city1->id}");
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_get_featured_events(): void
    {
        $category = $this->createCategory();
        $city = $this->createCity();
        $this->createEvent(['is_featured' => true, 'category' => $category, 'city' => $city]);
        $this->createEvent(['is_featured' => false, 'name' => 'Regular', 'category' => $category, 'city' => $city]);

        $response = $this->getJson('/api/events/featured');
        $response->assertOk();
    }

    public function test_can_show_event(): void
    {
        $event = $this->createEvent();

        $response = $this->getJson("/api/events/{$event->id}");
        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_cannot_show_draft_event(): void
    {
        $event = $this->createEvent(['status' => 'draft']);

        $response = $this->getJson("/api/events/{$event->id}");
        $response->assertNotFound();
    }

    public function test_can_get_event_sections(): void
    {
        $event = $this->createEvent();
        $this->createSection($event, ['name' => 'Section A']);
        $this->createSection($event, ['name' => 'Section B', 'name_ar' => 'القسم ب', 'sort_order' => 2]);

        $response = $this->getJson("/api/events/{$event->id}/sections");
        $response->assertOk();
    }

    public function test_can_get_event_spaces(): void
    {
        $event = $this->createEvent();
        $section = $this->createSection($event);
        $this->createSpace($event, $section);

        $response = $this->getJson("/api/events/{$event->id}/spaces");
        $response->assertOk();
    }

    // ==================== SPACES ====================

    public function test_can_show_space(): void
    {
        $event = $this->createEvent();
        $space = $this->createSpace($event);

        $response = $this->getJson("/api/spaces/{$space->id}");
        $response->assertOk()->assertJsonPath('success', true);
    }

    // ==================== SERVICES ====================

    public function test_can_list_services(): void
    {
        $this->createService();

        $response = $this->getJson('/api/services');
        $response->assertOk()->assertJsonPath('success', true);
    }
}
