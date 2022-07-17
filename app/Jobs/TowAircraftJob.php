<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Tracker;
use App\Models\ParkingSpot;
use App\Models\Aircraft;

class TowAircraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $aircraft;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Aircraft $aircraft)
    {
        //
        $this->aircraft=$aircraft;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $tracker = Tracker::firstOrFail();
        $spot=null;
        if($this->aircraft->type=="PRIVATE"){
            $spot = ParkingSpot::where('spot_type','SMALL')->where('available',1)->first();
            $tracker->small_spots_occupied -= 1;

        }else{
            $spot = ParkingSpot::where('spot_type','LARGE')->where('available',1)->first();
            $tracker->large_spots_occupied -= 1;

        }
        $spot->aircraft_id=$this->aircraft->id;
        $spot->available=false;
        $spot->save();

        $this->aircraft->state="PARKED";

        $this->aircraft->save();

    }
}
