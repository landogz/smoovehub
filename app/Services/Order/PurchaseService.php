<?php

namespace App\Services\Order;

use App\Models\User;
use App\Repositories\Order\PurchaseRepository;
use Illuminate\Http\Request;

class PurchaseService
{
    public function __construct(
        private readonly PurchaseRepository $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function complete(?User $buyer, array $payload, Request $request): array
    {
        $deliveryFee = 120.0;
        $processingFee = round(((float) $payload['price']) * 0.03, 2);
        $deliveryPct = round($deliveryFee * 0.03, 2);

        $computedTotal = (float) $payload['price'] + $deliveryFee + $processingFee + $deliveryPct;

        $order = $this->repository->createPurchase($buyer, [
            ...$payload,
            'delivery_fee' => $deliveryFee,
            'processing_fee' => $processingFee,
            'platform_fee' => $deliveryPct,
            'total' => $payload['total'] ?? $computedTotal,
        ]);

        return $order;
    }
}
