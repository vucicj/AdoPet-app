<?php

namespace Database\Seeders;

use App\Models\Pet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $shelterId = 2;

        Pet::create([
            'name' => 'Max',
            'breed' => 'Golden Retriever',
            'age' => '2 years',
            'gender' => 'Male',
            'location' => 'Austin',
            'distance' => '8 miles',
            'image' => 'dog1.jpg',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Luna',
            'breed' => 'Cat',
            'age' => '1 year',
            'gender' => 'Female',
            'location' => 'Austin',
            'distance' => '5 miles',
            'image' => 'cat2.jpg',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Charlie',
            'breed' => 'Beagle',
            'age' => '3 years',
            'gender' => 'Male',
            'location' => 'Chicago',
            'distance' => '12 miles',
            'image' => 'dog2.webp',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Snowball',
            'breed' => 'Persian Cat',
            'age' => '4 years',
            'gender' => 'Female',
            'location' => 'Austin',
            'distance' => '7 miles',
            'image' => 'rabbit1.webp',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Buddy',
            'breed' => 'Border Collie',
            'age' => '2 years',
            'gender' => 'Male',
            'location' => 'Austin',
            'distance' => '4 miles',
            'image' => 'rabbit2.webp',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Misty',
            'breed' => 'Mixed Breed',
            'age' => '6 months',
            'gender' => 'Female',
            'location' => 'Portland',
            'distance' => '9 miles',
            'image' => 'bird1.jpg',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Tweet',
            'breed' => 'Parrot',
            'age' => '1 year',
            'gender' => 'Male',
            'location' => 'Austin',
            'distance' => '6 miles',
            'image' => 'bird2.jpg',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);

        Pet::create([
            'name' => 'Whiskers',
            'breed' => 'Parrot',
            'age' => '1 year',
            'gender' => 'Male',
            'location' => 'Austin',
            'distance' => '6 miles',
            'image' => 'cat1.jpg',
            'status' => 'available',
            'shelter_id' => $shelterId
        ]);
    }
}
