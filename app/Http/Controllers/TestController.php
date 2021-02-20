<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function login()
    {
        return response()->json([
            'code'=>20000,
            'data'=>[
                'token'=>'testtoken'.time()
            ]
        ]);
    }

    public function info()
    {
        return response()->json([
            'code'=>20000,
            'data'=>[
                'name'=>'liuyi',
                'roles'=>'',
                'avatar'=>'',
            ]
        ]);
    }

    public function logout()
    {
        return response()->json([
            'code'=>20000,
            'data'=>[
                'token'=>'testtoken'.time()
            ]
        ]);
    }
}
