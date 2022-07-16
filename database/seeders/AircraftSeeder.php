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
        $craft1 = new Aircraft();
        $craft1->type="AIRLINER";
        $craft1->callsign="AR101";
        $craft1->state="PARKED";
        $craft1->save();

        $craft2 = new Aircraft();
        $craft2->type="AIRLINER";
        $craft2->callsign="AR102";
        $craft2->state="AIRBORNE";
        $craft2->save();


        $tracker = Tracker::firstOrFail();

        $tracker->large_spots_occupied=2;
        $tracker->save();
    }
}
