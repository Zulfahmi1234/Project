<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Carbon;

class OpenMeteoService
{
    /** Cache TTL for current weather: 10 minutes */
    const CACHE_TTL_CURRENT = 600;

    /** Cache TTL for forecast: 1 hour */
    const CACHE_TTL_FORECAST = 3600;

    /**
     * WMO Weather Code to Indonesian condition mapping.
     */
    const WEATHER_CODE_MAP = [
        0  => 'Cerah',
        1  => 'Cerah Berawan',
        2  => 'Cerah Berawan',
        3  => 'Cerah Berawan',
        45 => 'Berkabut',
        48 => 'Berkabut',
        51 => 'Gerimis',
        53 => 'Gerimis',
        55 => 'Gerimis',
        56 => 'Gerimis Beku',
        57 => 'Gerimis Beku',
        61 => 'Hujan',
        63 => 'Hujan',
        65 => 'Hujan',
        66 => 'Hujan Beku',
        67 => 'Hujan Beku',
        71 => 'Hujan Salju',
        73 => 'Hujan Salju',
        75 => 'Hujan Salju',
        77 => 'Butiran Salju',
        80 => 'Hujan Lebat',
        81 => 'Hujan Lebat',
        82 => 'Hujan Lebat',
        85 => 'Hujan Salju Ringan',
        86 => 'Hujan Salju Lebat',
        95 => 'Badai Petir',
        96 => 'Badai Petir dengan Hujan Es',
        99 => 'Badai Petir dengan Hujan Es',
    ];

    /**
     * Indonesian day name mapping.
     */
    const DAY_NAMES = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    /**
     * Get the base URL for Open-Meteo API.
     */
    private function getBaseUrl(): string
    {
        return config('services.open_meteo.base_url', 'https://api.open-meteo.com/v1');
    }

    /**
     * Get the base URL for Open-Meteo Geocoding API.
     */
    private function getGeocodingUrl(): string
    {
        return config('services.open_meteo.geocoding_url', 'https://geocoding-api.open-meteo.com/v1');
    }

    /**
     * Map WMO weather code to Indonesian condition string.
     */
    private function mapWeatherCode(int $code): string
    {
        return self::WEATHER_CODE_MAP[$code] ?? 'Tidak Diketahui';
    }

    /**
     * Get Indonesian day name from English day name.
     */
    private function getIndonesianDayName(string $date): string
    {
        $englishDay = Carbon::parse($date)->format('l');
        return self::DAY_NAMES[$englishDay] ?? $englishDay;
    }

