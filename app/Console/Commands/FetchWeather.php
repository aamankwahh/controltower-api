<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Weather;

class FetchWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return 0;
        
        try {
            $endpoint = "https://api.openweathermap.org/data/2.5/weather?lat=45.78523584715256&lon=-111.19022627437427&appid=1a1f91e2241e9056cf2dd4f9cf66e8da";
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'GET',
                $endpoint
               
            );
            //dd($response);
            //$res = json_decode($response->getBody());
    
            $result = $response->getBody();
    
            $this->info($response->getBody());
    
            $weather = new Weather();
            $weather->response=$result;
    
            $weather->save();

            return 0;
        } catch (\Throwable $th) {
            //throw $th;

            return 1;
        }

    }
}
