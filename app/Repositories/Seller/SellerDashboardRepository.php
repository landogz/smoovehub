<?php

namespace App\Repositories\Seller;

use App\Models\Distribution\DistributionCatalogImport;
use App\Models\Distribution\DistributionInventory;
use App\Models\Order\Order;
use App\Models\Seller\SellerProfile;
use App\Models\Tracking\OnsiteVisitAppointment;
use App\Models\User;

class SellerDashboardRepository
{
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
            'note' => 'Payouts release on CRS delivery confirmation. Stripe Connect ships in Phase 3.',
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
        $profile->update([
            'store_name' => $payload['store_name'],
            'description' => $payload['description'] ?? $profile->description,
            'city' => $payload['city'] ?? $profile->city,
            'state' => $payload['state'] ?? $profile->state,
            'business_type' => $payload['business_type'] ?? $profile->business_type,
            'accepts_onsite_visits' => (bool) ($payload['accepts_onsite_visits'] ?? $profile->accepts_onsite_visits),
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
            ->where('seller_profile_id', $profile->id)
            ->latest()
            ->get()
            ->map(fn (DistributionCatalogImport $import) => [
                'id' => $import->uuid,
                'status' => $import->status,
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
            ->map(fn (OnsiteVisitAppointment $appt) => [
                'id' => $appt->uuid,
                'status' => $appt->status,
                'scheduled_at' => optional($appt->scheduled_at)->toIso8601String(),
                'customer_notes' => $appt->customer_notes,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function openApi(): array
    {
        return [
            'key_preview' => 'sh_live_••••••••••••••••',
            'status' => 'pending',
            'message' => 'Seller API keys stay masked until Open API provisioning ships with inventory sync.',
        ];
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
}
