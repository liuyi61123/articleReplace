<?php

namespace App\Http\Controllers\Article;

use App\Models\Article\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\DB;
use ZipArchive;

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
            $output = $article->with('template')->paginate(20);
            return response()->json($output);
        }else{
            return view('article.articles.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request,Article $article)
    {
        $article_data['template_id'] = $request->input('template_id');
        $article_data['name'] = $request->input('name');
        $article_data['desc'] = $request->input('desc');
        $article_data['config'] = $request->except(['template_id','name','desc']);
        $article->fill($article_data);
        $article->save();

        //计算参数
        $res = $article->generate();
        return response()->json(['status'=>200,'data'=>$res]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        $exists = Storage::disk('local')->exists('public/articles/'.$id.'/articles'.$id.'.zip');
        if($exists){
            $pathToFile = storage_path('app/public/articles/'.$id.'/articles'.$id.'.zip');
        }else{
            $zip = new ZipArchive();
            $base_path = storage_path('app/public/articles/'.$id);
            $zipfilename = $base_path.'/articles'.$id.'.zip';
            $zip->open($zipfilename,ZipArchive::CREATE);  //打开压缩包
            $zip->addGlob($base_path.'/*.txt',GLOB_BRACE, array('remove_path' =>$base_path));
            $zip->close(); //关闭压缩包
        }

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
            $article = Article::find($id);
            return response()->json($article);
        }else{
            return view('article.articles.edit',compact('id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $Article
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article_data['status'] = 0;
        $article_data['template_id'] = $request->input('template_id');
        $article_data['name'] = $request->input('name');
        $article_data['desc'] = $request->input('desc');
        $article_data['config'] = $request->except(['template_id','name','desc']);
        $article->fill($article_data);
        $article->save();

        //计算参数
        $article->generate();
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

        //删除对应的文件
        $directory = 'public/articles/'.$id;
        Storage::deleteDirectory($directory);

        return response()->json(['status'=>200]);
    }
}
