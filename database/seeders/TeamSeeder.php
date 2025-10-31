<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Sport, Team};

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the sports exist before seeding teams
        $football = Sport::firstOrCreate(['name' => 'Football']);
        $iceHockey = Sport::firstOrCreate(['name' => 'Ice Hockey']);
        $cricket = Sport::firstOrCreate(['name' => 'Cricket']);

        $teams = [
            ['name' => 'Salzburg', '_sport_id' => $football->id],
            ['name' => 'Sturm', '_sport_id' => $football->id],
            ['name' => 'KAC', '_sport_id' => $iceHockey->id],
            ['name' => 'Capitals', '_sport_id' => $iceHockey->id],
            ['name' => 'Lahore Qalandars', '_sport_id' => $cricket->id],
            ['name' => 'Karachi Kings', '_sport_id' => $cricket->id],
        ];

        foreach ($teams as $team) {
            Team::firstOrCreate([
                'name' => $team['name'],
                '_sport_id' => $team['_sport_id'],
            ]);
        }
    }
}
