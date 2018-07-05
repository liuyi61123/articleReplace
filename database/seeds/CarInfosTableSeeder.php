<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('car_infos')->insert([
            [
                'name' => '宝马',
                'pid' => 0
            ],
            [
                'name' => '奥迪',
                'pid' => 0
            ],
            [
                'name' => '奔驰',
                'pid' => 0
            ],
            [
                'name' => '大众',
                'pid' => 0
            ],
            [
                'name' => '本田',
                'pid' => 0
            ],
            [
                'name' => '丰田',
                'pid' => 0
            ],
            [
                'name' => '别克',
                'pid' => 0
            ],
            [
                'name' => 'x1',
                'pid' => 1
            ],
            [
                'name' => 'x2',
                'pid' => 1
            ],

            [
                'name' => 'x3',
                'pid' => 1
            ],
            [
                'name' => 'A2',
                'pid' => 2
            ],
            [
                'name' => 'A4',
                'pid' => 2
            ],
            [
                'name' => 'A6',
                'pid' => 2
            ],
            [
                'name' => 'S300',
                'pid' => 3
            ],
            [
                'name' => 'S500',
                'pid' => 3
            ],
            [
                'name' => 'S750',
                'pid' => 3
            ],
        ]);
    }
}
