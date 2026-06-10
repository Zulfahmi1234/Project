<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class NominatimService
{
    /** Cache TTL for boundary: 24 hours */
    const CACHE_TTL_BOUNDARY = 86400;

    /**
     * Get the base URL for Nominatim API.
     */
    private function getBaseUrl(): string
    {
        return config('services.nominatim.base_url', 'https://nominatim.openstreetmap.org');
    }

    /**
     * Get the User-Agent header for Nominatim requests.
     */
    private function getUserAgent(): string
    {
        return config('services.nominatim.user_agent', 'AeroCast/1.0 (contact: opensky@student.ac.id)');
    }

    /**
     * Normalize query for cache key.
     * Converts to lowercase and replaces spaces with underscores.
     */
    private function normalizeQuery(string $query): string
    {
        return strtolower(str_replace(' ', '_', trim($query)));
    }

    /**
     * Get GeoJSON boundary for a city.
     *
     * @param string $query City name to search
     * @return array|null Formatted boundary data, or null if not found
     *
     * @throws ConnectionException
     */
    public function getBoundary(string $query): ?array
    {
        $queryNormalized = $this->normalizeQuery($query);
        $cacheKey = "boundary_{$queryNormalized}";
        $isCached = Cache::has($cacheKey);

        $boundaryData = Cache::remember($cacheKey, self::CACHE_TTL_BOUNDARY, function () use ($query) {
            $response = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->timeout(10)
                ->withHeaders([
                    'User-Agent'      => $this->getUserAgent(),
                    'Accept-Language' => 'id,en;q=0.9',
                ])
                ->get($this->getBaseUrl() . '/search', [
                    'q'               => $query,
                    'polygon_geojson' => 1,
                    'format'          => 'json',
                    'limit'           => 1,
                ]);

            if ($response->failed()) {
                throw new ConnectionException('Nominatim API tidak merespons.');
            }

            $results = $response->json();

            if (empty($results)) {
                return null;
            }

            $result = $results[0];

            return [
                'type' => 'Feature',
                'properties' => [
                    'display_name' => $result['display_name'] ?? null,
                    'osm_id'       => $result['osm_id'] ?? null,
                    'place_id'     => $result['place_id'] ?? null,
                    'boundingbox'  => $result['boundingbox'] ?? [],
                ],
                'geometry' => $result['geojson'] ?? null,
            ];
        });

        if ($boundaryData === null) {
            // Don't cache null results — remove from cache
            Cache::forget("boundary_{$queryNormalized}");
            return null;
        }

        return [
            'query'            => $query,
            'cached'           => $isCached,
            'cache_expires_at' => $isCached
                ? now()->addSeconds(self::CACHE_TTL_BOUNDARY)->toIso8601String()
                : null,
            'boundary'         => $boundaryData,
        ];
    }
}
