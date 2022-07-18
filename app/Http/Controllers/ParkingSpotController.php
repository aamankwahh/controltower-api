<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingSpot;

class ParkingSpotController extends Controller
{
    //


    public function index(){

        $spots = ParkingSpot::select('spot_name','spot_type','callsign','available')
        ->leftJoin('aircraft','parking_spots.aircraft_id','=','aircraft.id')
        ->get();
//$query->join("aircraft", "traffic_logs.aircraft_id", "=", "aircraft.id");

        return response()->json(["parking_spots"=>$spots]);

    }
}
