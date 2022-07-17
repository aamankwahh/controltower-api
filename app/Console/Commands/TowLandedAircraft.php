<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aircraft;
use App\Models\ParkingSpot;
use App\Models\Tracker;
use Illuminate\Support\Facades\Log;

class TowLandedAircraft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aircraft:tow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tows aircraft';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return 0;
        $tracker = Tracker::firstOrFail();
        $aircraft = Aircraft::where('state','LANDED')->first();

        if($aircraft){

            $spot=null;
            if($aircraft->type=="PRIVATE"){
                $spot = ParkingSpot::where('spot_type','SMALL')->where('available',1)->first();
                $tracker->small_spots_occupied -= 1;
    
            }else{
               
                $spot = ParkingSpot::where('spot_type','LARGE')
                ->where('available',true)
                ->first();
                Log::info($spot);
               // $tracker->large_spots_occupied -= 1;
    
            }

        $spot->aircraft_id=$aircraft->id;
        $spot->available=false;

        $tracker->runway_available=true;
        
        $spot->save();

        $tracker->save();

        $aircraft->state="PARKED";

       
        $aircraft->save();
        }
       
       

    }
}
