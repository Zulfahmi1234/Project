<?php

namespace App\Models;

use Database\Factories\FavoriteLocationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FavoriteLocation model — Fitur 5 (Manajemen Lokasi Favorit).
 *
 * Menyimpan kota-kota yang di-bookmark oleh user.
 * Unique constraint: (user_id, city_name, latitude, longitude).
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $city_name
 * @property float       $latitude
 * @property float       $longitude
 * @property string      $country
 * @property string      $country_code
 * @property string      $timezone
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property-read User   $user
 * @property-read string $display_name
 *
 * @method static Builder<static> forUser(int $userId)
 * @method static Builder<static> byCity(string $cityName)
 */
class FavoriteLocation extends Model
{
    /** @use HasFactory<FavoriteLocationFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'favorite_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'city_name',
        'latitude',
        'longitude',
        'country',
        'country_code',
        'timezone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'user_id'   => 'integer',
        ];
    }

    // ────────────────────────────────────────────
    //  Relationships
    // ────────────────────────────────────────────

    /**
     * Get the user that owns the favorite location.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ────────────────────────────────────────────
    //  Scopes
    // ────────────────────────────────────────────

    /**
     * Scope: filter favorites by user ID.
     *
     * @param Builder<static> $query
     */
    public function scopeForUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope: filter favorites by city name (partial match).
     *
     * @param Builder<static> $query
     */
    public function scopeByCity(Builder $query, string $cityName): void
    {
        $query->where('city_name', 'ILIKE', "%{$cityName}%");
    }

    // ────────────────────────────────────────────
    //  Accessors
    // ────────────────────────────────────────────

    /**
     * Get formatted display name: "City, Country".
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->city_name}, {$this->country}";
    }

    // ────────────────────────────────────────────
    //  Helpers
    // ────────────────────────────────────────────

    /**
     * Convert to array format for frontend map/flyTo consumption.
     *
     * @return array{id: int, city_name: string, latitude: float, longitude: float, country: string, country_code: string, timezone: string, display_name: string}
     */
    public function toMapData(): array
    {
        return [
            'id'           => $this->id,
            'city_name'    => $this->city_name,
            'latitude'     => (float) $this->latitude,
            'longitude'    => (float) $this->longitude,
            'country'      => $this->country,
            'country_code' => $this->country_code,
            'timezone'     => $this->timezone,
            'display_name' => $this->display_name,
        ];
    }
}
