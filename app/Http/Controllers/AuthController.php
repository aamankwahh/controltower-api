<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    //
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('authToken')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);

    }

    /**
     * Get user login data
     * @return array
     */
	private function getUserLoginData($user = null){
		if(!$user){
			$user = auth()->user();
		}
		$accessToken = $user->createToken('authToken')->accessToken;
        return ['user' => $user, 'token' => $accessToken, 'nextpage' => '/home'];
	}
	

	/**
     * Authenticate and login user
     * @return \Illuminate\Http\Response
     */
	function login(Request $request){
		$email = $request->email;
		$password = $request->password;
        Auth::attempt(['email' => $email, 'password' => $password]); //login with email 

		// if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
		// 	Auth::attempt(['email' => $username, 'password' => $password]); //login with email 
		// } 
		// else {
		// 	Auth::attempt(['username' => $username, 'password' => $password]); //login with username
		// }
        if (!Auth::check()) {
            return $this->reject("Username or password not correct", 400);
        }
		$user = auth()->user();
		$loginData = $this->getUserLoginData($user);
        //return $this->respond($loginData);
        return response($loginData, 200);
	}

    public function login2(Request $request){
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['user'=>$user,'token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
    
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
