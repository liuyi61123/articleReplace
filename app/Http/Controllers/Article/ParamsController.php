<?php

namespace App\Http\Controllers\Article;

use Illuminate\Http\Request;
use App\Models\Article\Param;

class ParamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Param $param)
    {
        if ($request->ajax()) {
            //渲染列表
            $output = $param->paginate(20);
            return response()->json($output);
        }else{
            return view('article.params.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.params.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Param $param)
    {
        $param->fill($request->all());
        $param->save();

        return response()->json(['status'=>200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Param $param)
    {
        return response()->json($param);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if ($request->ajax()) {
            //渲染列表
            $param = Param::find($id);
            return response()->json($param);
        }else{
            return view('article.params.edit',compact('id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request ,Param $param)
    {
        $param->fill($request->all());
        $param->save();

        return response()->json(['status'=>200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Param $param)
    {
        //删除数据库
        $param->delete();
        return response()->json(['status'=>200]);
    }
}
