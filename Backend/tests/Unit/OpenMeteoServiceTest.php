<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OpenMeteoService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;

class OpenMeteoServiceTest extends TestCase
{
    protected OpenMeteoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OpenMeteoService();
        Cache::flush();
    }

    public function test_get_current_weather_success()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'latitude' => -6.2,
                'longitude' => 106.8,
                'timezone' => 'Asia/Jakarta',
                'current' => [
                    'time' => '2026-06-25T01:00',
                    'temperature_2m' => 28.5,
                    'apparent_temperature' => 31.2,
                    'relative_humidity_2m' => 75,
                    'wind_speed_10m' => 12.5,
                    'wind_direction_10m' => 180,
                    'weather_code' => 3,
                    'is_day' => 0,
                ]
            ], 200)
        ]);

        $result = $this->service->getCurrentWeather(-6.2, 106.8, 'Jakarta');

        $this->assertEquals('Jakarta', $result['city']);
        $this->assertEquals(28.5, $result['current']['temperature']);
        $this->assertEquals('Cerah Berawan', $result['current']['condition']);
        $this->assertFalse($result['current']['is_day']);
        $this->assertTrue(Cache::has("weather_current_-6.2_106.8"));
    }

    public function test_get_current_weather_throws_exception_on_failure()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response([], 500)
        ]);

        $this->expectException(ConnectionException::class);
        $this->service->getCurrentWeather(-6.2, 106.8, 'Jakarta');
    }

    public function test_get_forecast_success()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'latitude' => -6.2,
                'longitude' => 106.8,
                'daily' => [
                    'time' => ['2026-06-25', '2026-06-26'],
                    'weather_code' => [61, 3],
                    'temperature_2m_max' => [32.0, 33.5],
                    'temperature_2m_min' => [25.0, 24.5],
                    'relative_humidity_2m_mean' => [80, 70],
                    'wind_speed_10m_max' => [15.0, 10.0],
                    'precipitation_sum' => [5.5, 0],
                    'precipitation_probability_max' => [90, 10],
                ]
            ], 200)
        ]);

        $result = $this->service->getForecast(-6.2, 106.8, 'Jakarta');

        $this->assertEquals('Jakarta', $result['city']);
        $this->assertCount(2, $result['forecast']);
        $this->assertEquals('Hujan', $result['forecast'][0]['condition']);
        $this->assertEquals(32.0, $result['forecast'][0]['temperature_max']);
        $this->assertFalse($result['cached']);
        
        // Second call should hit cache
        $cachedResult = $this->service->getForecast(-6.2, 106.8, 'Jakarta');
        $this->assertTrue($cachedResult['cached']);
    }

    public function test_search_city_success()
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'id' => 123,
                        'name' => 'Jakarta',
                        'latitude' => -6.2146,
                        'longitude' => 106.8451,
                        'country' => 'Indonesia',
                        'country_code' => 'ID',
                        'admin1' => 'Jakarta',
                        'timezone' => 'Asia/Jakarta',
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->searchCity('Jakarta');

        $this->assertEquals('Jakarta', $result['query']);
        $this->assertEquals(1, $result['count']);
        $this->assertEquals('Jakarta', $result['results'][0]['name']);
        $this->assertEquals('Jakarta, Jakarta, Indonesia', $result['results'][0]['display_name']);
    }

    public function test_search_city_empty_results()
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([], 200)
        ]);

        $result = $this->service->searchCity('UnknownCity');

        $this->assertEquals('UnknownCity', $result['query']);
        $this->assertEquals(0, $result['count']);
        $this->assertEmpty($result['results']);
    }
}
