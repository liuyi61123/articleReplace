<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsitePush;

class WebsitePushesController extends Controller
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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //渲染列表
            $websites = WebsitePush::paginate(20);
            return response()->json($websites);
        }else{
            return view('website_pushes.index');
        }
    }

    /**
     * 手动提交
     *
     * @return \Illuminate\Http\Response
     */
    public function manual()
    {
        return view('website_pushes.manual');
    }

    /**
     * 自动提交
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('website_pushes.create');
    }

    /**
     * 自动提交编辑
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if ($request->ajax()) {
            //渲染列表
            $website_push = WebsitePush::find($id);
            return response()->json($website_push);
        }else{
            return view('website_pushes.edit',compact('id'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = $request->input('type');
        if($type == 'manual'){
            //手动提交
            $result = WebsitePush::manual($request->all());
        }elseif($type == 'automatic'){
            //自动提交
            $result = $request->all();
            //保存数据库
            $website_push = WebsitePush::create([
                'name'=>$request->input('name'),
                'is_automatic'=>$request->input('is_automatic'),
                'config'=>$request->input('config')
            ]);
            $website_push->save();
        }else{
            $result = [];
        }
        
        return response()->json(['status'=>200,'data'=>$result]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,WebsitePush $website_push)
    {
        $status = $request->input('status');
        if($status){
            //修改状态
            if($request->input('status') == 1){
                //执行
                $website_push->automatic();
                $msg = '开始执行';
            }else{
                //停止
                $website_push->status = $request->input('status');
                $website_push->save();
                $msg = '停止执行';
            }
            return response()->json(['status'=>200,'msg'=>$msg ]);
        }else{
            $all = $request->all();
            unset($all['type']);
            $website_push->fill($all);
            $website_push->save();

            return response()->json(['status'=>200]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        WebsitePush::destroy($id);
        return response()->json(['status'=>200]);
    }
}
