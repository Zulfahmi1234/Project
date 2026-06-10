<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorites\StoreFavoriteRequest;
use App\Services\FavoriteLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteLocationController extends Controller
{
    public function __construct(
        private readonly FavoriteLocationService $favoriteLocationService,
    ) {}

    /**
     * Get all favorite locations for the authenticated user.
     *
     * GET /api/v1/favorites
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = $this->favoriteLocationService->getAll(
            userId: $request->user()->id,
        );

        return response()->json([
            'status' => 'success',
            'data'   => [
                'count'     => $favorites->count(),
                'favorites' => $favorites->map(fn ($fav) => [
                    'id'           => $fav->id,
                    'city_name'    => $fav->city_name,
                    'latitude'     => $fav->latitude,
                    'longitude'    => $fav->longitude,
                    'country'      => $fav->country,
                    'country_code' => $fav->country_code,
                    'timezone'     => $fav->timezone,
                    'created_at'   => $fav->created_at,
                ]),
            ],
        ], 200);
    }

    /**
     * Store a new favorite location.
     *
     * POST /api/v1/favorites
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $result = $this->favoriteLocationService->store(
            userId: $request->user()->id,
            data: $request->validated(),
        );

        if ($result['duplicate']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lokasi ini sudah ada di daftar favorit Anda.',
            ], 409);
        }

        $location = $result['location'];

        return response()->json([
            'status'  => 'success',
            'message' => 'Lokasi berhasil ditambahkan ke favorit.',
            'data'    => [
                'id'           => $location->id,
                'city_name'    => $location->city_name,
                'latitude'     => $location->latitude,
                'longitude'    => $location->longitude,
                'country'      => $location->country,
                'country_code' => $location->country_code,
                'timezone'     => $location->timezone,
                'created_at'   => $location->created_at,
            ],
        ], 201);
    }

    /**
     * Delete a favorite location.
     *
     * DELETE /api/v1/favorites/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $result = $this->favoriteLocationService->delete(
            userId: $request->user()->id,
            locationId: $id,
        );

        if (!$result['success']) {
            if ($result['reason'] === 'not_found') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Lokasi favorit tidak ditemukan.',
                ], 404);
            }

            if ($result['reason'] === 'forbidden') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk menghapus data ini.',
                ], 403);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Lokasi berhasil dihapus dari favorit.',
        ], 200);
    }
}
