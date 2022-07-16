<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParkingSpot;

class ParkingSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for ($i=400; $i<406 ; $i++) { 
            # code...
            $spot = new ParkingSpot();
            $spot->spot_name ="SPOT".$i;
            $spot->spot_type="SMALL";
            $spot->available=1;
            $spot->save();
        }

        for ($i=300; $i<311 ; $i++) { 
            # code...
            $spot = new ParkingSpot();
            $spot->spot_name ="SPOT".$i;
            $spot->spot_type="LARGE";
            $spot->available=1;
            $spot->save();
        }
       
    }
}
