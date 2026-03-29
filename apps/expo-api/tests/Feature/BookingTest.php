<?php

namespace Tests\Feature;

use Tests\TestCase;

class BookingTest extends TestCase
{
    public function test_visit_requests_require_auth(): void
    {
        $response = $this->getJson('/api/v1/visit-requests');
        $response->assertStatus(401);
    }

    public function test_rental_requests_require_auth(): void
    {
        $response = $this->getJson('/api/v1/rental-requests');
        $response->assertStatus(401);
    }

    public function test_invoices_require_auth(): void
    {
        $response = $this->getJson('/api/v1/invoices');
        $response->assertStatus(401);
    }

    public function test_support_tickets_require_auth(): void
    {
        $response = $this->getJson('/api/v1/support-tickets');
        $response->assertStatus(401);
    }
}
