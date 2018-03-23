<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
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
    public function index()
    {
        return view('articles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Article $article)
    {   return response()->json($request->all());
        $article_data = $request->only('template_id','title','keywords','description','content');
        $article->fill($article_data);
        $article->save();
        //遍历参数集合
        $param_names = $request->input('param_names');
        $param_contents = $request->input('param_contents');
        $param_data = array();
        for($i=0;$i<count($param_names);$i++){
            $param_data[$i] = [
                'name'=>$param_names[$i],
                'content'=>$param_contents[$i]
            ];
        }
        $article->params()->createMany($param_data);
        return response()->json($article);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //
    }
}
