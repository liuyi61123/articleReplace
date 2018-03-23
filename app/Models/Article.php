<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 此文章相关的参数
     */
    public function params()
    {
        return $this->hasMany(ArticleParam::class);
    }

    /**
     * 导出文件
     */
     public function export($article){
         //先将文章按照格式拼接
         $text = $article->title.PHP_EOL.$article->keywords.PHP_EOL.$article->description.PHP_EOL.$article->content;
         $params = $article->params;

         $texts = array();
         //计算要替换的内容
         foreach ($params as $param) {
             $contents = explode(PHP_EOL, $param->content);
             foreach ($contents as $content) {
                $texts['title'] =  str_replace($param->name,$content,$text);
                //生成文件
                Storage::put($content.time.'.txt',$text);
             }
         }

     }
}
