<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
            <meta charset="utf-8" />
            <title>{title}</title>
            <meta name="keywords" content="{keywords}" />
            <meta name="description" content="{description}" />
            <script>
                var title = '招聘模板';
            </script>
        </head>
        <body>
            <img src=" {图片}"> //随机获取的图片
            <img src="{image}">//生成的图片
            {段落}//段落
            {city}//城市
            {county}//区
            {car_brand}//汽车品牌
            {car_model}//汽车型号
            {price}//价格
        </body>
        </html>
HTML;

        DB::table('templates')->insert([
            ['name' => '招聘模板','content' =>$html],
            ['name' => '夜场模板','content' =>$html],
            ['name' => '下水道模板','content' =>$html]
        ]);
    }
}
