<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleParam;
use Illuminate\Support\Facades\Storage;
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Article $article)
    {
        if ($request->ajax()) {
            //渲染列表
            $output = $article->all();
            return response()->json($output);
        }else{
            return view('articles.index');
        }
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
    {
        $article_data = $request->only('template_id','title','keywords','description','content');
        $article->fill($article_data);
        $article->save();

        //遍历参数集合,并保存
        $params = $request->input('params');
        $article->params()->createMany($params);

        //计算参数
        $data = $request->all();
        $data['id'] = $article->id;
        $res = $article->export($data);
        return response()->json(['status'=>200]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        $pathToFile = 'storage/articles/'.$id.'/articles.zip';
        return response()->download($pathToFile);
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
    public function edit(Request $request,$id)
    {
        if ($request->ajax()) {
            //渲染列表
            $article = Article::with('params')->find($id);
            return response()->json($article);
        }else{
            return view('articles.edit',compact('id'));
        }
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
        $article_data = $request->only('template_id','title','keywords','description','content');
        $article->fill($article_data);
        $article->save();

        //删除关联的参数
        $params = $request->input('params');
        ArticleParam::where('article_id',$article->id)->delete();
        //保存
        $article->params()->createMany($params);

        //删除原文件后，重新生成
        $directory = 'public/articles/'.$article->id;
        Storage::deleteDirectory($directory);
        //计算参数
        $data = $request->all();
        $data['id'] = $article->id;
        $res = $article->export($data);
        return response()->json(['status'=>200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //删除数据库
        $id = $article->id;
        $article->delete();
        ArticleParam::where('article_id',$id)->delete();

        //删除对应的文件
        $directory = 'public/articles/'.$id;
        Storage::deleteDirectory($directory);

        return response()->json(['status'=>200]);
    }
}
