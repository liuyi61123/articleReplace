<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class CarInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $brand_api = 'https://jisucxdq.market.alicloudapi.com/car/brand';
        $header = [
            'headers' => [
                'Authorization' => 'APPCODE 127f2a01c31746f3bf412ffee5686388',
            ]
        ];
        $response = $client->request('GET', $brand_api, $header);
    }
}
