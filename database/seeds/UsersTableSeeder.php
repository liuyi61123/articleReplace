<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
            'name' => 'liuyi',
            'email' => '475793342@qq.com',
            'password' => bcrypt('liuyi61123')
            ],
            [
            'name' => 'test',
            'email' => 'test@qq.com',
            'password' => bcrypt('password')
            ]
        ]);
    }
}
