<?php

namespace Tests\Unit;

use App\Services\Delivery\CrsDeliveryQuoteService;
use Tests\TestCase;

class CrsDeliveryQuoteServiceTest extends TestCase
{
    public function test_live_crs_brackets_price_a_449_chair_at_179_99(): void
    {
        $quote = app(CrsDeliveryQuoteService::class)->computeFromRates([
            ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
            ['service_type' => 'delivery', 'value_range' => '$750 - Less than $1500', 'rate' => 199.99],
            ['service_type' => 'delivery_mileage', 'rate' => 1.75],
        ], 449);

        $this->assertEquals(179.99, $quote['delivery_fee']);
        $this->assertSame('Less than $750', $quote['value_range']);
    }

    public function test_mileage_over_twelve_miles_uses_crs_per_mile_rate(): void
    {
        $quote = app(CrsDeliveryQuoteService::class)->computeFromRates([
            ['service_type' => 'delivery', 'value_range' => 'Less than $750', 'rate' => 179.99],
            ['service_type' => 'delivery_mileage', 'rate' => 1.75],
        ], 449, 20);

        $this->assertEquals(8.0, $quote['billable_miles']);
        $this->assertEquals(14.0, $quote['mileage_fee']);
        $this->assertEquals(193.99, $quote['delivery_fee']);
    }
}
