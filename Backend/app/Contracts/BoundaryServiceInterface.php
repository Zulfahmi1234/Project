<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Kontrak untuk pengambilan batas (boundary) wilayah GeoJSON.
 *
 * Menjadi perantara ke Nominatim API untuk mengambil poligon batas administratif
 * guna divisualisasikan pada peta (map) kota/wilayah.
 *
 * PENTING: Nominatim memiliki batas laju (rate limit) 1 request/detik.
 * Implementasi WAJIB melakukan cache hasil selama 24 jam.
 * Format key cache: boundary_{query_normalized}
 */
interface BoundaryServiceInterface
{
    /**
     * Mengambil poligon batas GeoJSON untuk suatu kota/wilayah.
     *
     * Mengembalikan objek GeoJSON Feature beserta poligon batas
     * dan metadatanya. Mengembalikan null jika batas tidak ditemukan.
     * Hasil null TIDAK BOLEH di-cache.
     *
     * @param string $query Nama kota atau wilayah yang dicari
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
     * }|null Null jika tidak ada batas wilayah yang ditemukan dari pencarian
     *
     * @throws ConnectionException Jika Nominatim API tidak dapat dijangkau
     */
    public function getBoundary(string $query): ?array;
}
