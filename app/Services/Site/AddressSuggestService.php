<?php

namespace App\Services\Site;

use App\Repositories\Site\AddressSuggestRepository;

class AddressSuggestService
{
    /**
     * @var array<string, string>
     */
    private const STATE_ABBR = [
        'alabama' => 'AL', 'alaska' => 'AK', 'arizona' => 'AZ', 'arkansas' => 'AR',
        'california' => 'CA', 'colorado' => 'CO', 'connecticut' => 'CT', 'delaware' => 'DE',
        'district of columbia' => 'DC', 'florida' => 'FL', 'georgia' => 'GA', 'hawaii' => 'HI',
        'idaho' => 'ID', 'illinois' => 'IL', 'indiana' => 'IN', 'iowa' => 'IA',
        'kansas' => 'KS', 'kentucky' => 'KY', 'louisiana' => 'LA', 'maine' => 'ME',
        'maryland' => 'MD', 'massachusetts' => 'MA', 'michigan' => 'MI', 'minnesota' => 'MN',
        'mississippi' => 'MS', 'missouri' => 'MO', 'montana' => 'MT', 'nebraska' => 'NE',
        'nevada' => 'NV', 'new hampshire' => 'NH', 'new jersey' => 'NJ', 'new mexico' => 'NM',
        'new york' => 'NY', 'north carolina' => 'NC', 'north dakota' => 'ND', 'ohio' => 'OH',
        'oklahoma' => 'OK', 'oregon' => 'OR', 'pennsylvania' => 'PA', 'rhode island' => 'RI',
        'south carolina' => 'SC', 'south dakota' => 'SD', 'tennessee' => 'TN', 'texas' => 'TX',
        'utah' => 'UT', 'vermont' => 'VT', 'virginia' => 'VA', 'washington' => 'WA',
        'west virginia' => 'WV', 'wisconsin' => 'WI', 'wyoming' => 'WY',
    ];

    public function __construct(
        private readonly AddressSuggestRepository $addressSuggestRepository,
    ) {}

    /**
     * @return list<array{label: string, line1: string|null, city: string|null, state: string|null, zip: string|null, lat: float|null, lng: float|null}>
     */
    public function suggest(string $query): array
    {
        $features = $this->addressSuggestRepository->search($query);
        $suggestions = [];
        $seen = [];

        foreach ($features as $feature) {
            if (! is_array($feature)) {
                continue;
            }

            $mapped = $this->mapFeature($feature);

            if ($mapped === null) {
                continue;
            }

            $key = mb_strtolower($mapped['label']);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $suggestions[] = $mapped;
        }

        return $suggestions;
    }

    /**
     * @param  array<string, mixed>  $feature
     * @return array{label: string, line1: string|null, city: string|null, state: string|null, zip: string|null, lat: float|null, lng: float|null}|null
     */
    private function mapFeature(array $feature): ?array
    {
        $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
        $country = strtoupper((string) ($properties['countrycode'] ?? $properties['country'] ?? ''));

        if ($country !== '' && ! in_array($country, ['US', 'USA', 'UNITED STATES'], true)) {
            return null;
        }

        $housenumber = trim((string) ($properties['housenumber'] ?? ''));
        $street = trim((string) ($properties['street'] ?? ''));
        $name = trim((string) ($properties['name'] ?? ''));
        $city = trim((string) ($properties['city'] ?? $properties['locality'] ?? $properties['district'] ?? ''));
        $state = $this->abbreviateState((string) ($properties['state'] ?? ''));
        $zip = $this->normalizeZip((string) ($properties['postcode'] ?? ''))
            ?? $this->normalizeZip($name);
        $geometry = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
        $coordinates = is_array($geometry['coordinates'] ?? null) ? $geometry['coordinates'] : [];

        $line1 = trim($housenumber.' '.$street);

        if ($line1 === '' && $name !== '' && $name !== $city && $this->normalizeZip($name) === null) {
            $line1 = $name;
        }

        if ($line1 === '') {
            $line1 = null;
        }

        $labelParts = array_values(array_filter([
            $line1,
            $city !== '' ? $city : null,
            $state,
            $zip,
        ], fn (?string $part) => $part !== null && $part !== ''));

        if ($labelParts === []) {
            return null;
        }

        return [
            'label' => implode(', ', $labelParts),
            'line1' => $line1,
            'city' => $city !== '' ? $city : null,
            'state' => $state,
            'zip' => $zip,
            'lat' => isset($coordinates[1]) ? (float) $coordinates[1] : null,
            'lng' => isset($coordinates[0]) ? (float) $coordinates[0] : null,
        ];
    }

    private function abbreviateState(string $state): ?string
    {
        $state = trim($state);

        if ($state === '') {
            return null;
        }

        if (preg_match('/^[A-Z]{2}$/', $state) === 1) {
            return $state;
        }

        $key = mb_strtolower($state);

        return self::STATE_ABBR[$key] ?? $state;
    }

    private function normalizeZip(string $zip): ?string
    {
        if (preg_match('/(\d{5})/', $zip, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