    /**
     * Get current weather data.
     *
     * @param float  $lat      Latitude
     * @param float  $lng      Longitude
     * @param string $cityName City name for display
     * @return array Formatted weather data
     *
     * @throws ConnectionException
     */
    public function getCurrentWeather(float $lat, float $lng, string $cityName): array
    {
        $cacheKey = "weather_current_{$lat}_{$lng}";

        return Cache::remember($cacheKey, self::CACHE_TTL_CURRENT, function () use ($lat, $lng, $cityName) {
            $response = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])->timeout(10)->get($this->getBaseUrl() . '/forecast', [
                'latitude'        => $lat,
                'longitude'       => $lng,
                'current'         => 'temperature_2m,apparent_temperature,relative_humidity_2m,wind_speed_10m,wind_direction_10m,weather_code,is_day',
                'wind_speed_unit' => 'kmh',
                'timezone'        => 'auto',
            ]);

            if ($response->failed()) {
                throw new ConnectionException('Open-Meteo API tidak merespons.');
            }

            $data = $response->json();
            $current = $data['current'] ?? [];

            return [
                'city'      => $cityName,
                'latitude'  => $data['latitude'] ?? $lat,
                'longitude' => $data['longitude'] ?? $lng,
                'timezone'  => $data['timezone'] ?? 'auto',
                'current'   => [
                    'time'           => $current['time'] ?? null,
                    'temperature'    => $current['temperature_2m'] ?? null,
                    'feels_like'     => $current['apparent_temperature'] ?? null,
                    'humidity'       => $current['relative_humidity_2m'] ?? null,
                    'wind_speed'     => $current['wind_speed_10m'] ?? null,
                    'wind_direction' => $current['wind_direction_10m'] ?? null,
                    'condition'      => $this->mapWeatherCode((int) ($current['weather_code'] ?? -1)),
                    'is_day'         => (bool) ($current['is_day'] ?? true),
                ],
                'units' => [
                    'temperature' => '°C',
                    'wind_speed'  => 'km/h',
                    'humidity'    => '%',
                ],
            ];
        });
    }

    /**
     * Get 7-day weather forecast.
     *
     * @param float  $lat      Latitude
     * @param float  $lng      Longitude
     * @param string $cityName City name for display
     * @return array Formatted forecast data
     *
     * @throws ConnectionException
     */
    public function getForecast(float $lat, float $lng, string $cityName): array
    {
        $cacheKey = "weather_forecast_{$lat}_{$lng}";
        $isCached = Cache::has($cacheKey);

        $forecastData = Cache::remember($cacheKey, self::CACHE_TTL_FORECAST, function () use ($lat, $lng, $cityName) {
            $response = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])->timeout(10)->get($this->getBaseUrl() . '/forecast', [
                'latitude'        => $lat,
                'longitude'       => $lng,
                'daily'           => 'temperature_2m_max,temperature_2m_min,weather_code,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,relative_humidity_2m_mean',
                'wind_speed_unit' => 'kmh',
                'timezone'        => 'auto',
                'forecast_days'   => 7,
            ]);

            if ($response->failed()) {
                throw new ConnectionException('Open-Meteo API tidak merespons.');
            }

            $data = $response->json();
            $daily = $data['daily'] ?? [];
            $dates = $daily['time'] ?? [];

            $forecast = [];
            foreach ($dates as $i => $date) {
                $forecast[] = [
                    'date'                         => $date,
                    'day_name'                     => $this->getIndonesianDayName($date),
                    'condition'                    => $this->mapWeatherCode((int) ($daily['weather_code'][$i] ?? -1)),
                    'temperature_max'              => $daily['temperature_2m_max'][$i] ?? null,
                    'temperature_min'              => $daily['temperature_2m_min'][$i] ?? null,
                    'humidity_mean'                => $daily['relative_humidity_2m_mean'][$i] ?? null,
                    'wind_speed_max'               => $daily['wind_speed_10m_max'][$i] ?? null,
                    'precipitation_sum'            => $daily['precipitation_sum'][$i] ?? null,
                    'precipitation_probability_max' => $daily['precipitation_probability_max'][$i] ?? null,
                ];
            }

            return [
                'city'      => $cityName,
                'latitude'  => $data['latitude'] ?? $lat,
                'longitude' => $data['longitude'] ?? $lng,
                'forecast'  => $forecast,
                'units'     => [
                    'temperature'   => '°C',
                    'wind_speed'    => 'km/h',
                    'precipitation' => 'mm',
                ],
            ];
        });

        // Add cache metadata
        $forecastData['cached'] = $isCached;
        $forecastData['cache_expires_at'] = $isCached
            ? now()->addSeconds(self::CACHE_TTL_FORECAST)->toIso8601String()
            : null;

        return $forecastData;
    }

    /**
     * Search cities via Open-Meteo Geocoding API.
     *
     * @param string $query Search query
     * @param int    $count Number of results
     * @return array Formatted search results
     *
     * @throws ConnectionException
     */
    public function searchCity(string $query, int $count = 5): array
    {
        $response = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])->timeout(10)->get($this->getGeocodingUrl() . '/search', [
            'name'     => $query,
            'count'    => $count,
            'language' => 'id',
            'format'   => 'json',
        ]);

        if ($response->failed()) {
            throw new ConnectionException('Open-Meteo Geocoding API tidak merespons.');
        }

        $data = $response->json();
        $results = $data['results'] ?? [];

        $formatted = [];
        foreach ($results as $result) {
            $formatted[] = [
                'id'           => $result['id'] ?? null,
                'name'         => $result['name'] ?? null,
                'latitude'     => $result['latitude'] ?? null,
                'longitude'    => $result['longitude'] ?? null,
                'country'      => $result['country'] ?? null,
                'country_code' => $result['country_code'] ?? null,
                'admin1'       => $result['admin1'] ?? null,
                'timezone'     => $result['timezone'] ?? null,
                'display_name' => implode(', ', array_filter([
                    $result['name'] ?? null,
                    $result['admin1'] ?? null,
                    $result['country'] ?? null,
                ])),
            ];
        }

        return [
            'query'   => $query,
            'count'   => count($formatted),
            'results' => $formatted,
        ];
    }
}
