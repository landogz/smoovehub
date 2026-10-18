<?php

namespace App\Repositories\Seller;

use App\Models\Auth\PersonalAccessToken;
use App\Models\Catalog\Product;
use App\Models\Distribution\DistributionCatalogImport;
use App\Models\Distribution\DistributionInventory;
use App\Models\Order\Order;
use App\Models\Seller\SellerProfile;
use App\Models\Tracking\OnsiteVisitAppointment;
use App\Models\User;
use App\Services\Platform\MarkupCalculationService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class SellerDashboardRepository
{
    public function __construct(
        private readonly MarkupCalculationService $markup,
        private readonly SellerPayoutAccountRepository $payoutAccounts,
    ) {}

    public function requireProfile(User $user): SellerProfile
    {
        $profile = $user->sellerProfile;

        if (! $profile) {
            abort(403, 'Seller profile not found.');
        }

        return $profile;
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(SellerProfile $profile): array
    {
        $orders = Order::query()
            ->where('seller_profile_id', $profile->id)
            ->with('items')
            ->latest()
            ->get();

        $paid = $orders->where('payment_status', 'paid');
        $revenue30 = $paid
            ->filter(fn (Order $order) => $order->paid_at && $order->paid_at->gte(now()->subDays(30)))
            ->sum(fn (Order $order) => (float) $order->subtotal);

        return [
            'store' => $this->storePayload($profile, $profile->user),
            'stats' => [
                'revenue_30d' => round((float) $revenue30, 2),
                'orders' => $orders->count(),
                'products' => (int) $profile->products()->count(),
                'rating' => (float) ($profile->rating_avg ?? 0),
            ],
            'recent_orders' => $orders->take(5)->map(fn (Order $order) => $this->orderPayload($order))->values()->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function orders(SellerProfile $profile): array
    {
        return Order::query()
            ->where('seller_profile_id', $profile->id)
            ->with('items')
            ->latest()
            ->get()
            ->map(fn (Order $order) => $this->orderPayload($order))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function payouts(SellerProfile $profile): array
    {
        $orders = Order::query()->where('seller_profile_id', $profile->id)->get();
        $paidDelivered = $orders->where('payment_status', 'paid')->where('status', 'delivered');
        $paidPending = $orders->where('payment_status', 'paid')->where('status', '!=', 'delivered');
        $unpaid = $orders->where('payment_status', '!=', 'paid');

        return [
            'available' => round((float) $paidDelivered->sum('subtotal'), 2),
            'pending' => round((float) $paidPending->sum('subtotal') + (float) $unpaid->sum('subtotal'), 2),
            'total_paid' => round((float) $paidDelivered->sum('subtotal'), 2),
            'note' => 'Payouts release Fridays after CRS delivery confirmation. Stripe Connect ships in Phase 3.',
            'banks' => $this->payoutAccounts->list($profile),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(SellerProfile $profile, User $user): array
    {
        return $this->storePayload($profile, $user);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateSettings(SellerProfile $profile, User $user, array $payload): array
    {
        $policies = is_array($profile->policies) ? $profile->policies : [];
        $incomingPolicies = is_array($payload['policies'] ?? null) ? $payload['policies'] : [];
        $policies['returns'] = $incomingPolicies['returns'] ?? ($policies['returns'] ?? null);
        $policies['shipping'] = $incomingPolicies['shipping'] ?? ($policies['shipping'] ?? null);

        $profile->update([
            'store_name' => $payload['store_name'],
            'description' => $payload['description'] ?? $profile->description,
            'city' => $payload['city'] ?? $profile->city,
            'state' => $payload['state'] ?? $profile->state,
            'business_type' => $payload['business_type'] ?? $profile->business_type,
            'accepts_onsite_visits' => (bool) ($payload['accepts_onsite_visits'] ?? $profile->accepts_onsite_visits),
            'policies' => $policies,
        ]);

        return $this->storePayload($profile->fresh(), $user);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function distCatalog(): array
    {
        return DistributionInventory::query()
            ->with('company')
            ->where('status', 'active')
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn (DistributionInventory $item) => [
                'id' => $item->uuid,
                'sku' => $item->sku,
                'name' => $item->name,
                'wholesale' => (float) $item->internal_reseller_price,
                'qty' => (int) $item->quantity,
                'supplier' => $item->company?->company_name ?? 'Distributor',
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function imports(SellerProfile $profile): array
    {
        return DistributionCatalogImport::query()
            ->with(['product', 'inventoryItem'])
            ->where('seller_profile_id', $profile->id)
            ->latest()
            ->get()
            ->map(fn (DistributionCatalogImport $import) => [
                'id' => $import->uuid,
                'status' => $import->status,
                'name' => $import->product?->name ?? $import->inventoryItem?->name ?? 'Imported SKU',
                'sku' => $import->inventoryItem?->sku,
                'wholesale_price' => (float) $import->wholesale_price,
                'resale_price' => (float) $import->resale_price,
                'imported_at' => optional($import->imported_at)->toDateString(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function onsiteAppointments(SellerProfile $profile): array
    {
        return OnsiteVisitAppointment::query()
            ->where('seller_profile_id', $profile->id)
            ->latest('scheduled_at')
            ->get()
            ->map(fn (OnsiteVisitAppointment $appt) => $this->onsitePayload($appt))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function updateOrderStatus(SellerProfile $profile, string $uuid, string $status): array
    {
        $order = Order::query()
            ->with('items')
            ->where('uuid', $uuid)
            ->where('seller_profile_id', $profile->id)
            ->first();

        if (! $order) {
            throw ValidationException::withMessages([
                'order' => ['Order was not found.'],
            ]);
        }

        $order->update([
            'status' => $status,
            'delivered_at' => $status === 'delivered' ? now() : $order->delivered_at,
        ]);

        return $this->orderPayload($order->fresh('items'));
    }

    /**
     * @return array<string, mixed>
     */
    public function importDistSku(SellerProfile $profile, string $uuid): array
    {
        $item = DistributionInventory::query()
            ->with('company')
            ->where('uuid', $uuid)
            ->where('status', 'active')
            ->first();

        if (! $item) {
            throw ValidationException::withMessages([
                'sku' => ['Distribution SKU was not found.'],
            ]);
        }

        $already = DistributionCatalogImport::query()
            ->where('seller_profile_id', $profile->id)
            ->where('distribution_inventory_id', $item->id)
            ->first();

        if ($already) {
            throw ValidationException::withMessages([
                'sku' => ['This SKU is already in your import list.'],
            ]);
        }

        $wholesale = (float) $item->internal_reseller_price;
        $condition = $item->condition ?: 'BRAND_NEW';
        $listingPrice = $this->markup->listingPrice('distribution', $condition, $wholesale);
        $slug = $this->uniqueProductSlug(Str::slug($item->name.'-'.$profile->id));

        $product = Product::query()->create([
            'seller_profile_id' => $profile->id,
            'distribution_inventory_id' => $item->id,
            'category_id' => $item->category_id,
            'sku' => $item->sku,
            'name' => $item->name,
            'slug' => $slug,
            'description' => $item->description,
            'condition' => $condition,
            'internal_price' => $wholesale,
            'listing_price' => $listingPrice,
            'compare_at_price' => $listingPrice,
            'quantity' => max(1, (int) $item->quantity),
            'ownership_type' => 'distribution',
            'status' => 'active',
            'seo_meta' => ['icon' => '🏭', 'imported' => true],
        ]);

        $profile->increment('product_count');

        $import = DistributionCatalogImport::query()->create([
            'seller_profile_id' => $profile->id,
            'distribution_company_id' => $item->distribution_company_id,
            'distribution_inventory_id' => $item->id,
            'product_id' => $product->id,
            'wholesale_price' => $wholesale,
            'resale_price' => $listingPrice,
            'status' => 'imported',
            'imported_at' => now(),
        ]);

        return [
            'import' => [
                'id' => $import->uuid,
                'status' => $import->status,
                'name' => $product->name,
                'sku' => $item->sku,
                'wholesale_price' => $wholesale,
                'resale_price' => $listingPrice,
                'imported_at' => $import->imported_at?->toDateString(),
            ],
            'product' => [
                'id' => $product->uuid,
                'name' => $product->name,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateOnsite(SellerProfile $profile, string $uuid, array $payload): array
    {
        $appt = OnsiteVisitAppointment::query()
            ->where('uuid', $uuid)
            ->where('seller_profile_id', $profile->id)
            ->first();

        if (! $appt) {
            throw ValidationException::withMessages([
                'appointment' => ['Visit request was not found.'],
            ]);
        }

        $appt->update([
            'status' => $payload['status'],
            'seller_notes' => $payload['seller_notes'] ?? $appt->seller_notes,
        ]);

        return $this->onsitePayload($appt->fresh());
    }

    /**
     * @return array<string, mixed>
     */
    public function openApi(User $user): array
    {
        $token = $user->tokens()->where('name', 'seller-open-api')->latest()->first();

        return [
            'has_token' => (bool) $token,
            'key_preview' => $token ? 'sh_live_••••••••'.$this->tokenMask($token) : null,
            'status' => $token ? 'active' : 'inactive',
            'last_used_at' => $token?->last_used_at?->toIso8601String(),
            'message' => $token
                ? 'Your Open API key is active. The full key is shown only when generated.'
                : 'Generate a store API token for inventory sync. The plain key is shown once.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function issueOpenApiToken(User $user): array
    {
        $user->tokens()->where('name', 'seller-open-api')->delete();

        /** @var NewAccessToken $token */
        $token = $user->createToken('seller-open-api', ['seller:open-api']);

        return [
            'token' => $token->plainTextToken,
            'key_preview' => 'sh_live_••••••••'.substr($token->accessToken->uuid ?? '', -4),
            'status' => 'active',
            'has_token' => true,
            'message' => 'Copy this key now. It will not be shown again.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function revokeOpenApiToken(User $user): array
    {
        $user->tokens()->where('name', 'seller-open-api')->delete();

        return $this->openApi($user);
    }

    /**
     * @return array<string, mixed>
     */
    private function storePayload(SellerProfile $profile, ?User $user): array
    {
        return [
            'store_name' => $profile->store_name,
            'store_slug' => $profile->store_slug,
            'description' => $profile->description,
            'city' => $profile->city,
            'state' => $profile->state,
            'business_type' => $profile->business_type,
            'email' => $user?->email,
            'rating' => (float) ($profile->rating_avg ?? 0),
            'accepts_onsite_visits' => (bool) $profile->accepts_onsite_visits,
            'verified' => $profile->verification_status === 'verified',
            'policies' => [
                'returns' => is_array($profile->policies) ? ($profile->policies['returns'] ?? '') : '',
                'shipping' => is_array($profile->policies) ? ($profile->policies['shipping'] ?? '') : '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Order $order): array
    {
        $item = $order->items->first();
        $notes = is_string($order->notes) ? json_decode($order->notes, true) : [];

        return [
            'id' => $order->uuid,
            'order_number' => $order->order_number,
            'product' => $item?->name ?? 'Order',
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'date' => $order->created_at?->timezone(config('app.timezone'))->format('M j, Y'),
            'delivery_zip' => is_array($notes) ? ($notes['delivery_zip'] ?? null) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function onsitePayload(OnsiteVisitAppointment $appt): array
    {
        return [
            'id' => $appt->uuid,
            'status' => $appt->status,
            'scheduled_at' => optional($appt->scheduled_at)->toIso8601String(),
            'customer_notes' => $appt->customer_notes,
            'seller_notes' => $appt->seller_notes,
        ];
    }

    private function uniqueProductSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'imported-product';
        $counter = 1;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function tokenMask(?PersonalAccessToken $token): string
    {
        $uuid = (string) ($token?->uuid ?? '');

        return $uuid !== '' ? substr($uuid, -4) : 'key';
    }
}
