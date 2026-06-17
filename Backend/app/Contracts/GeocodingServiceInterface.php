<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Contract for geocoding (city search) operations.
 *
 * Proxies Open-Meteo Geocoding API to search for cities
 * by name and return their coordinates and metadata.
 */
interface GeocodingServiceInterface
{
    /**
     * Search cities by name via Open-Meteo Geocoding API.
     *
     * Returns a list of matching cities with coordinates, country info,
     * and a pre-formatted display name for the Frontend dropdown.
     *
     * @param string $query Search query (city name or partial name)
     * @param int    $count Maximum number of results to return (default: 5)
     * @return array{
     *     query: string,
     *     count: int,
     *     results: array<int, array{
     *         id: int|null,
     *         name: string|null,
     *         latitude: float|null,
     *         longitude: float|null,
     *         country: string|null,
     *         country_code: string|null,
     *         admin1: string|null,
     *         timezone: string|null,
     *         display_name: string
     *     }>
     * }
     *
     * @throws ConnectionException When Open-Meteo Geocoding API is unreachable
     */
    public function searchCity(string $query, int $count = 5): array;
}
