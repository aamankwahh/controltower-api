<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aircraft;

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
    }
}
