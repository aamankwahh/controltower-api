<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestLog;

class RequestLogController extends Controller
{
    //
    public function getUpdates(){
        //try catch
        $logs=RequestLog::select("request_type","request_logs.action as requested_action"
        ,"callsign","status","request_logs.created_at as creation_date","request_logs.updated_at as last_updated")
        ->join("aircraft", "request_logs.aircraft_id", "=", "aircraft.id")
        ->orderBy('request_logs.id', 'desc')
        ->take(10)
        ->get();

      

        return response()->json(["logs"=>$logs]);
        
    }
}
