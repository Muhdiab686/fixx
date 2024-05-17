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
       $team = ["team 1" ," team 2" , "team 3" , "team 4" , "team 5"];
       foreach ($team as $team1) {
         $Mteam = new Maintenance_team();
        $Mteam->name = $team1;
        $Mteam->save();
       }
    }
}
