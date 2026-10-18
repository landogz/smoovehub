<?php

namespace App\Repositories\Site;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressSuggestRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query): array
    {
        $key = 'address-suggest:'.md5(mb_strtolower($query));
        $ttl = (int) config('services.photon.cache_seconds', 3600);

        return Cache::remember($key, $ttl, function () use ($query): array {
            return $this->fetchPhoton($query);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchPhoton(string $query): array
    {
        $baseUrl = rtrim((string) config('services.photon.url', 'https://photon.komoot.io'), '/');

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.photon.timeout', 4))
                ->withHeaders([
                    'User-Agent' => 'SmooveHub/1.0 (https://smoovehub.competitiverelocationcrm.com)',
                ])
                ->get($baseUrl.'/api', [
                    'q' => $query,
                    'limit' => 8,
                    'lang' => 'en',
                    'lat' => 39.8283,
                    'lon' => -98.5795,
                ]);

            if (! $response->successful()) {
                Log::warning('Photon address suggest failed.', [
                    'status' => $response->status(),
                ]);

                return [];
            }

            $features = $response->json('features');

            return is_array($features) ? $features : [];
        } catch (\Throwable $exception) {
            Log::warning('Photon address suggest unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }
}
