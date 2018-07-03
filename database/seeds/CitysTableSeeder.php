<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('citys')->insert([
            [
                'name' => '上海',
                'pid' => 0
            ],
            [
                'name' => '北京',
                'pid' => 0
            ],
            [
                'name' => '黄埔',
                'pid' => 1
            ],
            [
                'name' => '静安',
                'pid' => 1
            ],
            [
                'name' => '徐汇',
                'pid' => 1
            ],
            [
                'name' => '长宁',
                'pid' => 1
            ],

            [
                'name' => '宝山',
                'pid' => 1
            ],
            [
                'name' => '虹口',
                'pid' => 1
            ],
            [
                'name' => '杨浦',
                'pid' => 1
            ],
            [
                'name' => '浦东',
                'pid' => 1
            ],
            [
                'name' => '闵行',
                'pid' => 1
            ],
            [
                'name' => '普陀',
                'pid' => 1
            ],
            [
                'name' => '青浦',
                'pid' => 1
            ],
            [
                'name' => '嘉定',
                'pid' => 1
            ],
            [
                'name' => '松江',
                'pid' => 1
            ],
            [
                'name' => '奉贤',
                'pid' => 1
            ],
            [
                'name' => '金山',
                'pid' => 1
            ],
            [
                'name' => '崇明',
                'pid' => 1
            ]
        ]);
    }
}
