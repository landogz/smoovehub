<?php

namespace Tests\Feature;

use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    public function test_maintenance_route_redirects_home_when_disabled(): void
    {
        config(['maintenance.enabled' => false]);

        $this->get('/maintenance')->assertRedirect('/');
    }

    public function test_home_returns_503_when_maintenance_is_enabled(): void
    {
        config([
            'maintenance.enabled' => true,
            'maintenance.message' => 'Closed for a tune-up.',
            'maintenance.retry_after' => 120,
        ]);

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', '120')
            ->assertSee('We’ll be back soon', false)
            ->assertSee('Closed for a tune-up.');
    }

    public function test_client_pages_stay_open_during_maintenance(): void
    {
        config(['maintenance.enabled' => true]);

        $this->get('/client-payment')->assertOk();
        $this->get('/project-progress')->assertOk();
    }

    public function test_api_returns_json_503_during_maintenance(): void
    {
        config([
            'maintenance.enabled' => true,
            'maintenance.message' => 'Closed for a tune-up.',
        ]);

        $this->getJson('/api/v1/catalog/products')
            ->assertStatus(503)
            ->assertJson([
                'status' => false,
                'message' => 'Closed for a tune-up.',
            ]);
    }
}
