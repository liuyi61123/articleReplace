<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use Illuminate\Support\Facades\Storage;

class WebsitesController extends Controller
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
     * 上传url
     */
    public function upload(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type','add');//追加还是覆盖
        $file_name = 'websites/'.$id.'.txt';
        $exists = Storage::disk('public')->exists($file_name);  

        if(($type == 'add')&&$exists){
            $content = file_get_contents($request->file('upload_file'));
            $path = Storage::disk('public')->append($file_name,$content); 
            $url = Storage::disk('public')->url($path);

            $data = [
                'name'=>$id.'.txt',
                'url'=>$url
            ];
            return response()->json(['status'=>200,'data'=>$data]);
        }else{
            $path = $request->file('upload_file')->storeAs('websites', $id.'.txt','public');
            $url = Storage::disk('public')->url($path);
            $data = [
                'name'=>$id.'.txt',
                'url'=>$url
            ];

            $website = Website::findOrFail($id);
            $website->urls = $data;
            $website->save();
            
            return response()->json(['status'=>200,'data'=>$data]);
        }
    }

    /**
     * 删除urls
     */
    public function removeUrls(Request $request)
    {
        $id = $request->input('id');
        $file_name = 'websites/'.$id.'.txt';
        Storage::disk('public')->delete($file_name);

        $website = Website::findOrFail($id);
        $website->urls = null;
        $website->save();

        return response()->json(['status'=>200]);
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
            $websites = Website::with('category')->paginate(20);
            return response()->json($websites);
        }else{
            return view('websites.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('websites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Website $website)
    {
        $website->fill($request->all());
        $website->save();
        return response()->json(['status'=>200,'msg'=>'添加成功']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $website = Website::find($id);
        return response()->json($website);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('websites.edit',compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        $website->fill($request->all());
        $website->save();
        return response()->json(['status'=>200,'msg'=>'修改成功']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Website $website)
    {
        $website->delete();
        return response()->json(['status'=>200]);
    }
}
