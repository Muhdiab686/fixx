<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maintenance_team;
class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
  public function run()
  {
    $teams = [
      ['name' => 'Damascus', 'latitude' => 33.5138073, 'longitude' => 36.2765279],
      ['name' => 'Aleppo', 'latitude' => 36.2021047, 'longitude' => 37.1342603],
      ['name' => 'Homs', 'latitude' => 34.7324278, 'longitude' => 36.7134505],
      ['name' => 'Latakia', 'latitude' => 35.5291266, 'longitude' => 35.789562],
      ['name' => 'Tartus', 'latitude' => 34.895851, 'longitude' => 35.8865959]
    ];
    foreach ($teams as $team) {
      $Mteam = new Maintenance_team();
      $Mteam->name = $team['name'];
      $Mteam->latitude = $team['latitude'];
      $Mteam->longitude = $team['longitude'];
      $Mteam->save();
    }
  }
}
