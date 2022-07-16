<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    //
    public function currentuserdata(){
		$user = auth()->user();
		return response($user);
	}
}
