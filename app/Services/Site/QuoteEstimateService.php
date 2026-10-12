<?php

namespace App\Services\Site;

class QuoteEstimateService
{
    private const PROCESSING_RATE = 0.03;

    private const FREE_STAIR_FLIGHTS = 3;

    private const STAIRS_OVER_FREE = 75.0;

    private const FREE_ELEV_FLOORS = 3;

    private const ELEV_OVER_FREE = 75.0;

    private const LONG_CARRY_FREE_FT = 75;

    private const LONG_CARRY_FEE = 100.0;

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function estimate(array $input): array
    {
        $pickupZip = (string) $input['pickup_zip'];
        $deliveryZip = (string) $input['delivery_zip'];
        $doorWidth = (float) $input['door_width'];
        $doorHeight = (float) $input['door_height'];
        $stairsEnabled = (bool) ($input['stairs_enabled'] ?? false);
        $stairFlights = $stairsEnabled ? max(0, (int) ($input['stair_flights'] ?? 0)) : 0;
        $stairType = (string) ($input['stair_type'] ?? 'standard');
        $elevatorEnabled = (bool) ($input['elevator_enabled'] ?? false);
        $elevatorFloors = $elevatorEnabled ? max(0, (int) ($input['elevator_floors'] ?? 0)) : 0;
        $elevatorWidth = $elevatorEnabled ? (float) ($input['elevator_width'] ?? 0) : 0.0;
        $carryDistance = max(0, (float) ($input['carry_distance'] ?? 0));
        $deliveryType = (string) ($input['delivery_type'] ?? 'CURBSIDE');
        $inHome = $deliveryType === 'IN_HOME';

        $miles = $this->estimateMiles($pickupZip, $deliveryZip);
        $base = $this->crsBaseRate($miles);
        $surchargeTotal = 0.0;
        $items = [[
            'name' => 'Base delivery (CRS)',
            'amount' => $base,
            'type' => 'base',
            'note' => 'Est. '.number_format($miles, 0).' mi · Competitive Relocation Service',
        ]];

        if ($inHome) {
            $items[] = [
                'name' => 'In-home delivery',
                'amount' => 0,
                'type' => 'inc',
                'note' => 'Included — no extra charge',
            ];
        }

        if ($stairFlights > 0) {
            $overFlights = max(0, $stairFlights - self::FREE_STAIR_FLIGHTS);

            if ($stairFlights <= self::FREE_STAIR_FLIGHTS) {
                $items[] = [
                    'name' => 'Stair carry ('.$stairFlights.' flight'.($stairFlights > 1 ? 's' : '').')',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'Free — within '.self::FREE_STAIR_FLIGHTS.'-flight threshold',
                ];
            } else {
                $stairFee = $overFlights * self::STAIRS_OVER_FREE;
                $items[] = [
                    'name' => 'Stair carry ('.$stairFlights.' flights)',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'Flights 1–'.self::FREE_STAIR_FLIGHTS.': FREE',
                ];
                $items[] = [
                    'name' => 'Stair overage ('.$overFlights.' flight'.($overFlights > 1 ? 's' : '').' above '.self::FREE_STAIR_FLIGHTS.'th)',
                    'amount' => $stairFee,
                    'type' => 'sur',
                    'note' => '$'.number_format(self::STAIRS_OVER_FREE, 0).'/flight over '.self::FREE_STAIR_FLIGHTS,
                ];
                $surchargeTotal += $stairFee;
            }

            if ($stairType === 'spiral') {
                $items[] = [
                    'name' => 'Spiral staircase (1 extra flight equiv.)',
                    'amount' => self::STAIRS_OVER_FREE,
                    'type' => 'sur',
                    'note' => 'Spiral counts as one additional flight',
                ];
                $surchargeTotal += self::STAIRS_OVER_FREE;
            }
        }

        if ($elevatorEnabled && $elevatorFloors > 0) {
            $overFloors = max(0, $elevatorFloors - self::FREE_ELEV_FLOORS);

            if ($elevatorFloors <= self::FREE_ELEV_FLOORS) {
                $items[] = [
                    'name' => 'Elevator ('.$elevatorFloors.' floor'.($elevatorFloors > 1 ? 's' : '').')',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'Free — within '.self::FREE_ELEV_FLOORS.'-floor threshold',
                ];
            } else {
                $elevFee = $overFloors * self::ELEV_OVER_FREE;
                $items[] = [
                    'name' => 'Elevator ('.$elevatorFloors.' floors)',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'Floors 1–'.self::FREE_ELEV_FLOORS.': FREE',
                ];
                $items[] = [
                    'name' => 'Elevator overage ('.$overFloors.' floor'.($overFloors > 1 ? 's' : '').' above '.self::FREE_ELEV_FLOORS.'th)',
                    'amount' => $elevFee,
                    'type' => 'sur',
                    'note' => '$'.number_format(self::ELEV_OVER_FREE, 0).'/floor over '.self::FREE_ELEV_FLOORS,
                ];
                $surchargeTotal += $elevFee;
            }
        }

        if ($carryDistance > 0) {
            if ($carryDistance <= self::LONG_CARRY_FREE_FT) {
                $items[] = [
                    'name' => 'Long carry ('.$carryDistance.' ft)',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'Free — within '.self::LONG_CARRY_FREE_FT.'ft threshold',
                ];
            } else {
                $items[] = [
                    'name' => 'Long carry ('.$carryDistance.' ft)',
                    'amount' => 0,
                    'type' => 'inc',
                    'note' => 'First '.self::LONG_CARRY_FREE_FT.'ft: FREE',
                ];
                $items[] = [
                    'name' => 'Long carry overage (+'.number_format($carryDistance - self::LONG_CARRY_FREE_FT, 0).' ft over '.self::LONG_CARRY_FREE_FT.'ft)',
                    'amount' => self::LONG_CARRY_FEE,
                    'type' => 'sur',
                    'note' => 'Flat fee — any distance over '.self::LONG_CARRY_FREE_FT.'ft',
                ];
                $surchargeTotal += self::LONG_CARRY_FEE;
            }
        }

        $preProcessing = $base + $surchargeTotal;
        $processingFee = round($preProcessing * self::PROCESSING_RATE, 2);

        $items[] = [
            'name' => 'SmooveHub processing fee (3%)',
            'amount' => $processingFee,
            'type' => 'fee',
            'note' => '3% of delivery total · Charged by SmooveHub',
        ];

        $items[] = [
            'name' => 'Waiting time',
            'amount' => 0,
            'type' => 'info',
            'note' => 'FREE first 30 min · $150/hr after (if owner not present)',
        ];

        $total = $preProcessing + $processingFee;
        $quoteId = 'SHQ-'.strtoupper(substr(base_convert((string) time(), 10, 36), -6));
        $distLabel = match (true) {
            $miles <= 50 => 'Auto-approved',
            $miles <= 100 => 'CRM Confirmation',
            $miles <= 200 => 'Validation Required',
            default => 'Custom Quote',
        };

        $pills = $this->buildPills($inHome, $stairFlights, $elevatorEnabled, $elevatorFloors, $carryDistance);
        $summary = $this->buildSummary(
            $pickupZip,
            $deliveryZip,
            $doorWidth,
            $doorHeight,
            $stairFlights,
            $stairType,
            $elevatorEnabled,
            $elevatorFloors,
            $elevatorWidth,
            $carryDistance,
            $inHome,
            $input,
        );

        $crsUrl = $this->buildCrsUrl($input, $quoteId, $doorWidth, $doorHeight, $stairFlights, $stairType, $elevatorEnabled, $elevatorWidth, $elevatorFloors, $inHome);

        return [
            'quote_id' => $quoteId,
            'miles' => round($miles, 0),
            'distance_label' => $distLabel,
            'expires_at' => now()->addDay()->format('M j'),
            'estimate_total' => round($total, 2),
            'line_items' => $items,
            'pills' => $pills,
            'summary' => $summary,
            'requires_phone_quote' => $miles > 200,
            'crs_url' => $crsUrl,
        ];
    }

