<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\Order\Order;
use Database\Seeders\CatalogProductSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CatalogOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_products_come_from_the_database(): void
    {
        $this->seed([SmooveHubDatabaseSeeder::class, CatalogProductSeeder::class]);

        $response = $this->getJson('/api/v1/catalog/products')->assertOk();
        $sku = $response->json('data.products.0.sku');

        $this->assertTrue($response->json('status'));
        $this->assertIsString($sku);
        $this->assertStringStartsWith('SH-', $sku);
    }

    public function test_checkout_quote_uses_crs_service_rate_brackets(): void
    {
        Http::fake([
            'https://crs.test/api/delivery-quote' => Http::response(['status' => false], 404),
            'https://crs.test/api/servicerates' => Http::response([
                ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
                ['service_type' => 'delivery', 'value_range' => '$750 - Less than $1500', 'rate' => 199.99],
                ['service_type' => 'delivery_mileage', 'rate' => 1.75],
            ]),
        ]);

        $this->seed([SmooveHubDatabaseSeeder::class, CatalogProductSeeder::class]);

        $product = Product::query()->where('sku', 'SH-RES-012')->firstOrFail();

        $this->postJson('/api/v1/orders/quote', [
            'product_id' => $product->uuid,
            'delivery_type' => 'CURBSIDE',
        ])
            ->assertOk()
            ->assertJsonPath('data.quote.delivery_fee', 179.99)
            ->assertJsonPath('data.quote.crs.value_range', 'Less than $750');
    }

    public function test_trusted_purchase_creates_order_and_item(): void
    {
        Http::fake([
            'https://crs.test/api/delivery-quote' => Http::response(['status' => false], 404),
            'https://crs.test/api/servicerates' => Http::response([
                ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
            ]),
        ]);

        $this->seed([SmooveHubDatabaseSeeder::class, CatalogProductSeeder::class]);

        $product = Product::query()->where('sku', 'SH-RES-001')->firstOrFail();

        $this->postJson('/api/v1/orders/purchase', [
            'product_id' => $product->uuid,
            'delivery_address' => '123 Main St, Dallas, TX',
            'delivery_zip' => '75201',
            'delivery_type' => 'CURBSIDE',
        ])
            ->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.order.payment_status', 'unpaid');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertSame('unpaid', Order::query()->first()?->payment_status);
    }
}
