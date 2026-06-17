<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_weather_requires_authentication()
    {
        $response = $this->getJson('/api/v1/weather/current?latitude=5.5483&longitude=95.3238');
        $response->assertStatus(401);
    }

    public function test_can_get_current_weather()
    {
        $user = User::factory()->create();

        // Fake the HTTP request to Open-Meteo
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'current' => [
                    'time' => '2025-01-01T10:00',
                    'temperature_2m' => 31.2,
                    'apparent_temperature' => 34.5,
                    'relative_humidity_2m' => 78,
                    'wind_speed_10m' => 12.4,
                    'wind_direction_10m' => 180,
                    'weather_code' => 1,
                    'is_day' => 1,
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/weather/current?latitude=5.5483&longitude=95.3238&city_name=Banda+Aceh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'city',
                         'latitude',
                         'longitude',
                         'timezone',
                         'current' => [
                             'time',
                             'temperature',
                             'feels_like',
                             'humidity',
                             'wind_speed',
                             'wind_direction',
                             'condition',
                             'is_day',
                         ],
                         'units' => [
                             'temperature',
                             'wind_speed',
                             'humidity',
                         ]
                     ]
                 ]);
    }

    public function test_can_get_weather_forecast()
    {
        $user = User::factory()->create();

        // Fake the HTTP request to Open-Meteo
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'daily' => [
                    'time' => ['2025-01-01', '2025-01-02'],
                    'weather_code' => [1, 2],
                    'temperature_2m_max' => [33.5, 29.8],
                    'temperature_2m_min' => [24.1, 23.5],
                    'relative_humidity_2m_mean' => [76, 88],
                    'wind_speed_10m_max' => [18.2, 22.0],
                    'precipitation_sum' => [0.0, 4.5],
                    'precipitation_probability_max' => [10, 75],
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/weather/forecast?latitude=5.5483&longitude=95.3238&city_name=Banda+Aceh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'city',
                         'latitude',
                         'longitude',
                         'cached',
                         'forecast' => [
                             '*' => [
                                 'date',
                                 'day_name',
                                 'condition',
                                 'temperature_max',
                                 'temperature_min',
                                 'humidity_mean',
                                 'wind_speed_max',
                                 'precipitation_sum',
                                 'precipitation_probability_max',
                             ]
                         ],
                         'units'
                     ]
                 ]);
    }
}
