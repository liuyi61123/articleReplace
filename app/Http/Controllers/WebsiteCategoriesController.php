<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteCategory;
use App\Http\Resources\WebsiteCategory as WebsiteCategoryResource;

class WebsiteCategoriesController extends Controller
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
            $categories = WebsiteCategory::orderBy('sort','asc')->get();
            return WebsiteCategory::tree($categories->toArray());
        }else{
            return view('website_categories.index');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function api(Request $request)
    {
        $level = $request->input('level',0);
        $categories = WebsiteCategory::where('level',$level)->get();
        return $categories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('website_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,WebsiteCategory $category)
    {
        $category->fill($request->all());
        $category->save();
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
        $category = WebsiteCategory::find($id);
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('website_categories.edit',compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,WebsiteCategory $category)
    {
        $category->fill($request->all());
        $category->save();
        return response()->json(['status'=>200,'msg'=>'修改成功']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebsiteCategory $category)
    {
        $id = $category->id;
        $category->delete();

        WebsiteCategory::where('id',$id)->destroy();
        return response()->json(['status'=>200]);
    }
}