    private function estimateMiles(string $pickupZip, string $deliveryZip): float
    {
        return max(5, min(500, abs(((int) $pickupZip) - ((int) $deliveryZip)) * 0.012));
    }

    private function crsBaseRate(float $miles): float
    {
        return match (true) {
            $miles <= 25 => 75.0,
            $miles <= 50 => 120.0,
            $miles <= 100 => 190.0,
            $miles <= 200 => 290.0,
            default => 390.0,
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     */
    private function buildPills(
        bool $inHome,
        int $stairFlights,
        bool $elevatorEnabled,
        int $elevatorFloors,
        float $carryDistance,
    ): array {
        $pills = [];

        if ($inHome) {
            $pills[] = ['text' => 'In-home (free)', 'warn' => false];
        }

        if ($stairFlights > 0 && $stairFlights <= self::FREE_STAIR_FLIGHTS) {
            $pills[] = ['text' => $stairFlights.' stair flight'.($stairFlights > 1 ? 's' : '').' (free)', 'warn' => false];
        }

        if ($stairFlights > self::FREE_STAIR_FLIGHTS) {
            $pills[] = ['text' => $stairFlights.' stair flights (+$'.(($stairFlights - self::FREE_STAIR_FLIGHTS) * self::STAIRS_OVER_FREE).')', 'warn' => true];
        }

        if ($elevatorEnabled && $elevatorFloors > 0 && $elevatorFloors <= self::FREE_ELEV_FLOORS) {
            $pills[] = ['text' => 'Elevator '.$elevatorFloors.' fl (free)', 'warn' => false];
        }

        if ($elevatorEnabled && $elevatorFloors > self::FREE_ELEV_FLOORS) {
            $pills[] = ['text' => 'Elevator '.$elevatorFloors.' fl (+$'.(($elevatorFloors - self::FREE_ELEV_FLOORS) * self::ELEV_OVER_FREE).')', 'warn' => true];
        }

        if ($carryDistance > 0 && $carryDistance <= self::LONG_CARRY_FREE_FT) {
            $pills[] = ['text' => 'Carry '.$carryDistance.'ft (free)', 'warn' => false];
        }

        if ($carryDistance > self::LONG_CARRY_FREE_FT) {
            $pills[] = ['text' => 'Carry '.$carryDistance.'ft (+$100)', 'warn' => true];
        }

        $pills[] = ['text' => 'Wait: 30min free · $150/hr after', 'warn' => false];

        return $pills;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, string>>
     */
    private function buildSummary(
        string $pickupZip,
        string $deliveryZip,
        float $doorWidth,
        float $doorHeight,
        int $stairFlights,
        string $stairType,
        bool $elevatorEnabled,
        int $elevatorFloors,
        float $elevatorWidth,
        float $carryDistance,
        bool $inHome,
        array $input,
    ): array {
        $elevatorLabel = 'No';

        if ($elevatorEnabled) {
            $elevatorLabel = $elevatorFloors.' floor'.($elevatorFloors > 1 ? 's' : '');

            if ($elevatorWidth > 0) {
                $elevatorLabel .= ' — '.$elevatorWidth.'"';
            }
        }

        return [
            ['label' => 'Pickup ZIP', 'value' => $pickupZip],
            ['label' => 'Delivery ZIP', 'value' => $deliveryZip],
            ['label' => 'Door Width', 'value' => $doorWidth.'"'],
            ['label' => 'Door Height', 'value' => $doorHeight.'"'],
            ['label' => 'Stairs', 'value' => $stairFlights > 0 ? $stairFlights.' flight'.($stairFlights > 1 ? 's' : '').' ('.$stairType.')' : 'None'],
            ['label' => 'Elevator', 'value' => $elevatorLabel],
            ['label' => 'Long carry', 'value' => $carryDistance > 0 ? $carryDistance.'ft' : '—'],
            ['label' => 'Delivery type', 'value' => $inHome ? 'In-home (no extra charge)' : 'Curbside'],
            ['label' => 'Waiting policy', 'value' => '30 min free · $150/hr after (owner absent)'],
            ['label' => 'Weight', 'value' => filled($input['weight'] ?? null) ? (string) $input['weight'] : '—'],
            ['label' => 'Pieces', 'value' => filled($input['pieces'] ?? null) ? (string) $input['pieces'] : '—'],
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function buildCrsUrl(
        array $input,
        string $quoteId,
        float $doorWidth,
        float $doorHeight,
        int $stairFlights,
        string $stairType,
        bool $elevatorEnabled,
        float $elevatorWidth,
        int $elevatorFloors,
        bool $inHome,
    ): string {
        $base = 'https://www.competitiverelocation.com/services/?services=delivery';
        $params = http_build_query([
            'from_zip' => $input['pickup_zip'],
            'to_zip' => $input['delivery_zip'],
            'door_width' => $doorWidth,
            'door_height' => $doorHeight,
            'stairs' => $stairFlights > 0 ? 'yes' : 'no',
            'stair_flights' => $stairFlights,
            'stair_type' => $stairType,
            'elevator' => $elevatorEnabled ? 'yes' : 'no',
            'elevator_width' => $elevatorWidth > 0 ? $elevatorWidth : '',
            'elevator_floors' => $elevatorFloors,
            'delivery_type' => $inHome ? 'in_home' : 'curbside',
            'weight' => $input['weight'] ?? '',
            'pieces' => $input['pieces'] ?? '',
            'name' => $input['name'] ?? '',
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
            'quote_id' => $quoteId,
            'source' => 'smoovehub',
        ]);

        return $base.'&'.$params;
    }
}
