<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Kontrak untuk operasi geocoding (pencarian kota).
 *
 * Menjadi perantara (proxy) ke Open-Meteo Geocoding API untuk mencari kota
 * berdasarkan nama dan mengembalikan koordinat serta metadatanya.
 */
interface GeocodingServiceInterface
{
    /**
     * Mencari kota berdasarkan nama melalui Open-Meteo Geocoding API.
     *
     * Mengembalikan daftar kota yang cocok beserta koordinat, info negara,
     * dan nama tampilan (display name) yang sudah diformat untuk dropdown Frontend.
     *
     * @param string $query Kueri pencarian (nama kota atau sebagian nama)
     * @param int    $count Jumlah maksimum hasil yang dikembalikan (default: 5)
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
     * @throws ConnectionException Jika Open-Meteo Geocoding API tidak dapat dijangkau
     */
    public function searchCity(string $query, int $count = 5): array;
}
