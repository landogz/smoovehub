<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\User;
use Database\Seeders\CatalogProductSeeder;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\MemberAccountSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberAccountTest extends TestCase
{
    use RefreshDatabase;

    private function seedMarketplace(): User
    {
        $this->seed([
            SmooveHubDatabaseSeeder::class,
            CatalogProductSeeder::class,
            DemoUsersSeeder::class,
            MemberAccountSeeder::class,
        ]);

        return User::query()->where('email', 'member@smoovehub.com')->firstOrFail();
    }

    public function test_member_sees_orders_they_placed_on_the_shop(): void
    {
        Http::fake([
            'https://crs.test/api/delivery-quote' => Http::response(['status' => false], 404),
            'https://crs.test/api/servicerates' => Http::response([
                ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
            ]),
        ]);

        $member = $this->seedMarketplace();
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
            ->assertJsonPath('data.member.email', 'member@smoovehub.com');

        $this->actingAs($member)
            ->getJson('/api/v1/member/orders?tab=to_pay')
            ->assertOk()
            ->assertJsonPath('data.orders.0.payment_status', 'unpaid');

        $this->actingAs($member)
            ->getJson('/api/v1/member/notifications')
            ->assertOk()
            ->assertJsonPath('data.notifications.0.title', 'Order placed');
    }

    public function test_member_can_update_profile_and_manage_address(): void
    {
        $member = $this->seedMarketplace();

        $this->actingAs($member)
            ->putJson('/api/v1/member/profile', [
                'name' => 'Rolan Member',
                'username' => 'rolan_member',
                'gender' => 'male',
            ])
            ->assertOk()
            ->assertJsonPath('data.member.username', 'rolan_member');

        $created = $this->actingAs($member)
            ->postJson('/api/v1/member/addresses', [
                'recipient_name' => 'Rolan Member',
                'phone' => '2145550199',
                'line1' => '456 Oak Ave',
                'city' => 'Plano',
                'state' => 'TX',
                'zip' => '75023',
                'is_default' => true,
            ])
            ->assertCreated()
            ->json('data.address.id');

        $this->actingAs($member)
            ->getJson('/api/v1/member/addresses')
            ->assertOk()
            ->assertJsonPath('data.addresses.0.is_default', true);

        $this->actingAs($member)
            ->deleteJson('/api/v1/member/addresses/'.$created)
            ->assertOk();
    }

    public function test_payment_methods_store_last4_only(): void
    {
        $member = $this->seedMarketplace();

        $this->actingAs($member)
            ->postJson('/api/v1/member/payment-methods', [
                'type' => 'card',
                'brand' => 'Visa',
                'last4' => '8584',
                'exp_month' => 4,
                'exp_year' => 2029,
                'holder_name' => 'Demo Member',
                'card_number' => '4111111111111111',
            ])
            ->assertUnprocessable();

        $this->actingAs($member)
            ->postJson('/api/v1/member/payment-methods', [
                'type' => 'card',
                'brand' => 'Visa',
                'last4' => '8584',
                'exp_month' => 4,
                'exp_year' => 2029,
                'holder_name' => 'Demo Member',
                'is_default' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.payment_method.last4', '8584')
            ->assertJsonMissing(['card_number']);
    }

    public function test_member_can_redeem_voucher(): void
    {
        $member = $this->seedMarketplace();

        $this->actingAs($member)
            ->postJson('/api/v1/member/vouchers/redeem', ['code' => 'SAVE10'])
            ->assertOk()
            ->assertJsonPath('data.voucher.code', 'SAVE10');
    }

    public function test_avatar_upload_accepts_small_jpeg(): void
    {
        Storage::fake('public');
        $member = $this->seedMarketplace();

        $this->actingAs($member)
            ->post('/api/v1/member/avatar', [
                'avatar' => UploadedFile::fake()->image('me.jpg', 200, 200),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', true);
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
