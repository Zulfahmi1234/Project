<?php

namespace App\Contracts;

use Illuminate\Http\Client\ConnectionException;

/**
 * Contract for weather data retrieval.
 *
 * Proxies Open-Meteo API to fetch current weather and 7-day forecast.
 * Implementations must cache responses to reduce third-party API load.
 *
 * Cache TTL guidelines:
 * - Current weather: 10 minutes
 * - Forecast: 1 hour
 */
interface WeatherServiceInterface
{
    /**
     * Get current weather data for a specific location.
     *
     * Returns formatted weather metrics including temperature, humidity,
     * wind speed/direction, and WMO weather condition translated to Indonesian.
     *
     * @param float  $lat      Latitude of the location
     * @param float  $lng      Longitude of the location
     * @param string $cityName City name for display in response
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
     * @throws ConnectionException When Open-Meteo API is unreachable
     */
    public function getCurrentWeather(float $lat, float $lng, string $cityName): array;

    /**
     * Get 7-day weather forecast for a specific location.
     *
     * Returns daily forecast data including temperature range, precipitation,
     * wind speed, and humidity. Response includes cache metadata.
     *
     * @param float  $lat      Latitude of the location
     * @param float  $lng      Longitude of the location
     * @param string $cityName City name for display in response
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
     * @throws ConnectionException When Open-Meteo API is unreachable
     */
    public function getForecast(float $lat, float $lng, string $cityName): array;
}
