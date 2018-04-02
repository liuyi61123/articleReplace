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
     function replace($article)
     {
         //先将文章按照格式拼接
         $zipper = new \Chumper\Zipper\Zipper;
         $params = $article['params'];
         $text = $article['title'].PHP_EOL.$article['keywords'].PHP_EOL.$article['description'].PHP_EOL.$article['content'];

         $file_base_path = 'public/articles/'.$article['id'].'/';
         $num = count($params);
         $texts = array();
         switch ($num) {
             case '1':
                 $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
                 foreach ($contents0 as $content0) {
                     $replace_text =  str_replace($params[0]['name'],$content0,$text);
                     //生成文件名称
                     $file_path = $file_base_path.$content0.'.txt';
                     //生成文件
                     Storage::put($file_path,$replace_text);
                 }
                 break;
             case '2':
                 $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
                 $contents1 = array_unique(array_filter(explode(',',$params[1]['content'])));
                 foreach ($contents0 as $content0) {
                      foreach ($contents1 as $content1) {
                          $replace_text =  str_replace($params[0]['name'],$content0,$text);
                          $replace_text =  str_replace($params[1]['name'],$content1,$replace_text);
                          //生成文件名称
                          $file_path = $file_base_path.$content0.'-'.$content1.'.txt';
                          //生成文件
                          Storage::put($file_path,$replace_text);
                      }
                 }
                 break;
             case '3':
                 $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
                 $contents1 = array_unique(array_filter(explode(',',$params[1]['content'])));
                 $contents2 = array_unique(array_filter(explode(',',$params[2]['content'])));
                 foreach ($contents0 as $content0) {
                      foreach ($contents1 as $content1) {
                           foreach ($contents2 as $content2) {
                               $replace_text =  str_replace($params[0]['name'],$content0,$text);
                               $replace_text =  str_replace($params[1]['name'],$content1,$replace_text);
                               $replace_text =  str_replace($params[2]['name'],$content2,$replace_text);
                               //生成文件名称
                               $file_path = $file_base_path.$content0.'-'.$content1.'-'.$content2.'.txt';
                               //生成文件
                               Storage::put($file_path,$replace_text);
                           }
                      }
                 }
                 break;
             case '4':
                 $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
                 $contents1 = array_unique(array_filter(explode(',',$params[1]['content'])));
                 $contents2 = array_unique(array_filter(explode(',',$params[2]['content'])));
                 $contents3 = array_unique(array_filter(explode(',',$params[3]['content'])));
                 foreach ($contents0 as $content0) {
                      foreach ($contents1 as $content1) {
                          foreach ($contents2 as $content2) {
                               foreach ($contents3 as $content3) {
                                   $replace_text =  str_replace($params[0]['name'],$content0,$text);
                                   $replace_text =  str_replace($params[1]['name'],$content1,$replace_text);
                                   $replace_text =  str_replace($params[2]['name'],$content3,$replace_text);
                                   $replace_text =  str_replace($params[3]['name'],$content2,$replace_text);
                                   //生成文件名称
                                   $file_path = $file_base_path.$content0.'-'.$content1.'-'.$content2.'-'.$content3.'.txt';
                                   //生成文件
                                   Storage::put($file_path,$replace_text);
                               }
                           }
                      }
                 }
                 break;
             case '5':
                 $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
                 $contents1 = array_unique(array_filter(explode(',',$params[1]['content'])));
                 $contents2 = array_unique(array_filter(explode(',',$params[2]['content'])));
                 $contents3 = array_unique(array_filter(explode(',',$params[3]['content'])));
                 $contents4 = array_unique(array_filter(explode(',',$params[4]['content'])));
                 foreach ($contents0 as $content0) {
                      foreach ($contents1 as $content1) {
                          foreach ($contents2 as $content2) {
                              foreach ($contents3 as $content3) {
                                  foreach ($contents4 as $content4) {
                                       $replace_text =  str_replace($params[0]['name'],$content0,$text);
                                       $replace_text =  str_replace($params[1]['name'],$content1,$replace_text);
                                       $replace_text =  str_replace($params[2]['name'],$content2,$replace_text);
                                       $replace_text =  str_replace($params[3]['name'],$content3,$replace_text);
                                       $replace_text =  str_replace($params[4]['name'],$content4,$replace_text);
                                       //生成文件名称
                                       $file_path = $file_base_path.$content0.'-'.$content1.'-'.$content2.'-'.$content3.'-'.$content4.'.txt';
                                       //生成文件
                                       Storage::put($file_path,$replace_text);
                                   }
                               }
                           }
                      }
                 }
                 break;
             default:
                 return false;
                 break;
         }
         $files = glob('storage/articles/'.$article['id'].'/'.'*.txt');
         $zipper->make('storage/articles/'.$article['id'].'/'.'articles'.$article['id'].'.zip')->add($files)->close();
         return true;
     }
}
