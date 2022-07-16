<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\TrafficLogController;
use App\Http\Controllers\AccountController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:api'])->group(function () {
    Route::get('account/currentuserdata', [AccountController::class, 'currentuserdata']);
    /* routes for Aircraft Controller  */	
	Route::get('aircraft', [AircraftController::class,'index']);
	Route::get('aircraft/index', [AircraftController::class,'index']);
	Route::get('aircraft/index/{filter?}/{filtervalue?}', [AircraftController::class,'index']);	
	Route::get('aircraft/view/{rec_id}', [AircraftController::class,'view']);	
	Route::post('aircraft/add', [AircraftController::class,'add']);	
	Route::any('aircraft/edit/{rec_id}', [AircraftController::class,'edit']);	
	Route::any('aircraft/delete/{rec_id}', [AircraftController::class,'delete']);

    /* routes for Traffic Controller  */	
	Route::get('traffic', [TrafficLogController::class,'index']);
	Route::get('traffic/index', [TrafficLogController::class,'index']);
	Route::get('traffic/index/{filter?}/{filtervalue?}', [TrafficLogController::class,'index']);	
	Route::get('traffic/view/{rec_id}', [TrafficLogController::class,'view']);	
	Route::post('traffic/add', [TrafficLogController::class,'add']);	
	Route::any('traffic/edit/{rec_id}', [TrafficLogController::class,'edit']);	
	Route::any('traffic/delete/{rec_id}', [TrafficLogController::class,'delete']);
});

//Route::put('{callsign}/location', [AuthController::class, 'register']);

Route::get('aircraft/generate', [AircraftController::class, 'generateKey']);
Route::post('{callsign}/intent', [AircraftController::class, 'setState']);

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);
//Route::post('auth/login', 'AuthController@login');
//Route::get('login', 'AuthController@login')->name('login');
