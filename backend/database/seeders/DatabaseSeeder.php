<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a general test user (idempotent)
        User::firstOrCreate([
            'email' => 'marija@gmail.com',
        ], [
            'name' => 'Marija',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Ensure a shelter user exists for seeded pets
        User::firstOrCreate([
            'email' => 'jelena@gmail.com',
        ], [
            'name' => 'Jelena',
            'password' => bcrypt('password'),
            'role' => 'shelter',
        ]);

        $this->call([
            PetSeeder::class,
        ]);
    }
}
