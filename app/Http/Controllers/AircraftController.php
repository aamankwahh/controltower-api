<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\KeyPair;
use Spatie\Crypto\Rsa\PrivateKey;
use Spatie\Crypto\Rsa\PublicKey;
use Illuminate\Support\Facades\Storage;
use App\Models\Aircraft;
use App\Models\Tracker;

class AircraftController extends Controller
{
    //

    public function updateLocation(Request $request){

    }

    public function generateKey(){

        //Storage::disk('local')->put('example.txt', 'Contents');
        Storage::disk('local')->put('example.txt', 'Contents');
        $url = Storage::url('/s');
        //$contents = Storage::get('oauth-public.key');
        // $privateKey=null;
        // $publicKey=null;
        // // generating an RSA key pair
        // [$privateKey, $publicKey] = (new KeyPair())->generate();


        return response()->json(["public"=>$url]);

    }

    public function setState(Request $request){

        $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs
        $state_actions = config('constants.state_actions');
        $state=$request->state; // get state value
        $callsign = $request->callsign;
        $next_state=$state_actions[$state];

       // return response()->json(["message"=>$state_actions['APPROACH']]);

        if(!in_array($state, $allowable_actions)){
            return response('Bad Request', 409);
        }
        
        try {
          
        
        $tracker_column= strtolower("can_".$state);
       
        $tracker = Tracker::firstOrFail();
        $aircraft = Aircraft::where('callsign','=',$callsign)->firstOrFail();

        if($tracker->$tracker_column){
            //check weather
            $aircraft->state=$next_state;
            $aircraft->save();

            $tracker->$tracker_column=false;
            $tracker->save();

            return response('No Content',204);
        }else{

            return response('Conflict',409);
        }
        
       // return response()->json(["col"=>$tracker_column]);
        } catch (\Throwable $th) {
            //throw $th;
            return response('Unable to process',500);
        }

    
        
    }
}
