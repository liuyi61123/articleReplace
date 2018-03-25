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
            'article_id' => '1',
            'name' => '{$name}',
            'content' =>'上海,广州,深圳,北京',
        ]);
    }
}
