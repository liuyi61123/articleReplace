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
        $header = [
            'headers' => [
                'Authorization' => 'APPCODE fe945f8ba6d04d33a869c3b20c2bf065',
            ]
        ];
        $brand_api = 'https://jisucxdq.market.alicloudapi.com/car/brand';
        $response = $client->request('GET', $brand_api, $header);
        $brands = json_decode($response->getBody()->getContents(),true)['result'];

        //先插入品牌
        foreach($brands as $brand){
            $brands_data[] = [
                'id'=>$brand['id'],
                'name'=>$brand['name'],
                'pid'=>0
            ];
        }
        DB::table('car_infos')->insert($brands_data);

        //再循环插入型号
        $model_api = 'https://jisucxdq.market.alicloudapi.com/car/carlist?parentid=';
        foreach($brands as $brand){

            $parentid = $brand['id'];
            $response = $client->request('GET', $model_api.$parentid, $header);
            $contents = json_decode($response->getBody()->getContents(),true);
            $carlists = array();

            foreach($contents['result'] as $content){
                foreach($content['carlist'] as $value){
                    if(!in_array(['name'=>$value['name'],'pid'=>$parentid],$carlists)){
                        $carlists[] = array(
                            'name'=>$value['name'],
                            'pid'=>$parentid
                        );
                    }
                }
            }
            DB::table('car_infos')->insert($carlists);

            //暂停 10 秒
            // sleep(2);
        }
    }
}
