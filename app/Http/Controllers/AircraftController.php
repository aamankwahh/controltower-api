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
        $allowable_states=config('constants.allowable_states');
        $state_actions = config('constants.state_actions');
        $state=$request->state; // get state value
        $callsign = $request->callsign;
       
        //get aircraft and tracker
        $tracker = Tracker::firstOrFail();
        $aircraft = Aircraft::where('callsign','=',$callsign)->firstOrFail();

       // return response()->json(["message"=>$state_actions['APPROACH']]);
        //check request is an action or a state
        if(in_array($state, $allowable_actions)){
            
            //it's an action
            $tracker_column= strtolower("can_".$state);
    
            if($tracker->$tracker_column){
                
                $aircraft->action=$state;
                $aircraft->state=null;
                $aircraft->save();
    
                $tracker->$tracker_column=false;
                $tracker->save();
    
                return response('No Content',204);
            }else{

                return response('Conflict',409);
            }
            
        }else if(in_array($state, $allowable_states)){
            //it's a state
            $tracker_column = "can_".$aircraft->action;
            $aircraft->action=null;
            $aircraft->state=$state;
            $aircraft->save();

            $tracker->$tracker_column=true;
            $tracker->save();


        }else
        {
            return response('Bad Request', 409);
        }
        
       
        
    }
    }

