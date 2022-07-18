<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\ParkingSpot;
use App\Models\RequestLog;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Spatie\Crypto\Rsa\KeyPair;
use Spatie\Crypto\Rsa\PrivateKey;
use Spatie\Crypto\Rsa\PublicKey;

class AircraftController extends Controller
{
    //
    /**
     * List table records
     * @param  \Illuminate\Http\Request
     * @param string $fieldname //filter records by a table field
     * @param string $fieldvalue //filter value
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $fieldname = null, $fieldvalue = null)
    {
        $query = Aircraft::query();
        if ($request->search) {
            $search = trim($request->search);
            Aircraft::search($query, $search);
        }
        $orderby = $request->orderby ?? "aircraft.id";
        $ordertype = $request->ordertype ?? "desc";
        $query->orderBy($orderby, $ordertype);
        if ($fieldname) {
            $query->where($fieldname, $fieldvalue); //filter by a single field name
        }
        $records = $this->paginate($query, Aircraft::listFields());
        
        return $this->respond($records);
    }


    //--------SWAGGER DOCUMENTATION START-------------------
   /**
 * @OA\Put(
 *     path="/api/{callsign}/location",
 *      tags={"Aircraft Location Update"},
 *     summary="Aircraft state change request",
 *     @OA\Parameter(
 *         description="Aircraft's updates location",
 *         in="path",
 *         name="callsign",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         @OA\Examples(example="string", value="AR104", summary="Airliner - Current State: Airborne"),
 * 
 *         
 * 
 *     ),
 * @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="latitude",
 *                     type="double"
 *                 ),
 *                  @OA\Property(
 *                     property="longitude",
 *                     type="double"
 *                 ),
 *                  @OA\Property(
 *                     property="altitude",
 *                     type="integer"
 *                 ),
 *                  @OA\Property(
 *                     property="heading",
 *                     type="integer"
 *                 ),
 *                   @OA\Property(
 *                     property="type",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="token",
 *                     type="string"
 *                 ),
 *                
 *                
 *                 example={"latitude": "111.87654","longitude": "-23.7654","altitude": "3000","heading": "120","type": "AIRLINER","token":"1a1f91e2241e9056cf2dd4f9cf66e8da"}
 *             )
 *         )
 *     ),
 *  
 *     @OA\Response(
 *         response=204,
 *         description="OK"
 *     ),
 *  @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     ),
 *  @OA\Response(
 *         response=409,
 *         description="Conflict"
 *     ),
 *  @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 * )
 */
//--------SWAGGER DOCUMENTATION END-------------------

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
            'altitude' => 'required',
            'heading' => 'required',
            'type'=>'required'
           
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        try {
            //code...
            $callsign = $request->callsign;
            $longitude = $request->longitude;
            $latitude = $request->latitude;
            $altitude = $request->altitude;
            $heading = $request->heading;
            $type = $request->type;

            $aircraft = Aircraft::where('callsign', $callsign)->first();

            if ($aircraft) {
                $aircraft->latitude = $latitude;
                $aircraft->longitude = $longitude;
                $aircraft->altitude = $altitude;
                $aircraft->heading = $heading;

                $aircraft->save();

                // return response('Success', 204);
            } else {
                $aircraft = new Aircraft();
                $aircraft->type=$request->type;
                $aircraft->latitude = $latitude;
                $aircraft->longitude = $longitude;
                $aircraft->altitude = $altitude;
                $aircraft->heading = $heading;
                $aircraft->callsign = $callsign;
                $aircraft->save();

            }
            $status=1;
            $request_type="LOCATION_UPDATE";
            $this->logAircraftRequest($aircraft,$request_type,$status,"Weather");
            return response('Success', 204);
            
        } catch (\Throwable$th) {
            throw $th;
           // return response('Bad Request', 400);
        }

    }

//--------SWAGGER DOCUMENTATION START-------------------
   /**
 * @OA\Post(
 *     path="/api/{callsign}/intent",
 *      tags={"Request Aircraft State Change"},
 *     summary="Aircraft state change request",
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
 * @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="state",
 *                     type="string"
 *                 ),
 *                  @OA\Property(
 *                     property="token",
 *                     type="string"
 *                 ),
 *                
 *                
 *                 example={"state": "TAKEOFF","token":"1a1f91e2241e9056cf2dd4f9cf66e8da"}
 *             )
 *         )
 *     ),
 *  
 *     @OA\Response(
 *         response=204,
 *         description="OK"
 *     ),
 *  @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     ),
 *  @OA\Response(
 *         response=409,
 *         description="Conflict"
 *     ),
 *  @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 * )
 */
