<?php

namespace Tests\Feature;

use App\Models\Order\Order;
use App\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function demoSeller(): User
    {
        $this->seed([SmooveHubDatabaseSeeder::class, DemoUsersSeeder::class]);

        return User::query()->where('email', 'demo@smoovehub.com')->firstOrFail();
    }

    public function test_seller_can_load_dashboard_overview_and_list_a_product(): void
    {
        $seller = $this->demoSeller();

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/dashboard')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.stats.products', 0);

        $created = $this->actingAs($seller)
            ->postJson('/api/v1/seller/products', [
                'name' => 'Demo Oak Side Table',
                'base_price' => 100,
                'condition' => 'BRAND_NEW',
                'category' => 'RESIDENTIAL',
                'quantity' => 3,
            ])
            ->assertCreated()
            ->assertJsonPath('data.product.price', 110)
            ->json('data.product');

        $this->actingAs($seller)
            ->putJson('/api/v1/seller/products/'.$created['id'], [
                'name' => 'Demo Oak Side Table XL',
                'base_price' => 200,
                'condition' => 'THRIFT',
                'category' => 'RESIDENTIAL',
                'quantity' => 2,
                'description' => 'Updated listing',
            ])
            ->assertOk()
            ->assertJsonPath('data.product.name', 'Demo Oak Side Table XL')
            ->assertJsonPath('data.product.original', 200);

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/products')
            ->assertOk()
            ->assertJsonPath('data.products.0.name', 'Demo Oak Side Table XL');

        $this->actingAs($seller)
            ->putJson('/api/v1/seller/settings', [
                'store_name' => 'Demo Seller Studio',
                'city' => 'Dallas',
                'state' => 'TX',
                'phone' => '2145550199',
                'pickup_zip' => '75201',
                'street' => '100 Warehouse Way',
                'accepts_onsite_visits' => true,
                'policies' => [
                    'returns' => '7-day returns',
                    'shipping' => 'CRS delivery',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.settings.store_name', 'Demo Seller Studio')
            ->assertJsonPath('data.settings.phone', '2145550199')
            ->assertJsonPath('data.settings.pickup_zip', '75201')
            ->assertJsonPath('data.settings.policies.returns', '7-day returns');
    }

    public function test_seller_can_upload_store_logo(): void
    {
        Storage::fake('public');
        $seller = $this->demoSeller();

        $this->actingAs($seller)
            ->post('/api/v1/seller/logo', [
                'logo' => UploadedFile::fake()->image('store.jpg', 200, 200),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/settings')
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->assertNotEmpty($seller->fresh()->sellerProfile?->logo_path);
    }

    public function test_seller_can_update_order_status_and_manage_payout_banks(): void
    {
        $seller = $this->demoSeller();
        $profile = $seller->sellerProfile;

        $order = Order::query()->create([
            'order_number' => 'SH-TEST-1001',
            'seller_profile_id' => $profile->id,
            'order_type' => 'marketplace',
            'fulfillment_source' => 'seller',
            'subtotal' => 110,
            'total' => 110,
            'status' => 'processing',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($seller)
            ->patchJson('/api/v1/seller/orders/'.$order->uuid, [
                'status' => 'shipped',
            ])
            ->assertOk()
            ->assertJsonPath('data.order.status', 'shipped');

        $this->actingAs($seller)
            ->postJson('/api/v1/seller/payout-accounts', [
                'bank_name' => 'Chase',
                'holder_name' => 'Demo Seller',
                'account_type' => 'checking',
                'last4' => '4242',
            ])
            ->assertCreated()
            ->assertJsonPath('data.bank.last4', '4242')
            ->assertJsonMissing(['account_number']);

        $this->actingAs($seller)
            ->postJson('/api/v1/seller/payout-accounts', [
                'bank_name' => 'Chase',
                'holder_name' => 'Demo Seller',
                'account_type' => 'checking',
                'last4' => '1111',
                'account_number' => '123456789',
                'routing_number' => '021000021',
            ])
            ->assertUnprocessable();
    }

    public function test_seller_can_crud_store_vouchers(): void
    {
        $seller = $this->demoSeller();

        $created = $this->actingAs($seller)
            ->postJson('/api/v1/seller/vouchers', [
                'code' => 'store10',
                'title' => 'Ten off store items',
                'type' => 'percent',
                'value' => 10,
                'min_spend' => 50,
            ])
            ->assertCreated()
            ->assertJsonPath('data.voucher.code', 'STORE10')
            ->json('data.voucher');

        $this->actingAs($seller)
            ->putJson('/api/v1/seller/vouchers/'.$created['id'], [
                'code' => 'STORE15',
                'title' => 'Fifteen off',
                'type' => 'percent',
                'value' => 15,
                'min_spend' => 75,
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('data.voucher.value', 15);

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/vouchers')
            ->assertOk()
            ->assertJsonPath('data.vouchers.0.code', 'STORE15');

        $this->actingAs($seller)
            ->deleteJson('/api/v1/seller/vouchers/'.$created['id'])
            ->assertOk();

        $this->actingAs($seller)
            ->getJson('/api/v1/seller/vouchers')
            ->assertOk()
            ->assertJsonPath('data.vouchers', []);
    }

    public function test_guest_is_redirected_from_dashboard_page(): void
    {
        $this->get('/dashboard')->assertRedirect(route('site.home'));
        $this->get('/dashboard#overview')->assertRedirect(route('site.home'));
    }

    public function test_member_is_redirected_from_dashboard_page(): void
    {
        $this->seed([SmooveHubDatabaseSeeder::class, DemoUsersSeeder::class]);
        $member = User::query()->where('email', 'member@smoovehub.com')->firstOrFail();

        $this->actingAs($member)
            ->get('/dashboard')
            ->assertRedirect(route('site.home'));
    }

    public function test_seller_can_open_dashboard_page(): void
    {
        $this->actingAs($this->demoSeller())
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_guest_cannot_open_seller_apis(): void
    {
        $this->getJson('/api/v1/seller/dashboard')->assertUnauthorized();
        $this->putJson('/api/v1/seller/products/not-a-real-id', [
            'name' => 'X',
            'base_price' => 10,
            'condition' => 'BRAND_NEW',
            'category' => 'RESIDENTIAL',
            'quantity' => 1,
        ])->assertUnauthorized();
        $this->postJson('/api/v1/seller/payout-accounts', [
            'bank_name' => 'Chase',
            'holder_name' => 'Guest',
            'account_type' => 'checking',
            'last4' => '0000',
        ])->assertUnauthorized();
        $this->getJson('/api/v1/seller/vouchers')->assertUnauthorized();
    }
}
