<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            'Football',
            'Ice Hockey',
            'Cricket',
            'Tennis',
            'Basketball',
        ];

        foreach ($sports as $name) {
            Sport::firstOrCreate(['name' => $name]);
        }
    }
}
