<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Kontrak untuk pengambilan data cuaca.
 *
 * Menjadi perantara ke Open-Meteo API untuk mengambil data cuaca saat ini dan prakiraan 7 hari ke depan.
 * Implementasi wajib melakukan cache terhadap respon untuk mengurangi beban API pihak ketiga.
 *
 * Panduan waktu hidup cache (TTL):
 * - Cuaca saat ini: 10 menit
 * - Prakiraan cuaca: 1 jam
 */
interface WeatherServiceInterface
{
    /**
     * Mengambil data cuaca saat ini untuk suatu lokasi spesifik.
     *
     * Mengembalikan metrik cuaca yang sudah diformat termasuk suhu, kelembapan,
     * kecepatan/arah angin, dan kondisi cuaca WMO yang telah diterjemahkan ke Bahasa Indonesia.
     *
     * @param float  $lat      Garis lintang (Latitude) lokasi
     * @param float  $lng      Garis bujur (Longitude) lokasi
     * @param string $cityName Nama kota untuk ditampilkan pada respon
     * @return array{
     *     city: string,
     *     latitude: float,
     *     longitude: float,
     *     timezone: string,
     *     current: array{
     *         time: string|null,
     *         temperature: float|null,
     *         feels_like: float|null,
     *         humidity: int|null,
     *         wind_speed: float|null,
     *         wind_direction: float|null,
     *         condition: string,
     *         is_day: bool
     *     },
     *     units: array{
     *         temperature: string,
     *         wind_speed: string,
     *         humidity: string
     *     }
     * }
     *
     * @throws ConnectionException Jika Open-Meteo API tidak dapat dijangkau
     */
    public function getCurrentWeather(float $lat, float $lng, string $cityName): array;

    /**
     * Mengambil prakiraan cuaca 7 hari untuk suatu lokasi spesifik.
     *
     * Mengembalikan data prakiraan harian termasuk rentang suhu, curah hujan,
     * kecepatan angin, dan kelembapan. Respon menyertakan metadata cache.
     *
     * @param float  $lat      Garis lintang (Latitude) lokasi
     * @param float  $lng      Garis bujur (Longitude) lokasi
     * @param string $cityName Nama kota untuk ditampilkan pada respon
     * @return array{
     *     city: string,
     *     latitude: float,
     *     longitude: float,
     *     forecast: array<int, array{
     *         date: string,
     *         day_name: string,
     *         condition: string,
     *         temperature_max: float|null,
     *         temperature_min: float|null,
     *         humidity_mean: int|null,
     *         wind_speed_max: float|null,
     *         precipitation_sum: float|null,
     *         precipitation_probability_max: int|null
     *     }>,
     *     units: array{
     *         temperature: string,
     *         wind_speed: string,
     *         precipitation: string
     *     },
     *     cached: bool,
     *     cache_expires_at: string|null
     * }
     *
     * @throws ConnectionException Jika Open-Meteo API tidak dapat dijangkau
     */
    public function getForecast(float $lat, float $lng, string $cityName): array;
}
