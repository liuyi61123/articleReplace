<?php

namespace App\Http\Controllers\Article;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article\Paragraph;

class ParagraphsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Paragraph $paragraph)
    {
        if ($request->ajax()) {
            //渲染列表
            $output = $paragraph->paginate(20);
            return response()->json($output);
        }else{
            return view('article.paragraphs.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.paragraphs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Paragraph $paragraph)
    {
        $paragraph->fill($request->all());
        $paragraph->save();

        return response()->json(['status'=>200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Paragraph $paragraph)
    {
        return response()->json($paragraph);
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
            $paragraph = Paragraph::find($id);
            return response()->json($paragraph);
        }else{
            return view('article.paragraphs.edit',compact('id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request ,Paragraph $paragraph)
    {
        $paragraph->fill($request->all());
        $paragraph->save();

        return response()->json(['status'=>200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Paragraph $paragraph)
    {
        //删除数据库
        $paragraph->delete();
        return response()->json(['status'=>200]);
    }
}
