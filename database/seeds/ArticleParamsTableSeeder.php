<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleParamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('article_params')->insert([
            [
            'article_id' => '1',
            'name' => '{$name}',
            'content' =>'上海,广州,深圳,北京'
            ],
            [
            'article_id' => '2',
            'name' => '{$name}',
            'content' =>'上海,广州,深圳,北京'
            ],
            [
            'article_id' => '2',
            'name' => '{$value}',
            'content' =>'奥迪,奔驰,吉普,别克'
            ],
            [
            'article_id' => '3',
            'name' => '{$name}',
            'content' =>'上海,广州,深圳,北京,重庆'
            ],
            [
            'article_id' => '3',
            'name' => '{$value1}',
            'content' =>'水管,水槽,水锈,水屁'
            ]
        ]);
    }
}
