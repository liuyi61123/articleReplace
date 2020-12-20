<?php

namespace App\Http\Controllers\Article;

use App\Models\Article\Template;
use App\Http\Requests\TemplateRequest;
use Illuminate\Http\Request;
use App\Handlers\OssUploadImageHandler;

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
            if($request->input('type') == 'all'){
                $output = $template->all();
            }else{
                $output = $template->paginate(20);
            }
            return response()->json($output);
        }else{
            return view('article.templates.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.templates.create');
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
            return view('article.templates.edit',compact('id'));
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

    /**
     * 上传图片
     */
    public function upload_image(Request $request,OssUploadImageHandler $upload){
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
     * 删除图片
     */
    public function delete_image(Request $request,OssUploadImageHandler $upload){
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
