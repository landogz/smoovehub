<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\User;
use Database\Seeders\CatalogProductSeeder;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MemberAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_sees_orders_they_placed_on_the_shop(): void
    {
        Http::fake([
            'https://crs.test/api/delivery-quote' => Http::response(['status' => false], 404),
            'https://crs.test/api/servicerates' => Http::response([
                ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
            ]),
        ]);

        $this->seed([SmooveHubDatabaseSeeder::class, CatalogProductSeeder::class, DemoUsersSeeder::class]);

        $member = User::query()->where('email', 'member@smoovehub.com')->firstOrFail();
        $product = Product::query()->where('sku', 'SH-RES-001')->firstOrFail();

        $this->actingAs($member)
            ->postJson('/api/v1/orders/purchase', [
                'product_id' => $product->uuid,
                'delivery_address' => '123 Main St, Dallas, TX',
                'delivery_zip' => '75201',
                'delivery_type' => 'CURBSIDE',
            ])
            ->assertCreated();

        $this->actingAs($member)
            ->getJson('/api/v1/member/account')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.stats.orders', 1)
            ->assertJsonPath('data.member.email', 'member@smoovehub.com')
            ->assertJsonPath('data.orders.0.payment_status', 'unpaid');
    }

    public function test_guest_cannot_open_member_account_api(): void
    {
        $this->getJson('/api/v1/member/account')
            ->assertUnauthorized();
    }

    public function test_account_page_is_public_shell(): void
    {
        $this->get('/account')->assertOk();
    }
}
