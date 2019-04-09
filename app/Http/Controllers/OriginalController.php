<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\PseudoOriginal;

class OriginalController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        return view('original.index');
    }

    public function store(Request $request)
    {
        $start_path = $request->input('start_path');
        $over_path = $request->input('over_path');
        $th = $request->input('th');
        PseudoOriginal::dispatch($start_path,$over_path,$th);

        return response()->json(['msg'=>'正在生成中']);
    }
}