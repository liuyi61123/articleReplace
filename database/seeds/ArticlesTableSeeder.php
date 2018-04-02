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
            [
            'template_id' => 1,
            'title' => '{$name}无法无天',
            'keywords' => '{$name}无法无天',
            'description' => '{$name}无法无天',
            'content' => '{$name}无法无天{$name}无法无天{$name}无法无天'
            ],
            [
            'template_id' => 2,
            'title' => '{$name}无法无天{$value}',
            'keywords' => '{$name}无法无天{$value}',
            'description' => '{$name}无法无天{$value}',
            'content' => '{$name}无法无天{$value}{$name}{$value}无法无天{$name}{$value}无法无天'
            ],
            [
            'template_id' => 3,
            'title' => '{$name}无法无天{$value1}',
            'keywords' => '{$name}无法无天{$value1}',
            'description' => '{$name}无法无天{$value1}',
            'content' => '{$name}无法无天{$value1}{$name}无法无天{$value1}{$name}无法无天{$value1}'
            ]
        ]);
    }
}
