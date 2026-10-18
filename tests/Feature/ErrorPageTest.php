<?php

namespace Tests\Feature;

use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_page_renders_branded_404(): void
    {
        $this->seed(SmooveHubDatabaseSeeder::class);

        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found', false)
            ->assertSee('Back to home', false)
            ->assertSee('Browse the shop', false);
    }

    public function test_unknown_api_route_returns_json_404(): void
    {
        $this->seed(SmooveHubDatabaseSeeder::class);

        $this->getJson('/api/v1/this-route-does-not-exist')
            ->assertNotFound()
            ->assertHeader('content-type', 'application/json');
    }
}
