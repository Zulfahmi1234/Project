<?php

namespace Database\Seeders;

use App\Models\FavoriteLocation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create favorite locations for test user
        FavoriteLocation::factory()->forUser($user)->city('Jakarta')->create();
        FavoriteLocation::factory()->forUser($user)->city('Bandung')->create();
        FavoriteLocation::factory()->forUser($user)->city('Denpasar')->create();
    }
}
