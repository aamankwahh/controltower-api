<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use App\Models\RequestLog;
use App\Models\Aircraft;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getWeatherInfo(Request $request){

        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign',$callsign)->first();

        $request_log = new RequestLog();
        $request_log->request_type="WEATHER_INFO";

        if($aircraft){
            $request_log->aircraft_id=$aircraft->id;
        }else{
            return response('Not valid',500);
        }
        

        try {
            
            $weather_record=Weather::latest()->first();

            if($weather_record){
                $weather_info = json_decode($weather_record->response);
                $request_log->status=1;
                $request_log->save();
                return response()->json($weather_info);
            }else{
                $request_log->status=0;
                $request_log->save();
                return response()->json(["message"=>"No records available"]);
            }

           
        } catch (\Throwable $th) {
            throw $th;
            //return response('Error',500);
        }

       

    }
}
