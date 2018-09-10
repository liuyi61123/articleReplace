<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class CitysTableSeeder extends Seeder
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
                'Authorization' => 'APPCODE 127f2a01c31746f3bf412ffee5686388',
            ]
        ];
        $province_api = 'https://api02.aliyun.venuscn.com/area/all?level=0';
        $response = $client->request('GET', $province_api, $header);
        $contents = json_decode($response->getBody()->getContents(),true);
        $provinceLists = array();

        foreach($contents['data'] as $value){
            $provinceLists[] = array(
                'id'=>$value['id'],
                'name'=>$value['name'],
                'pid'=>$value['parent_id']
            );
        }

        DB::table('citys')->insert($provinceLists);

        //再循环插入型号
        $city_api = 'https://api02.aliyun.venuscn.com/area/query?parent_id=';
        foreach($provinceLists as $provinceList){

            $parentid = $provinceList['id'];
            $response = $client->request('GET', $city_api.$parentid, $header);
            $contents = json_decode($response->getBody()->getContents(),true);
            $cityLists = array();

            foreach($contents['data'] as $value){
                $cityLists[] = array(
                    'id'=>$value['id'],
                    'name'=>$value['name'],
                    'pid'=>$value['parent_id']
                );
            }
            DB::table('citys')->insert($cityLists);

            //再循环插入型号
            $county_api = 'https://api02.aliyun.venuscn.com/area/query?parent_id=';
            foreach($cityLists as $cityList){

                $parentid = $cityList['id'];
                $response = $client->request('GET', $county_api.$parentid, $header);
                $contents = json_decode($response->getBody()->getContents(),true);
                $countyLists = array();

                foreach($contents['data'] as $value){
                    $countyLists[] = array(
                        'id'=>$value['id'],
                        'name'=>$value['name'],
                        'pid'=>$value['parent_id']
                    );
                }
                DB::table('citys')->insert($countyLists);
            }
        }
    }
}
