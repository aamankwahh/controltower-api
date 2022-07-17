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
        Log::info($records);
        return $this->respond($records);
    }

    public function updateLocation(Request $request)
    {
        Log::info($request);
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
            $this->logAircraftRequest($aircraft,$request_type,$status);
            return response('Success', 204);
            
        } catch (\Throwable$th) {
            throw $th;
           // return response('Bad Request', 400);
        }

    }

    public function generateKey()
    {

        $key="-----BEGIN PRIVATE KEY-----
        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
        yzL1uRvu6AFOLeLsCF+adr9G7wQIkdc=
        -----END PRIVATE KEY-----";


    //    $test= Spatie\Crypto\Rsa\PrivateKey::fromString($privateKeyString);

       // generating an RSA key pair
        [$privateKey, $publicKey] = (new KeyPair())->generate();
       //$test2 Spatie\Crypto\Rsa\PublicKey::fromString($publicKeyString);

        // $key1 = openssl_get_publickey($key);
        // open_ssl_ge

        return response()->json(["public" => $publicKey]);

    }

    /**
     * @OA\Get(
     *     path="/{callsign}/intent",
     *     tags={"Request State Change"},
     *     summary="Changes the state of an aircraft ",
     *     description="Returns http status code",
     *     operationId="setState",
     *     @OA\Response(
     *         response=204,
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
        $request_type = "STATE_CHANGE";

        $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs
        $state = $request->state; // get state value
        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign', '=', $callsign)->firstOrFail();

        $tracker = Tracker::firstOrFail();

        //Check action is allowed based on previous state
        if ($this->actionIsNotAllowed($state, $aircraft, $allowable_actions)) {

            $request_status = 0; // rejected
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
            return response('Action not allowed', 409);

        }

        //TAKE OFF
        if (($state == "TAKEOFF" || $state == "LANDED") && $tracker->runway_available == false) {
            $request_status = 0; // rejected
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
            return response('Runway not available', 409);
        }

        $tracker_column = strtolower("can_" . $state);

        // IF field is in tracker column
        if (Schema::hasColumn('trackers', $tracker_column)) {

            if ($tracker->runway_available == false) {
                return response('Runway not available2', 409);
            }
            $is_allowed = $tracker->$tracker_column;
            $previous_aircraft_state = strtolower($aircraft->state);

            if (!$is_allowed) {

                return response('Cannot perform action', 409);

            } else {
                Log::info("state " . $state);
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

                return response('Accepted', 204);
            }

        } else { //field not in schema

            $tracker_column = strtolower("can_" . strtolower($aircraft->state));

            //return response($tracker->$tracker_column,200);
            if (Schema::hasColumn('trackers', $tracker_column)); //check whether  table has column
            {
                if ($tracker->$tracker_column == 0) {

                    $tracker->$tracker_column = true;

                }

            }

            if ($state == "AIRBORNE") {
                $tracker->runway_available = true;
            }

            $aircraft->state = $state;
            $aircraft->save();
            $tracker->save();

            $request_status = 1; //accepted
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
            return response('Accepted', 204);

        }

    }

    public function setState3(Request $request)
    {
        $request_type = "STATE_CHANGE";

        $allowable_actions = config('constants.allowable_actions'); //Array of correct verbs
        $state = $request->state; // get state value
        $callsign = $request->callsign;
        $aircraft = Aircraft::where('callsign', '=', $callsign)->firstOrFail();

        $tracker = Tracker::firstOrFail();

        //Check action is allowed based on previous state
        if ($this->actionIsNotAllowed($state, $aircraft, $allowable_actions)) {

            $request_status = 0;
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
            return response('Conflict', 409);

        }

        //Check if spot is available
        $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);

        //
        // if ($state == "LANDED" && !$spot_is_available) {
        //     $request_status = 0;
        //     $this->logAircraftRequest($aircraft, $request_type, $request_status);
        //     return response('No landing - Spot not available', 409);
        // }

        $tracker_column = strtolower("can_" . $state);

        //
        if (Schema::hasColumn('trackers', $tracker_column)) {

            $is_allowed = $tracker->$tracker_column;

            if ($is_allowed) {

                if ($state == "TAKEOFF") {
                    if ($tracker->runway_available == false) {
                        return response('Runway not available', 409);
                        //log
                    }
                    if ($aircraft->type == "PRIVATE") {
                        $tracker->small_spots_occupied -= 1;
                    } else {
                        $tracker->large_spots_occupied -= 1;
                    }

                    $spot = ParkingSpot::where('aircraft_id', $aircraft->id)->first();
                    $spot->aircraft_id = null;
                    $spot->available = true;
                    $spot->save();

                    $tracker->runway_available = false;

                }
                $tracker->$tracker_column = false;

                // if($state!="APPROACH"){ // Runway is not available for landing and takeoff only
                //     $tracker->runway_available = false;
                // }

                $aircraft->state = $state;
                $aircraft->save();

                $tracker->save();
                $request_status = 1;
                $this->logAircraftRequest($aircraft, $request_type, $request_status);
                return response('No Content', 204);
            } else {
                $request_status = 0;
                $this->logAircraftRequest($aircraft, $request_type, $request_status);
                return response('Conflict', 409);
            }

        } else { // if column is not in tracker table

            if ($state == "LANDED" && $tracker->runway_available == false) {

                return response('Conflict', 409);
            }

            $tracker_column = strtolower("can_" . $aircraft->state);

            //return response($tracker->$tracker_column,200);
            if (Schema::hasColumn('trackers', $tracker_column)); //check whether  table has column
            {

                if ($tracker->$tracker_column == 0) {

                    $tracker->$tracker_column = true;
                    //$tracker->runway_available=true;
                    // $tracker->save();

                    //$aircraft->state = $state;
                    //$aircraft->save();

                    // $request_status=1;
                    // $this->logAircraftRequest($aircraft,$request_type,$request_status);

                    // return response('No Content', 204);

                }
            }

            // // Landed means aircraft is using the runway
            // if ($state != "LANDED") {
            //     $tracker->runway_available = true;
            // }

            //$tracker->save();

            // $tracker->runway_available=false;

            $tracker->save();
            $request_status = 1;
            $this->logAircraftRequest($aircraft, $request_type, $request_status);

            return response('No Content', 204);

        }

    }

    private function returnStatusConflict($aircraft, $request_type)
    {
        $request_status = 0;
        $this->logAircraftRequest($aircraft, $request_type, $request_status);
        return response('Conflict', 409);
    }

    private function logAircraftRequest($aircraft, $request_type, $status)
    {
        try {
            $log = new RequestLog();
            $log->aircraft_id = $aircraft->id;
            $log->request_type = $request_type;
            $log->status = $status;
            $log->save();
        } catch (\Throwable$th) {
            //throw $th;

        }
    }
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
