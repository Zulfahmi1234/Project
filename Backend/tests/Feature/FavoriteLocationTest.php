<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_favorite_locations()
    {
        $user = User::factory()->create();
        
        FavoriteLocation::factory()->count(2)->create([
            'user_id' => $user->id
        ]);

        // Another user's favorite
        FavoriteLocation::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/favorites');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'count',
                         'favorites' => [
                             '*' => [
                                 'id',
                                 'city_name',
                                 'latitude',
                                 'longitude',
                                 'country',
                                 'country_code',
                                 'timezone',
                                 'created_at',
                             ]
                         ]
                     ]
                 ]);
        
        // Ensure it only returns the user's favorites
        $this->assertEquals(2, count($response->json('data.favorites')));
    }

    public function test_can_add_favorite_location()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/favorites', [
            'city_name' => 'Banda Aceh',
            'latitude' => 5.5483,
            'longitude' => 95.3238,
            'country' => 'Indonesia',
            'country_code' => 'ID',
            'timezone' => 'Asia/Jakarta',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Lokasi berhasil ditambahkan ke favorit.',
                 ]);

        $this->assertDatabaseHas('favorite_locations', [
            'user_id' => $user->id,
            'city_name' => 'Banda Aceh',
        ]);
    }

    public function test_cannot_add_duplicate_favorite_location()
    {
        $user = User::factory()->create();

        FavoriteLocation::factory()->create([
            'user_id' => $user->id,
            'city_name' => 'Banda Aceh',
            'latitude' => 5.5483,
            'longitude' => 95.3238,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/favorites', [
            'city_name' => 'Banda Aceh',
            'latitude' => 5.5483,
            'longitude' => 95.3238,
            'country' => 'Indonesia',
            'country_code' => 'ID',
            'timezone' => 'Asia/Jakarta',
        ]);

        $response->assertStatus(409);
    }

    public function test_can_delete_favorite_location()
    {
        $user = User::factory()->create();

        $favorite = FavoriteLocation::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson('/api/v1/favorites/' . $favorite->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('favorite_locations', [
            'id' => $favorite->id,
        ]);
    }
}
