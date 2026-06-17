<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User model — Fitur 1 (Autentikasi).
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $email_verified_at
 * @property string      $password
 * @property string|null $remember_token
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FavoriteLocation> $favoriteLocations
 * @property-read int $favorites_count
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ────────────────────────────────────────────
    //  Relationships
    // ────────────────────────────────────────────

    /**
     * Get the favorite locations for the user.
     *
     * @return HasMany<FavoriteLocation, $this>
     */
    public function favoriteLocations(): HasMany
    {
        return $this->hasMany(FavoriteLocation::class);
    }

    // ────────────────────────────────────────────
    //  Helpers
    // ────────────────────────────────────────────

    /**
     * Check if a specific location is already in user's favorites.
     *
     * @param string $cityName  City name to check
     * @param float  $latitude  Latitude of the location
     * @param float  $longitude Longitude of the location
     */
    public function hasFavorite(string $cityName, float $latitude, float $longitude): bool
    {
        return $this->favoriteLocations()
            ->where('city_name', $cityName)
            ->where('latitude', $latitude)
            ->where('longitude', $longitude)
            ->exists();
    }
}
