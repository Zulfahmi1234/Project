<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Contract for GeoJSON boundary retrieval.
 *
 * Proxies Nominatim API to fetch administrative boundary polygons
 * for city/region visualization on the map.
 *
 * IMPORTANT: Nominatim has a rate limit of 1 request/second.
 * Implementations MUST cache results for 24 hours.
 * Cache key format: boundary_{query_normalized}
 */
interface BoundaryServiceInterface
{
    /**
     * Get GeoJSON boundary polygon for a city/region.
     *
     * Returns a GeoJSON Feature object with the boundary polygon
     * and metadata. Returns null if no boundary is found.
     * Null results should NOT be cached.
     *
     * @param string $query City or region name to search
     * @return array{
     *     query: string,
     *     cached: bool,
     *     cache_expires_at: string|null,
     *     boundary: array{
     *         type: string,
     *         properties: array{
     *             display_name: string|null,
     *             osm_id: int|null,
     *             place_id: int|null,
     *             boundingbox: array<string>
     *         },
     *         geometry: array|null
     *     }
     * }|null Null when no boundary is found for the query
     *
     * @throws ConnectionException When Nominatim API is unreachable
     */
    public function getBoundary(string $query): ?array;
}
