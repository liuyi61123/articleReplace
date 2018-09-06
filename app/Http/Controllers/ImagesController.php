<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Handlers\OssUploadImageHandler;

class ImagesController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,OssUploadImageHandler $oss)
    {
         //获取图片列表
         $last = $request->input('last','');
         $oss_imges = $oss->listArrays('',[
             'max-keys'=>18,
             'prefix'=>'uploads/templates',
             'delimiter'=>'',
             'marker'=>$last,
         ]);

         return response()->json($oss_imges);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,OssUploadImageHandler $upload)
    {
        $url = $upload->save($request->file('image'),'templates');
        if($url){
            $response = ['status'=>200,'data'=>$url];
            $status = 200;
        }else{
            $response = ['status'=>500,'msg'=>'上传失败'];
            $status = 500;
        }
        return response()->json($response,$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,OssUploadImageHandler $upload)
    {
        $delete = $upload->delete($request->url);
        if($delete){
            $response = ['status'=>200,'msg'=>'删除成功'];
            $status = 200;
        }else{
            $response = ['status'=>400,'msg'=>'删除失败','data'=>$request->url];
            $status = 500;
        }
        return response()->json($response,$status);
    }
}
