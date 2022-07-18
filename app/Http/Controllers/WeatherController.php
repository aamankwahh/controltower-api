<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use App\Models\RequestLog;
use App\Models\Aircraft;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    //--------SWAGGER DOCUMENTATION START-------------------
   /**
 * @OA\Get(
 *     path="/api/public/{callsign}/weather",
 *      tags={"Request Weather Update"},
 *     summary="Requests weather update from Control Tower",
 *     @OA\Parameter(
 *         description="Aircraft's call sign",
 *         in="path",
 *         name="callsign",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         @OA\Examples(example="string", value="AR101", summary="Airliner - Current State: PARKED"),
 * 
 *         
 * 
 *     ),
 *
 *       
 *  
 *     @OA\Response(
 *         response=204,
 *         description="OK"
 *     ),
 *  @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     ),
 * 
 *  
 * )
 */
//--------SWAGGER DOCUMENTATION END-------------------
    public function getWeatherInfo(Request $request){

        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign',$callsign)->first();

        $request_log = new RequestLog();
        $request_log->request_type="WEATHER_INFO";
        $request_log->action="Weather";

        if($aircraft){
            $request_log->aircraft_id=$aircraft->id;
        }else{
            return response('Not valid',500);
        }
        

        try {
            
            $weather_record=Weather::latest()->first();

            if($weather_record){
                $weather_info = json_decode($weather_record->response,true);


                $description=$weather_info['weather'][0]['description'];
                $temp=$weather_info['main']['temp'];

                $visibility=$weather_info['visibility'];
                $wind_speed=$weather_info['wind']['speed'];

                $wind_deg=$weather_info['wind']['deg'];
                
                $weather_response=array(
                    "description"=>$description,
                    "temperature"=>$temp,
                     "visibility"=>$visibility,
                    "wind"=>array(
                        "speed"=>$wind_speed,
                        "deg"=>$wind_deg
                    ),
                    "last_update"=>$weather_record->updated_at);
                $request_log->status=1;
               
                $request_log->save();
                return response()->json($weather_response);
            }else{
                $request_log->status=0;
                
                $request_log->save();
                return response()->json(["message"=>"No records available"],204);
            }

           
        } catch (\Throwable $th) {
            throw $th;
            return response('Error.Unable to retrieve info',500);
        }

       

    }
}
