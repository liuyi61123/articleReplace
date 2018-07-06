<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleParam;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\DB;

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
            $output = $article->with('template')->paginate();
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
    public function store(ArticleRequest $request,Article $article)
    {
        $article_data['template_id'] = $request->input('template_id');
        $article_data['config'] = json_encode($request->except('template_id'));

        $article->fill($article_data);
        $article->save();

        //计算参数
        $res = $article->generate($request->all(),$article->id);
        return response()->json(['status'=>200,'data'=>$res]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        $pathToFile = storage_path('app/public/articles/'.$id.'/articles'.$id.'.zip');
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
    public function update(ArticleRequest $request, Article $article)
    {
        $article_data['template_id'] = $request->input('template_id');
        $article_data['config'] = json_encode($request->except('template_id'));
        $article->fill($article_data);
        $article->save();

        //删除原文件后，重新生成
        $directory = 'public/articles/'.$article->id;
        Storage::deleteDirectory($directory);

        //计算参数
        $res = $article->generate($request->all(),$article->id);
        return response()->json(['status'=>200,'data'=>$res]);
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

    /**
     * 获取车子品牌列表
     */
     public function cars($pid){
        $cars = DB::table('car_infos')->where('pid',$pid)->get();
        return response()->json($cars);
     }

     /**
      * 获取地区列表
      */
      public function citys($pid){
          $citys = DB::table('citys')->where('pid',$pid)->get();
          return response()->json($citys);
      }
}
