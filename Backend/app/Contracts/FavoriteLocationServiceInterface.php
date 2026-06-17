<?php

namespace App\Contracts;

use App\Models\FavoriteLocation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for favorite location management.
 *
 * Handles CRUD operations for user's bookmarked locations.
 * All operations are scoped to the authenticated user's ID
 * to enforce ownership.
 */
interface FavoriteLocationServiceInterface
{
    /**
     * Get all favorite locations for a user.
     *
     * Results are ordered by creation date (newest first).
     *
     * @param int $userId The authenticated user's ID
     * @return Collection<int, FavoriteLocation>
     */
    public function getAll(int $userId): Collection;

    /**
     * Store a new favorite location.
     *
     * Checks for duplicates based on (user_id, city_name, latitude, longitude).
     * If a duplicate exists, returns duplicate flag without creating a new record.
     *
     * @param int   $userId The authenticated user's ID
     * @param array{
     *     city_name: string,
     *     latitude: float,
     *     longitude: float,
     *     country: string,
     *     country_code: string,
     *     timezone: string
     * } $data Validated location data
     * @return array{location: FavoriteLocation|null, duplicate: bool}
     */
    public function store(int $userId, array $data): array;

    /**
     * Delete a favorite location by ID.
     *
     * Verifies ownership before deletion. Returns result with
     * success flag and failure reason if applicable.
     *
     * @param int $userId     The authenticated user's ID
     * @param int $locationId The favorite location ID to delete
     * @return array{success: bool, reason: string|null}
     *     reason can be: 'not_found' | 'forbidden' | null (on success)
     */
    public function delete(int $userId, int $locationId): array;
}
