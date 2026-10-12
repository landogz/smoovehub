<?php

namespace App\Repositories\Order;

use App\Models\Order\Order;
use App\Models\User;

class PurchaseRepository
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createPurchase(?User $buyer, array $data): array
    {
        $orderNumber = 'SH-'.random_int(1000, 9999);

        if ($buyer) {
            Order::query()->create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer->id,
                'order_type' => 'marketplace',
                'fulfillment_source' => 'seller',
                'subtotal' => $data['price'],
                'delivery_fee' => $data['delivery_fee'] ?? 120,
                'processing_fee' => $data['processing_fee'] ?? round($data['price'] * 0.03, 2),
                'platform_fee' => $data['platform_fee'] ?? 0,
                'total' => $data['total'],
                'currency' => 'USD',
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => json_encode([
                    'product_id' => $data['product_id'],
                    'product_name' => $data['product_name'],
                    'seller_name' => $data['seller_name'],
                    'delivery_address' => $data['delivery_address'],
                    'delivery_zip' => $data['delivery_zip'],
                    'delivery_type' => $data['delivery_type'] ?? 'CURBSIDE',
                ]),
                'paid_at' => now(),
            ]);
        }

        return [
            'order_number' => $orderNumber,
            'status' => 'Confirmed',
            'product_name' => $data['product_name'],
            'total' => $data['total'],
        ];
    }
}
