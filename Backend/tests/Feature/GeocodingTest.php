<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_city()
    {
        $user = User::factory()->create();

        // Fake the HTTP request to Open-Meteo
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    [
                        'id' => 1214026,
                        'name' => 'Banda Aceh',
                        'latitude' => 5.5483,
                        'longitude' => 95.3238,
                        'country' => 'Indonesia',
                        'country_code' => 'ID',
                        'admin1' => 'Aceh',
                        'timezone' => 'Asia/Jakarta',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/geocoding/search?q=Banda+Aceh&count=5');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'query',
                         'count',
                         'results' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'latitude',
                                 'longitude',
                                 'country',
                                 'country_code',
                                 'admin1',
                                 'timezone',
                                 'display_name',
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_get_city_boundary()
    {
        $user = User::factory()->create();

        // Fake the HTTP request to Nominatim
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'place_id' => 298765432,
                    'osm_id' => 3629770,
                    'display_name' => 'Banda Aceh, Aceh, Indonesia',
                    'boundingbox' => ['5.4921', '5.6083', '95.2615', '95.4065'],
                    'geojson' => [
                        'type' => 'Polygon',
                        'coordinates' => [
                            [
                                [95.2615, 5.4921],
                                [95.4065, 5.4921],
                                [95.4065, 5.6083],
                                [95.2615, 5.6083],
                                [95.2615, 5.4921]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/geocoding/boundary?q=Banda+Aceh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'query',
                         'cached',
                         'boundary' => [
                             'type',
                             'properties' => [
                                 'display_name',
                                 'osm_id',
                                 'place_id',
                                 'boundingbox',
                             ],
                             'geometry' => [
                                 'type',
                                 'coordinates',
                             ]
                         ]
                     ]
                 ]);
    }
}
