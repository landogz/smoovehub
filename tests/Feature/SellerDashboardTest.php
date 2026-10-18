<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_load_dashboard_overview_and_list_a_product(): void
    {
        $this->seed([SmooveHubDatabaseSeeder::class, DemoUsersSeeder::class]);

        $seller = User::query()->where('email', 'demo@smoovehub.com')->firstOrFail();

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/dashboard')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.stats.products', 0);

        $this->actingAs($seller)
            ->postJson('/api/v1/seller/products', [
                'name' => 'Demo Oak Side Table',
                'base_price' => 100,
                'condition' => 'BRAND_NEW',
                'category' => 'RESIDENTIAL',
                'quantity' => 3,
            ])
            ->assertCreated()
            ->assertJsonPath('data.product.price', 110);

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/products')
            ->assertOk()
            ->assertJsonPath('data.products.0.name', 'Demo Oak Side Table');

        $this->actingAs($seller)
            ->putJson('/api/v1/seller/settings', [
                'store_name' => 'Demo Seller Studio',
            ])
            ->assertOk()
            ->assertJsonPath('data.settings.store_name', 'Demo Seller Studio');
    }

    public function test_guest_cannot_open_seller_apis(): void
    {
        $this->getJson('/api/v1/seller/dashboard')
            ->assertUnauthorized();
    }
}
