<?php

namespace Tests\Unit;

use App\Services\Platform\MarkupCalculationService;
use Tests\TestCase;

class MarkupCalculationServiceTest extends TestCase
{
    public function test_seller_listing_adds_ten_percent(): void
    {
        $price = app(MarkupCalculationService::class)->listingPrice('seller', 'BRAND_NEW', 100);

        $this->assertEquals(110.0, $price);
    }

    public function test_smoovehub_brand_new_adds_forty_percent(): void
    {
        $price = app(MarkupCalculationService::class)->listingPrice('smoovehub', 'BRAND_NEW', 100);

        $this->assertEquals(140.0, $price);
    }

    public function test_checkout_quote_adds_processing_and_delivery_fees(): void
    {
        $quote = app(MarkupCalculationService::class)->checkoutQuote(100.0, 120.0);

        $this->assertEquals(3.0, $quote['processing_fee']);
        $this->assertEquals(3.6, $quote['delivery_coordination_fee']);
        $this->assertEquals(226.6, $quote['total']);
    }
}
