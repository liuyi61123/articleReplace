<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('articles')->insert([
            'template_id' => 1,
            'title' => '{$name}}无法无天',
            'keywords' => '{$name}}无法无天',
            'description' => '{$name}}无法无天',
            'content' => '{$name}}无法无天{$name}}无法无天{$name}}无法无天',
        ]);
    }
}