//--------SWAGGER DOCUMENTATION END-------------------
    public function setState(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state' => 'required',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request_type = "STATE_CHANGE";

        $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs
        $state = $request->state; // get state value
        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign', '=', $callsign)->firstOrFail();

        $tracker = Tracker::firstOrFail();

        //Check action is allowed based on previous state
        if ($this->actionIsNotAllowed($state, $aircraft, $allowable_actions)) {

            $request_status = 0; // rejected
            $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
            return response('Action not allowed', 409);

        }

        //TAKE OFF
        if (($state == "TAKEOFF" || $state == "LANDED") && $tracker->runway_available == false) {
            $request_status = 0; // rejected
            $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
            return response('Runway not available', 409);
        }

        $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);
        Log::info($spot_is_available);

        //
        if($state=="APPROACH"  && !$spot_is_available){
            $request_status = 0; // rejected
            $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
            return response('No spot available', 409);
        }

        $tracker_column = strtolower("can_" . $state);

        // IF field is in tracker column
        if (Schema::hasColumn('trackers', $tracker_column)) {

            if ($tracker->runway_available == false) {
                $request_status = 0; // rejected
                $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
                return response('Runway not available', 409);
            }
            $is_allowed = $tracker->$tracker_column;
            $previous_aircraft_state = strtolower($aircraft->state);

            if (!$is_allowed) {

                $request_status = 0; // rejected
                $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
                return response('Cannot perform action', 409);

            } else {
                //Log::info("state " . $state);
                $table_column = "can_" . $previous_aircraft_state;
                if (Schema::hasColumn('trackers', $table_column)) {

                    $tracker->$table_column = true;
                }
                $aircraft->state = $state;
                $tracker->$tracker_column = false;

                if ($state != "APPROACH") {
                    $tracker->runway_available = false;
                }


                $aircraft->save();
                $tracker->save();
                $request_status = 1; // accepted
                $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
                return response('Accepted', 204);
            }

        } else { //field not in schema

            $previous_aircraft_state = $aircraft->state;
            $tracker_column = strtolower("can_" . strtolower($previous_aircraft_state));

            //return response($tracker->$tracker_column,200);
            if (Schema::hasColumn('trackers', $tracker_column)); //check whether  table has column
            {
                if ($tracker->$tracker_column == 0) {

                    $tracker->$tracker_column = true;

                }

            }

            if ($state == "AIRBORNE" && $previous_aircraft_state=="TAKEOFF") {
                $tracker->runway_available = true;

                $spot = ParkingSpot::where('aircraft_id', $aircraft->id)->first();
                $spot->aircraft_id = null;
                $spot->available = true;
                $spot->save();

                if ($aircraft->type == "PRIVATE") {
                    $tracker->small_spots_occupied -= 1;
                } else {
                    $tracker->large_spots_occupied -= 1;
                }
            }

            $aircraft->state = $state;
            $aircraft->save();
            $tracker->save();

            $request_status = 1; //accepted
            $this->logAircraftRequest($aircraft, $request_type, $request_status,$state);
            return response('Accepted', 204);

        }

    }

   
    private function logAircraftRequest($aircraft, $request_type, $status,$action=null)
    {
        try {
            $log = new RequestLog();
            $log->aircraft_id = $aircraft->id;
            $log->request_type = $request_type;
            $log->status = $status;
            $log->action=$action;
            $log->save();
        } catch (\Throwable$th) {
            //throw $th;

        }
    }
    private function checkAvailableSpot(Tracker $tracker, Aircraft $aircraft): bool
    {
        $is_available = false;

        // Log::info("aircraft_type. ".$aircraft->type)

        if ($aircraft->type == 'PRIVATE') {
            //check if spots occupied is less than total allowed
           
            
            $spot = ParkingSpot::where('spot_type','SMALL')->where('available',1)->first();
            
            if ($spot) {
                $is_available = true;
            }

        } else {
            $spot = ParkingSpot::where('spot_type','LARGE')->where('available',1)->first();
            if ($spot) {
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

    public function generateKey()
    {

     
        [$privateKey, $publicKey] = (new KeyPair())->generate();
       //$test2 Spatie\Crypto\Rsa\PublicKey::fromString($publicKeyString);

        // $key1 = openssl_get_publickey($key);
        // open_ssl_ge

        return response()->json(["public" => $publicKey]);

    }
}
