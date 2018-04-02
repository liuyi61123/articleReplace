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
        </head>
        <body>
            {content}
        </body>
        </html>
HTML;
        DB::table('templates')->insert([
            'name' => '招聘模板',
            'content' =>$html,
        ]);
    }
}
