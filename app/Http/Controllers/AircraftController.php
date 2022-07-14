<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Crypto\Rsa\KeyPair;
use Spatie\Crypto\Rsa\PrivateKey;
use Spatie\Crypto\Rsa\PublicKey;
use Illuminate\Support\Facades\Storage;

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
}
