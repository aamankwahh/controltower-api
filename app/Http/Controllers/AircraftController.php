<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\RequestLog;
use App\Models\Tracker;
use App\Models\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

            $request_status = 0;
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
            return response('Conflict', 409);

        }

        $spot_is_available = $this->checkAvailableSpot($tracker, $aircraft);

        if ($state == "APPROACH" && !$spot_is_available) {
            $request_status = 0;
            $this->logAircraftRequest($aircraft, $request_type, $request_status);
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

                $request_status = 1;
                $this->logAircraftRequest($aircraft, $request_type, $request_status);

                if ($state == "TAKEOFF") {
                    if ($aircraft->type == "PRIVATE") {
                        $tracker->small_spots_occupied -= 1;
                    } else {
                        $tracker->large_spots_occupied -= 1;
                    }
    
                    $spot = ParkingSpot::where('aircraft_id',$aircraft->id)->first();
    
                    $spot->aircraft_id=null;
                    $spot->save();
                }

                return response('No Content', 204);
            } else {
                $request_status=0;
            $this->logAircraftRequest($aircraft,$request_type,$request_status);
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

           

            // Landed means aircraft is using the runway
            if ($state != "LANDED") {
                $tracker->runway_available = true;
            }

            $tracker->save();

            $request_status=0;
            $this->logAircraftRequest($aircraft,$request_type,$request_status);

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
