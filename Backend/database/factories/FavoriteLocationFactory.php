<?php

namespace Database\Factories;

use App\Models\FavoriteLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FavoriteLocation>
 */
class FavoriteLocationFactory extends Factory
{
    protected $model = FavoriteLocation::class;

    /**
     * Predefined cities for realistic seeding.
     *
     * @var list<array{city_name: string, latitude: float, longitude: float, country: string, country_code: string, timezone: string}>
     */
    private const CITIES = [
        [
            'city_name'    => 'Jakarta',
            'latitude'     => -6.2088000,
            'longitude'    => 106.8456000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Surabaya',
            'latitude'     => -7.2575000,
            'longitude'    => 112.7521000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Bandung',
            'latitude'     => -6.9175000,
            'longitude'    => 107.6191000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Medan',
            'latitude'     => 3.5952000,
            'longitude'    => 98.6722000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Makassar',
            'latitude'     => -5.1477000,
            'longitude'    => 119.4327000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Makassar',
        ],
        [
            'city_name'    => 'Semarang',
            'latitude'     => -6.9666000,
            'longitude'    => 110.4196000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Yogyakarta',
            'latitude'     => -7.7956000,
            'longitude'    => 110.3695000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Jakarta',
        ],
        [
            'city_name'    => 'Denpasar',
            'latitude'     => -8.6500000,
            'longitude'    => 115.2167000,
            'country'      => 'Indonesia',
            'country_code' => 'ID',
            'timezone'     => 'Asia/Makassar',
        ],
        [
            'city_name'    => 'Tokyo',
            'latitude'     => 35.6762000,
            'longitude'    => 139.6503000,
            'country'      => 'Jepang',
            'country_code' => 'JP',
            'timezone'     => 'Asia/Tokyo',
        ],
        [
            'city_name'    => 'Singapore',
            'latitude'     => 1.3521000,
            'longitude'    => 103.8198000,
            'country'      => 'Singapura',
            'country_code' => 'SG',
            'timezone'     => 'Asia/Singapore',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->randomElement(self::CITIES);

        return [
            'user_id'      => User::factory(),
            'city_name'    => $city['city_name'],
            'latitude'     => $city['latitude'],
            'longitude'    => $city['longitude'],
            'country'      => $city['country'],
            'country_code' => $city['country_code'],
            'timezone'     => $city['timezone'],
        ];
    }

    /**
     * Set a specific city for the favorite.
     */
    public function city(string $cityName): static
    {
        $city = collect(self::CITIES)->firstWhere('city_name', $cityName);

        if (!$city) {
            return $this;
        }

        return $this->state(fn () => $city);
    }

    /**
     * Assign the favorite to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}
