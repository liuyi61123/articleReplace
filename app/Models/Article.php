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

        $params = $article->params;

        //计算要替换的内容
        foreach ($params as $param) {
            $contents = explode(PHP_EOL, $param->content);
            foreach ($contents as $content) {
                $text['title'] =  str_replace($param->name,$content,$article->title);
                $text['keywords]'] = str_replace($param->name,$content,$article->keywords);
                $text['description'] = str_replace($param->name,$content,$article->description);
                $text['content'] = str_replace($param->name,$content,$article->content);
                $text = implode(PHP_EOL, $text);
                //生成文件
                Storage::put($content.time.'.txt',$text);
            }
        }

     }
}
