<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Geocoding\SearchCityRequest;
use App\Http\Requests\Geocoding\GetBoundaryRequest;
use App\Services\OpenMeteoService;
use App\Services\NominatimService;
use Illuminate\Http\JsonResponse;

class GeocodingController extends Controller
{
    public function __construct(
        private readonly OpenMeteoService $openMeteoService,
        private readonly NominatimService $nominatimService,
    ) {}

    /**
     * Search cities via Open-Meteo Geocoding.
     *
     * GET /api/v1/geocoding/search
     */
    public function search(SearchCityRequest $request): JsonResponse
    {
        $data = $this->openMeteoService->searchCity(
            query: $request->validated('q'),
            count: $request->integer('count', 5),
        );

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }

    /**
     * Get GeoJSON boundary for a city.
     *
     * GET /api/v1/geocoding/boundary
     */
    public function boundary(GetBoundaryRequest $request): JsonResponse
    {
        $data = $this->nominatimService->getBoundary(
            query: $request->validated('q'),
        );

        if ($data === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Wilayah tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}
