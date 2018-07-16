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
            {county}
            {car_brand}
            {car_model}
            <img src="{image1}">
            <img src="{image2}">
        </body>
        </html>
HTML;
        $html1 = <<<HTML
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
                    var title = '夜场模板';
                </script>
            </head>
            <body>
                {county}
                {car_brand}
                {car_model}
                <img src="{image1}">
                <img src="{image2}">
            </body>
            </html>
HTML;
        $html2 = <<<HTML
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
                    var title = '下水道模板';
                </script>
            </head>
            <body>
                {county}
                {car_brand}
                {car_model}
                <img src="{image1}">
                <img src="{image2}">
            </body>
            </html>
HTML;
        DB::table('templates')->insert([
            ['name' => '招聘模板','content' =>$html],
            ['name' => '夜场模板','content' =>$html1],
            ['name' => '下水道模板','content' =>$html2]
        ]);
    }
}
