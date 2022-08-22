<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aircraft;
use App\Models\ParkingSpot;
use App\Models\RequestLog;
use App\Models\Tracker;
use App\Models\RequestLog;
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
               
               // $tracker->large_spots_occupied -= 1;
    
            }

        $spot->aircraft_id=$aircraft->id;
        $spot->available=false;
        $tracker->runway_available=true;
        $tracker->can_landed=true;
        $spot->save();
        $tracker->save();
        $aircraft->state="PARKED";
        $aircraft->save();

<<<<<<< HEAD
        $request_log = new RequestLog();
        $request_log->request_type="STATE_CHANGE";
        $request_log->action="TOW";
        $request_log->status=1;
        $request_log->aircraft_id=$aircraft->id;
        $request_log->save();
=======
        $log = new RequestLog();
        $log->aircraft_id = $aircraft->id;
        $log->request_type = "STATE_CHANGE";
        $log->status = 1;
        $log->action="PARKED";
        $log->save();
>>>>>>> 0a05e33775c543919e831fb002308b00e03c6ca0
        }
       
       

    }
}
