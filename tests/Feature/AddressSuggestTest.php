<?php

namespace Tests\Feature;

use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddressSuggestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SmooveHubDatabaseSeeder::class);
    }

    public function test_empty_query_is_rejected(): void
    {
        $this->getJson('/api/v1/site/address-suggest')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_short_query_is_rejected(): void
    {
        $this->getJson('/api/v1/site/address-suggest?q=7')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_photon_results_are_mapped_for_the_frontend(): void
    {
        Http::fake([
            'photon.komoot.io/*' => Http::response([
                'features' => [
                    [
                        'geometry' => ['coordinates' => [-96.797, 32.7767]],
                        'properties' => [
                            'housenumber' => '100',
                            'street' => 'Main Street',
                            'city' => 'Dallas',
                            'state' => 'Texas',
                            'postcode' => '75201',
                            'countrycode' => 'US',
                        ],
                    ],
                    [
                        'geometry' => ['coordinates' => [-0.1276, 51.5072]],
                        'properties' => [
                            'name' => 'London',
                            'countrycode' => 'GB',
                        ],
                    ],
                    [
                        'geometry' => ['coordinates' => [-96.798, 32.785]],
                        'properties' => [
                            'name' => '75201',
                            'city' => 'Dallas',
                            'state' => 'Texas',
                            'countrycode' => 'US',
                        ],
                    ],
                ],
            ]),
        ]);

        $this->getJson('/api/v1/site/address-suggest?q=75201')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.0.label', '100 Main Street, Dallas, TX, 75201')
            ->assertJsonPath('data.0.line1', '100 Main Street')
            ->assertJsonPath('data.0.city', 'Dallas')
            ->assertJsonPath('data.0.state', 'TX')
            ->assertJsonPath('data.0.zip', '75201')
            ->assertJsonPath('data.1.zip', '75201')
            ->assertJsonPath('data.1.line1', null)
            ->assertJsonCount(2, 'data');
    }
}
