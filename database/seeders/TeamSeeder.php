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
      ['name' => 'Team 1', 'latitude' => 33.5167, 'longitude' => 36.3167, 'title' => "دمشق الطبالة"],
      ['name' => 'Team 2', 'latitude' => 33.5089, 'longitude' => 36.3053, 'title' => "دمشق البرامكة"],
      ['name' => 'Team 3', 'latitude' => 33.5333, 'longitude' => 36.2500, 'title' => "دمشق المزة"],
      ['name' => 'Team 4', 'latitude' => 33.5225, 'longitude' => 36.3408, 'title' => "دمشق جديدة عرطوز"],
      ['name' => 'Team 5', 'latitude' => 33.5300, 'longitude' => 36.3167, 'title' => "دمشق مساكن برزة"]
    ];
    foreach ($teams as $team) {
      $Mteam = new Maintenance_team();
      $Mteam->name = $team['name'];
      $Mteam->latitude = $team['latitude'];
      $Mteam->longitude = $team['longitude'];
      $Mteam->title = $team['title'];
      $Mteam->save();
    }
  }
}
