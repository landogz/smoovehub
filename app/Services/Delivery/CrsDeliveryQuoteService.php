<?php

namespace App\Services\Delivery;

use App\Repositories\Delivery\CrsRateRepository;
use App\Services\Platform\MarkupCalculationService;

class CrsDeliveryQuoteService
{
    /**
     * Live Competitive Relocation brackets (used if CRS HTTP is down).
     *
     * @var array<int, array{min: float, max: ?float, rate: float, value_range: string}>
     */
    private const FALLBACK_BRACKETS = [
        ['min' => 0.0, 'max' => 750.0, 'rate' => 179.99, 'value_range' => 'Less than $750'],
        ['min' => 750.0, 'max' => 1500.0, 'rate' => 199.99, 'value_range' => '$750 - Less than $1500'],
        ['min' => 1500.0, 'max' => 2000.0, 'rate' => 205.94, 'value_range' => '$1500 - Less than $2000'],
        ['min' => 2000.0, 'max' => 2750.0, 'rate' => 225.99, 'value_range' => '$2000 - Less than $2750'],
        ['min' => 2750.0, 'max' => 4000.0, 'rate' => 299.99, 'value_range' => '$2750 - Less than $4000'],
        ['min' => 4000.0, 'max' => 6000.0, 'rate' => 325.99, 'value_range' => '$4000 - Less than $6000'],
        ['min' => 6000.0, 'max' => 8000.0, 'rate' => 475.99, 'value_range' => '$6000 - Less than $8000'],
        ['min' => 8000.0, 'max' => 10000.0, 'rate' => 950.99, 'value_range' => '$8000 - Less than $10000'],
        ['min' => 10000.0, 'max' => null, 'rate' => 1325.99, 'value_range' => '$10000 and more'],
    ];

    public function __construct(
        private readonly CrsRateRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function quote(float $itemValue, ?float $miles = null): array
    {
        $fromApi = $this->repository->quote($itemValue, $miles);

        if (is_array($fromApi) && isset($fromApi['delivery_fee'])) {
            return $this->normalize($fromApi, $itemValue);
        }

        $computed = $this->computeFromRates($this->repository->serviceRates(), $itemValue, $miles);

        if ($computed !== null) {
            return $computed;
        }

        return $this->computeFromBrackets(self::FALLBACK_BRACKETS, $itemValue, $miles, 1.75, 'crs_fallback');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rates
     * @return array<string, mixed>|null
     */
    public function computeFromRates(array $rates, float $itemValue, ?float $miles = null): ?array
    {
        $brackets = [];
        $mileageRate = 0.0;

        foreach ($rates as $rate) {
            if (! is_array($rate)) {
                continue;
            }

            $type = (string) ($rate['service_type'] ?? '');

            if ($type === 'delivery_mileage') {
                $mileageRate = (float) ($rate['rate'] ?? 0);
                continue;
            }

            if ($type !== 'delivery') {
                continue;
            }

            $bounds = $this->parseValueRange((string) ($rate['value_range'] ?? ''));

            if ($bounds === null) {
                continue;
            }

            $brackets[] = [
                'min' => $bounds['min'],
                'max' => $bounds['max'],
                'rate' => (float) ($rate['rate'] ?? 0),
                'value_range' => (string) $rate['value_range'],
            ];
        }

        if ($brackets === []) {
            return null;
        }

        usort($brackets, fn (array $a, array $b) => $a['min'] <=> $b['min']);

        return $this->computeFromBrackets($brackets, $itemValue, $miles, $mileageRate, 'crs_service_rates');
    }

    /**
     * @param  array<int, array{min: float, max: ?float, rate: float, value_range: string}>  $brackets
     * @return array<string, mixed>
     */
    private function computeFromBrackets(array $brackets, float $itemValue, ?float $miles, float $mileageRate, string $source): array
    {
        $matched = $brackets[array_key_last($brackets)];

        foreach ($brackets as $bracket) {
            $inMin = $itemValue >= $bracket['min'];
            $inMax = $bracket['max'] === null ? true : $itemValue < $bracket['max'];

            if ($inMin && $inMax) {
                $matched = $bracket;
                break;
            }
        }

        $mileageFee = 0.0;
        $billableMiles = 0.0;
        $included = 12.0;

        if ($miles !== null && $miles > $included && $mileageRate > 0) {
            $billableMiles = round($miles - $included, 2);
            $mileageFee = round($billableMiles * $mileageRate, 2);
        }

        $baseRate = round((float) $matched['rate'], 2);

        return [
            'delivery_fee' => round($baseRate + $mileageFee, 2),
            'base_rate' => $baseRate,
            'mileage_fee' => $mileageFee,
            'miles' => $miles !== null ? round($miles, 2) : null,
            'billable_miles' => $billableMiles,
            'mileage_rate' => $mileageRate,
            'included_miles' => $included,
            'value_range' => $matched['value_range'],
            'item_value' => round($itemValue, 2),
            'source' => $source,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalize(array $payload, float $itemValue): array
    {
        $deliveryFee = (float) ($payload['delivery_fee'] ?? MarkupCalculationService::DEFAULT_DELIVERY_FEE);

        return [
            'delivery_fee' => round($deliveryFee, 2),
            'base_rate' => round((float) ($payload['base_rate'] ?? $deliveryFee), 2),
            'mileage_fee' => round((float) ($payload['mileage_fee'] ?? 0), 2),
            'miles' => isset($payload['miles']) ? (float) $payload['miles'] : null,
            'billable_miles' => (float) ($payload['billable_miles'] ?? 0),
            'mileage_rate' => (float) ($payload['mileage_rate'] ?? 0),
            'included_miles' => (float) ($payload['included_miles'] ?? 12),
            'value_range' => $payload['value_range'] ?? null,
            'item_value' => round((float) ($payload['item_value'] ?? $itemValue), 2),
            'source' => (string) ($payload['source'] ?? 'crs_delivery_quote'),
        ];
    }

    /**
     * @return array{min: float, max: ?float}|null
     */
    public function parseValueRange(string $range): ?array
    {
        $normalized = strtolower(trim(str_replace(',', '', $range)));

        if ($normalized === '') {
            return null;
        }

        if (preg_match('/^\$?([0-9.]+)\s+and more$/', $normalized, $matches)) {
            return ['min' => (float) $matches[1], 'max' => null];
        }

        if (preg_match('/^\$?([0-9.]+)\s*-\s*less than \$?([0-9.]+)$/', $normalized, $matches)) {
            return ['min' => (float) $matches[1], 'max' => (float) $matches[2]];
        }

        if (preg_match('/^less than \$?([0-9.]+)$/', $normalized, $matches)) {
            return ['min' => 0.0, 'max' => (float) $matches[1]];
        }

        return null;
    }
}
