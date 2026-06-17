<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\WeatherServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Weather\GetWeatherRequest;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherServiceInterface $weatherService,
    ) {}

    /**
     * Get current weather data.
     *
     * GET /api/v1/weather/current
     */
    public function current(GetWeatherRequest $request): JsonResponse
    {
        $data = $this->weatherService->getCurrentWeather(
            lat: (float) $request->validated('latitude'),
            lng: (float) $request->validated('longitude'),
            cityName: $request->validated('city_name'),
        );

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }

    /**
     * Get 7-day weather forecast.
     *
     * GET /api/v1/weather/forecast
     */
    public function forecast(GetWeatherRequest $request): JsonResponse
    {
        $data = $this->weatherService->getForecast(
            lat: (float) $request->validated('latitude'),
            lng: (float) $request->validated('longitude'),
            cityName: $request->validated('city_name'),
        );

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}
