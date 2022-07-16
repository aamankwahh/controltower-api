<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrafficLogController extends Controller
{
    //
    /**
     * List table records
	 * @param  \Illuminate\Http\Request
     * @param string $fieldname //filter records by a table field
     * @param string $fieldvalue //filter value
     * @return \Illuminate\View\View
     */
	function index(Request $request, $fieldname = null , $fieldvalue = null){
		$query = TrafficLog::query();
        $query->join("aircraft", "traffic_logs.aircraft_id", "=", "aircraft.id");
		if($request->search){
			$search = trim($request->search);
			TrafficLog::search($query, $search);
		}
		$orderby = $request->orderby ?? "traffic_logs.id";
		$ordertype = $request->ordertype ?? "desc";
		$query->orderBy($orderby, $ordertype);
		if($fieldname){
			$query->where($fieldname , $fieldvalue); //filter by a single field name
		}
		$records = $this->paginate($query, TrafficLog::listFields());
        Log::info($records);
		return $this->respond($records);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrafficLog  $trafficLog
     * @return \Illuminate\Http\Response
     */
    public function show(TrafficLog $trafficLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TrafficLog  $trafficLog
     * @return \Illuminate\Http\Response
     */
    public function edit(TrafficLog $trafficLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TrafficLog  $trafficLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrafficLog $trafficLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TrafficLog  $trafficLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrafficLog $trafficLog)
    {
        //
    }
}
