<?php

namespace App\Services;

use App\Models\FavoriteLocation;
use Illuminate\Database\Eloquent\Collection;

class FavoriteLocationService
{
    /**
     * Get all favorite locations for a user.
     *
     * @param int $userId The authenticated user's ID
     * @return Collection<int, FavoriteLocation>
     */
    public function getAll(int $userId): Collection
    {
        return FavoriteLocation::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a new favorite location.
     *
     * @param int   $userId The authenticated user's ID
     * @param array $data   Validated location data
     * @return array{location: FavoriteLocation|null, duplicate: bool}
     */
    public function store(int $userId, array $data): array
    {
        // Check for duplicate
        $exists = FavoriteLocation::where('user_id', $userId)
            ->where('city_name', $data['city_name'])
            ->where('latitude', $data['latitude'])
            ->where('longitude', $data['longitude'])
            ->exists();

        if ($exists) {
            return ['location' => null, 'duplicate' => true];
        }

        $location = FavoriteLocation::create([
            'user_id'      => $userId,
            'city_name'    => $data['city_name'],
            'latitude'     => $data['latitude'],
            'longitude'    => $data['longitude'],
            'country'      => $data['country'],
            'country_code' => $data['country_code'],
            'timezone'     => $data['timezone'],
        ]);

        return ['location' => $location, 'duplicate' => false];
    }

    /**
     * Delete a favorite location.
     *
     * @param int $userId     The authenticated user's ID
     * @param int $locationId The favorite location ID
     * @return array{success: bool, reason: string|null}
     */
    public function delete(int $userId, int $locationId): array
    {
        $location = FavoriteLocation::find($locationId);

        if (!$location) {
            return ['success' => false, 'reason' => 'not_found'];
        }

        if ($location->user_id !== $userId) {
            return ['success' => false, 'reason' => 'forbidden'];
        }

        $location->delete();

        return ['success' => true, 'reason' => null];
    }
}
