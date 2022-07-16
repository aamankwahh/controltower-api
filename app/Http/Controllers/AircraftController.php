<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class AircraftController extends Controller
{
    //

    public function updateLocation(Request $request)
    {

    }

    public function generateKey()
    {

        //Storage::disk('local')->put('example.txt', 'Contents');
        Storage::disk('local')->put('example.txt', 'Contents');
        $url = Storage::url('/s');
        //$contents = Storage::get('oauth-public.key');
        // $privateKey=null;
        // $publicKey=null;
        // // generating an RSA key pair
        // [$privateKey, $publicKey] = (new KeyPair())->generate();

        return response()->json(["public" => $url]);

    }

    /**
     * @OA\Get(
     *     path="/{callsign}/intent",
     *     tags={"callsign"},
     *     summary="Returns pet inventories by status",
     *     description="Returns a map of status codes to quantities",
     *     operationId="getInventory",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\AdditionalProperties(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         )
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */

    public function setState(Request $request)
    {

        $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs

        $state = $request->state; // get state value
        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign', '=', $callsign)->firstOrFail();

        $tracker = Tracker::firstOrFail();

        
        if ($this->actionIsNotAllowed($state, $aircraft, $allowable_actions)) {
            return response('Conflict', 409);
        }

        $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);

        if ($state == "APPROACH" && !$spot_is_available) {
            return response('Conflict', 409);
        }

        $tracker_column = strtolower("can_" . $state);

        //
        if (Schema::hasColumn('trackers', $tracker_column)) {

            $is_allowed = $tracker->$tracker_column;

            if ($is_allowed && $tracker->runway_available) {

                $aircraft->state = $state;
                $aircraft->save();

                $tracker->$tracker_column = false;
                $tracker->runway_available = false;

                $tracker->save();

                return response('No Content', 204);
            } else {
                return response('Conflict', 409);
            }

        } else {

            $tracker_column = strtolower("can_" . $aircraft->state);

            //return response($tracker->$tracker_column,200);
            if (Schema::hasColumn('trackers', $tracker_column)); //check whether  table has column
            {
                
                if ($tracker->$tracker_column == 0) {

                    $tracker->$tracker_column = true;
                    $tracker->save();
    
                }
            }
           

            $aircraft->state = $state;
            $aircraft->save();

            if ($state == "PARKED") {
                if ($aircraft->type == "PRIVATE") {
                    $tracker->small_spots_occupied += 1;
                } else {
                    $tracker->large_spots_occupied += 1;
                }
            }

            if ($state == "TAKEOFF") {
                if ($aircraft->type == "PRIVATE") {
                    $tracker->small_spots_occupied -= 1;
                } else {
                    $tracker->large_spots_occupied -= 1;
                }
            }

            // Landed means aircraft is using the runway
            if ($state != "LANDED") {
                $tracker->runway_available = true;
            }

            $tracker->save();
            return response('No Content', 204);

        }

        // if (Schema::hasColumn('users', 'email')); //check whether users table has email column
        // {
        //     //your logic
        // }

        // return response()->json($allowable_actions[$state]['next_state']);

        // //check request is an action or a state
        // if (array_key_exists($state, $allowable_actions)) {
        //     //it's an action

        //     $action = $state; // assign state as action
        //     //check if aircraft is already in required state
        //     if ($aircraft->state == $allowable_actions[$action]) {
        //         return response('Conflict', 409);
        //     }

        //     $tracker_column = strtolower("can_" . $action);

        //     $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);

        //     ///
        //     if ($state == "APPROACH" && !$spot_is_available) {
        //         return response('Conflict', 409);
        //     }

        //     if ($tracker->$tracker_column && $tracker->runway_available) {

        //         $aircraft->action = $action;
        //         $aircraft->state = null;
        //         $aircraft->save();
        //         $tracker->$tracker_column = false;
        //         $tracker->runway_available = false;

        //         $tracker->save();

        //         return response('OK', 204);
        //     } else {

        //         return response('Conflict', 409);
        //     }

        // } else if (array_key_exists($state, $allowable_states)) { //LANDED, AIRBORNE, PARKED
        //     //it's a state
        //     // if ($aircraft->action == $allowable_states[$state]) {
        //     //     return response('Conflict', 409);
        //     // }

        //     if($state=="PARKED"){
        //         return response('Conflict', 409);
        //     }

        //     $tracker_column = "can_" . $aircraft->action;
        //     $aircraft->action = null;
        //     $aircraft->state = $state;
        //     $aircraft->save();

        //     $tracker->$tracker_column = true;

        //     $tracker->$tracker_column = false;

        //     if ($state == "PARKED") {
        //         if ($aircraft->type == "PRIVATE") {
        //             $tracker->small_spots_occupied += 1;
        //         } else {
        //             $tracker->large_spots_occupied += 1;
        //         }
        //     }

        //     if ($state != "LANDED") {
        //         $tracker->runway_available = true;
        //     }

        //     $tracker->save();
        //     return response('No Content', 204);

        // } else {
        //     return response('Bad Request', 409);
        // }

    }

    // public function setState2(Request $request)
    // {

    //     $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs
    //     $allowable_states = config('constants.allowable_states');
    //     $state_actions = config('constants.state_actions');
    //     $state = $request->state; // get state value
    //     $callsign = $request->callsign;

    //     //get aircraft and tracker
    //     $tracker = Tracker::firstOrFail();
    //     $aircraft = Aircraft::where('callsign', '=', $callsign)->firstOrFail();

    //     //check request is an action or a state
    //     if (array_key_exists($state, $allowable_actions)) {
    //         //it's an action

    //         $action = $state; // assign state as action
    //         //check if aircraft is already in required state
    //         if ($aircraft->state == $allowable_actions[$action]) {
    //             return response('Conflict', 409);
    //         }

    //         $tracker_column = strtolower("can_" . $action);

    //         $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);

    //         ///
    //         if ($state == "APPROACH" && !$spot_is_available) {
    //             return response('Conflict', 409);
    //         }

    //         if ($tracker->$tracker_column && $tracker->runway_available) {

    //             $aircraft->action = $action;
    //             $aircraft->state = null;
    //             $aircraft->save();
    //             $tracker->$tracker_column = false;
    //             $tracker->runway_available = false;

    //             $tracker->save();

    //             return response('OK', 204);
    //         } else {

    //             return response('Conflict', 409);
    //         }

    //     } else if (array_key_exists($state, $allowable_states)) { //LANDED, AIRBORNE, PARKED
    //         //it's a state
    //         // if ($aircraft->action == $allowable_states[$state]) {
    //         //     return response('Conflict', 409);
    //         // }

    //         if ($state == "PARKED") {
    //             return response('Conflict', 409);
    //         }

    //         $tracker_column = "can_" . $aircraft->action;
    //         $aircraft->action = null;
    //         $aircraft->state = $state;
    //         $aircraft->save();

    //         $tracker->$tracker_column = true;

    //         $tracker->$tracker_column = false;

    //         if ($state == "PARKED") {
    //             if ($aircraft->type == "PRIVATE") {
    //                 $tracker->small_spots_occupied += 1;
    //             } else {
    //                 $tracker->large_spots_occupied += 1;
    //             }
    //         }

    //         if ($state != "LANDED") {
    //             $tracker->runway_available = true;
    //         }

    //         $tracker->save();
    //         return response('No Content', 204);

    //     } else {
    //         return response('Bad Request', 409);
    //     }

    // }

    private function checkAvailableSpot(Tracker $tracker, Aircraft $aircraft): bool
    {
        $is_available = false;

        if ($aircraft->type == 'PRIVATE') {
            //check if spots occupied is less than total allowed
            if ($tracker->small_spots_occupied < $tracker->total_small_spots) {
                $is_available = true;
            }

        } else {

            if ($tracker->large_spots_occupied < $tracker->total_large_spots) {
                $is_available = true;
            }
        }

        return $is_available;
    }

    //Check if action is allowed
    private function actionIsNotAllowed($state, $aircraft, $allowable_actions)
    {

        $not_allowed = true;
        if (array_key_exists($state, $allowable_actions)) {

            if ($allowable_actions[$aircraft->state]['next_state'] == $state) {
                $not_allowed = false;
            }

        }

        return $not_allowed;
    }
}
