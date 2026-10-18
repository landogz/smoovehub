<?php

namespace App\Repositories\Order;

use App\Models\Catalog\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseRepository
{
    /**
     * @param  array<string, mixed>  $quote
     * @param  array<string, mixed>  $delivery
     * @return array<string, mixed>
     */
    public function createTrustedOrder(?User $buyer, Product $product, array $quote, array $delivery): array
    {
        return DB::transaction(function () use ($buyer, $product, $quote, $delivery) {
            $locked = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();

            if ($locked->quantity < 1) {
                throw ValidationException::withMessages([
                    'product_id' => ['This item is out of stock.'],
                ]);
            }

            $orderNumber = $this->uniqueOrderNumber();

            $order = Order::query()->create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer?->id,
                'seller_profile_id' => $locked->seller_profile_id,
                'order_type' => 'marketplace',
                'fulfillment_source' => $locked->ownership_type === 'distribution' ? 'distributor' : 'seller',
                'subtotal' => $quote['listing_price'],
                'delivery_fee' => $quote['delivery_fee'],
                'processing_fee' => $quote['processing_fee'],
                'platform_fee' => $quote['delivery_coordination_fee'],
                'total' => $quote['total'],
                'currency' => 'USD',
                'status' => 'placed',
                'payment_status' => 'unpaid',
                'notes' => json_encode([
                    'delivery_address' => $delivery['delivery_address'],
                    'delivery_zip' => $delivery['delivery_zip'],
                    'delivery_type' => $delivery['delivery_type'],
                    'trusted_checkout' => true,
                ]),
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $locked->id,
                'sku' => $locked->sku,
                'name' => $locked->name,
                'condition' => $locked->condition,
                'seller_name' => $locked->sellerProfile?->store_name ?? 'SmooveHub',
                'quantity' => 1,
                'unit_price' => $quote['listing_price'],
                'line_total' => $quote['listing_price'],
                'metadata' => [
                    'product_uuid' => $locked->uuid,
                ],
            ]);

            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'changed_by' => $buyer?->id,
                'status' => 'placed',
                'notes' => 'Trusted checkout — awaiting Stripe confirmation (Phase 3).',
            ]);

            $locked->decrement('quantity');

            return [
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'product_name' => $locked->name,
                'total' => (float) $order->total,
            ];
        });
    }

    private function uniqueOrderNumber(): string
    {
        do {
            $number = 'SH-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
