<?php

namespace App\Repositories\Delivery;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrsRateRepository
{
    /**
     * @return array<string, mixed>|null
     */
    public function quote(float $itemValue, ?float $miles = null): ?array
    {
        $baseUrl = rtrim((string) config('services.crs.url'), '/');

        if ($baseUrl === '') {
            return null;
        }

        if (Cache::get('crs.skip_delivery_quote_post')) {
            return null;
        }

        try {
            $request = Http::acceptJson()
                ->timeout((int) config('services.crs.timeout', 3))
                ->asJson();

            $token = (string) config('services.crs.token');

            if ($token !== '') {
                $request = $request->withHeaders(['X-SmooveHub-Key' => $token]);
            }

            $response = $request->post($baseUrl.'/api/delivery-quote', [
                'item_value' => $itemValue,
                'miles' => $miles,
                'source' => 'smoovehub',
            ]);

            if (in_array($response->status(), [404, 405, 501], true)) {
                Cache::put('crs.skip_delivery_quote_post', true, 3600);

                return null;
            }

            if ($response->successful() && $response->json('status') === true) {
                $data = $response->json('data');

                return is_array($data) ? $data : null;
            }
        } catch (\Throwable $exception) {
            Cache::put('crs.skip_delivery_quote_post', true, 300);
            Log::warning('CRS delivery-quote API unavailable.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function serviceRates(): array
    {
        $baseUrl = rtrim((string) config('services.crs.url'), '/');

        if ($baseUrl === '') {
            return [];
        }

        $ttl = (int) config('services.crs.cache_seconds', 3600);

        return Cache::remember('crs.service_rates', $ttl, function () use ($baseUrl) {
            try {
                $response = Http::acceptJson()
                    ->timeout((int) config('services.crs.timeout', 8))
                    ->get($baseUrl.'/api/servicerates');

                if (! $response->successful()) {
                    return [];
                }

                $rates = $response->json();

                return is_array($rates) ? $rates : [];
            } catch (\Throwable $exception) {
                Log::warning('CRS servicerates API unavailable.', [
                    'message' => $exception->getMessage(),
                ]);

                return [];
            }
        });
    }
}
