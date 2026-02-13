<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Marija',
            'email' => 'marija@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Ivana',
            'email' => 'ivana@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Jelena',
            'email' => 'jelena@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'shelter',
        ]);

        $this->call([
            PetSeeder::class,
        ]);
    }
}
