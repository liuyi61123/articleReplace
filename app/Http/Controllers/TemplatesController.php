<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Http\Requests\TemplateRequest;
use Illuminate\Http\Request;

class TemplatesController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Template $template)
    {
        if ($request->ajax()) {
            //渲染列表
            $output = $template->all();
            return response()->json($output);
        }else{
            return view('templates.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateRequest $request,Template $template)
    {
        $template->fill($request->all());
        $template->save();
        return response()->json(['status'=>200]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Template $template)
    {
        if ($request->ajax()) {
            //渲染列表
            return response()->json($template->toArray());
        }else{
            $id = $template->id;
            return view('templates.edit',compact('id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TemplateRequest $request,Template $template)
    {
        $template->fill($request->all());
        $template->save();
        return response()->json(['status'=>200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        //删除数据库
        if(count($template->articles)>0){
            return response()->json(['status'=>500,'msg'=>'不能删除']);
        }else{
            $template->delete();
            return response()->json(['status'=>200]);
        }
    }
}
