<?php

namespace App\Services\Order;

use App\Models\User;
use App\Repositories\Catalog\CatalogRepository;
use App\Repositories\Order\PurchaseRepository;
use App\Services\Audit\ActivityLogger;
use App\Services\Delivery\CrsDeliveryQuoteService;
use App\Services\Platform\MarkupCalculationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private readonly CatalogRepository $catalogRepository,
        private readonly PurchaseRepository $purchaseRepository,
        private readonly MarkupCalculationService $markup,
        private readonly CrsDeliveryQuoteService $crsDelivery,
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function quote(array $payload): array
    {
        $product = $this->requireProduct((string) $payload['product_id']);
        $quote = $this->checkoutQuote($product);

        return [
            'product' => $this->catalogRepository->toStorefrontCard($product),
            'quote' => $quote,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function complete(?User $buyer, array $payload, Request $request): array
    {
        $product = $this->requireProduct((string) $payload['product_id']);
        $quote = $this->checkoutQuote($product);

        $order = $this->purchaseRepository->createTrustedOrder($buyer, $product, $quote, [
            'delivery_address' => $payload['delivery_address'],
            'delivery_zip' => $payload['delivery_zip'],
            'delivery_type' => $payload['delivery_type'] ?? 'CURBSIDE',
        ]);

        $this->activityLogger->log(
            $buyer,
            'order.placed',
            'order',
            null,
            [
                'order_number' => $order['order_number'],
                'product_uuid' => $product->uuid,
                'total' => $order['total'],
            ],
            $request,
        );

        return [
            'order' => $order,
            'quote' => $quote,
        ];
    }

    private function requireProduct(string $uuid)
    {
        $product = $this->catalogRepository->findActiveByUuid($uuid);

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => ['Product is not available.'],
            ]);
        }

        return $product;
    }

    /**
     * @return array<string, mixed>
     */
    private function checkoutQuote($product): array
    {
        $crs = $this->crsDelivery->quote((float) $product->listing_price);
        $quote = $this->markup->checkoutQuote((float) $product->listing_price, (float) $crs['delivery_fee']);
        $quote['crs'] = [
            'value_range' => $crs['value_range'],
            'base_rate' => $crs['base_rate'],
            'source' => $crs['source'],
        ];

        return $quote;
    }
}
