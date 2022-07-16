<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aircraft;
use App\Models\Tracker;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $tracker = Tracker::firstOrFail();
        for ($i=100; $i <104 ; $i++) { 
            # code...
            $craft = new Aircraft();
            $craft->type="AIRLINER";
            $craft->callsign="AR".$i;
            $craft->state="PARKED";
            $craft->save();

            $tracker->large_spots_occupied+=1;
            $tracker->save();
        }

        //SMALL planes
        for ($i=200; $i <208 ; $i++) { 
            # code...
            $craft = new Aircraft();
            $craft->type="PRIVATE";
            $craft->callsign="AR".$i;
            $craft->state="PARKED";
            $craft->save();

            $tracker->small_spots_occupied+=1;
            $tracker->save();
        }


         //SMALL planes
         for ($i=104; $i <107 ; $i++) { 
            # code...
            $craft = new Aircraft();
            $craft->type="AIRLINER";
            $craft->callsign="AR".$i;
            $craft->state="AIRBORNE";
            $craft->save();
        }

        for ($i=208; $i <212 ; $i++) { 
            # code...
            $craft = new Aircraft();
            $craft->type="PRIVATE";
            $craft->callsign="AR".$i;
            $craft->state="AIRBORNE";
            $craft->save();
        }


        for ($i=104; $i <107 ; $i++) { 
            # code...
            $craft = new Aircraft();
            $craft->type="PRIVATE";
            $craft->callsign="AR".$i;
            $craft->state="AIRBORNE";
            $craft->save();
        }
       

       

       
    }
}
