<?php

namespace Tests\Feature;

use Tests\TestCase;

class SponsorTest extends TestCase
{
    public function test_sponsor_contracts_require_auth(): void
    {
        $response = $this->getJson('/api/v1/my/sponsor-contracts');
        $response->assertStatus(401);
    }

    public function test_sponsor_payments_require_auth(): void
    {
        $response = $this->getJson('/api/v1/my/sponsor-payments');
        $response->assertStatus(401);
    }

    public function test_sponsor_assets_require_auth(): void
    {
        $response = $this->getJson('/api/v1/my/sponsor-assets');
        $response->assertStatus(401);
    }

    public function test_sponsor_exposure_require_auth(): void
    {
        $response = $this->getJson('/api/v1/my/sponsor-exposure');
        $response->assertStatus(401);
    }
}
