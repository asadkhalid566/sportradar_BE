<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Red Bull Arena', 'city' => 'Salzburg'],
            ['name' => 'Vienna Rink', 'city' => 'Vienna'],
            ['name' => 'Wörthersee Stadium', 'city' => 'Klagenfurt'],
            ['name' => 'Tivoli Stadion Tirol', 'city' => 'Innsbruck'],
            ['name' => 'NV Arena', 'city' => 'St. Pölten'],
            ['name' => 'Linz Stadium', 'city' => 'Linz'],
            ['name' => 'UPC Arena', 'city' => 'Graz'],
            ['name' => 'Hypo-Arena', 'city' => 'Bregenz'],
            ['name' => 'Josko Arena', 'city' => 'Ried im Innkreis'],
            ['name' => 'Waldstadion', 'city' => 'Pasching'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate($loc);
        }
    }
}
