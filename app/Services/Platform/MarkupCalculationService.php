<?php

namespace App\Services\Platform;

use App\Repositories\Platform\MarkupRuleRepository;

class MarkupCalculationService
{
    public const DEFAULT_DELIVERY_FEE = 120.00;

    /**
     * @var array<string, float>
     */
    private const FALLBACK_RATES = [
        'sh_brand_new' => 0.40,
        'sh_thrift_floor' => 0.10,
        'dist_reseller' => 0.05,
        'seller_listing' => 0.10,
        'stripe_processing' => 0.03,
        'delivery_coordination' => 0.03,
    ];

    public function __construct(
        private readonly MarkupRuleRepository $repository,
    ) {}

    public function rate(string $ruleKey): float
    {
        $rates = $this->repository->activeRates();

        if ($rates->has($ruleKey)) {
            return (float) $rates->get($ruleKey);
        }

        return self::FALLBACK_RATES[$ruleKey] ?? 0.0;
    }

    public function listingMarkupRate(string $ownershipType, string $condition): float
    {
        if ($ownershipType === 'distribution') {
            $smoovehub = $this->smoovehubRate($condition);
            $reseller = $this->rate('dist_reseller');

            return ((1 + $smoovehub) * (1 + $reseller)) - 1;
        }

        if ($ownershipType === 'smoovehub') {
            return $this->smoovehubRate($condition);
        }

        return $this->rate('seller_listing');
    }

    public function listingPrice(string $ownershipType, string $condition, float $basePrice): float
    {
        $rate = $this->listingMarkupRate($ownershipType, $condition);

        return round($basePrice * (1 + $rate), 2);
    }

    /**
     * Customer checkout totals. Listing price already includes marketplace markup.
     *
     * @return array<string, mixed>
     */
    public function checkoutQuote(float $listingPrice, float $deliveryFee = self::DEFAULT_DELIVERY_FEE): array
    {
        $processing = round($listingPrice * $this->rate('stripe_processing'), 2);
        $coordination = round($deliveryFee * $this->rate('delivery_coordination'), 2);
        $total = round($listingPrice + $deliveryFee + $processing + $coordination, 2);

        return [
            'listing_price' => round($listingPrice, 2),
            'markup_display' => 'Included',
            'delivery_fee' => round($deliveryFee, 2),
            'processing_fee' => $processing,
            'delivery_coordination_fee' => $coordination,
            'processing_rate' => $this->rate('stripe_processing'),
            'delivery_coordination_rate' => $this->rate('delivery_coordination'),
            'total' => $total,
        ];
    }

    private function smoovehubRate(string $condition): float
    {
        return $condition === 'BRAND_NEW'
            ? $this->rate('sh_brand_new')
            : $this->rate('sh_thrift_floor');
    }
}
