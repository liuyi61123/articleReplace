<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}


/**
 * 导出文件
 */
 function export($article)
 {
     //先将文章按照格式拼接
     $zipper = new \Chumper\Zipper\Zipper;
     $params = $article['params'];
     $text = $article['title'].PHP_EOL.$article['keywords'].PHP_EOL.$article['description'].PHP_EOL.$article['content'];

     $file_base_path = 'articles/'.$article['id'].'/';
     $num = count($params);
     $texts = array();
     switch ($num) {
         case '1':
             $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
             foreach ($contents0 as $content0) {
                 $replace_text =  str_replace($params[0]['name'],$content0,$text);
                 //生成文件
                 $file_path = $file_base_path.$content0.'.txt';
                 Storage::put($file_path,$replace_text);
             }
             return true;
             break;
         case '2':
             $contents0 = array_unique(array_filter(explode(',',$params[0]['content'])));
             $contents1 = array_unique(array_filter(explode(',',$params[1]['content'])));
             foreach ($contents0 as $content0) {
                  foreach ($contents1 as $content1) {
                      $replace_text =  str_replace($params[0]['name'],$content0,$text);
                      $replace_text =  str_replace($params[1]['name'],$content1,$replace_text);
                      //生成文件
                      $file_path = $file_base_path.$content0.'-'.$content1.'.txt';
                      Storage::put($file_path,$replace_text);
                  }
             }
             return true;
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
                           //生成文件
                           $file_path = $file_base_path.$content0.'-'.$content1.'-'.$content2.'.txt';
                           //生成文件
                           Storage::put($file_path,$replace_text);
                       }
                  }
             }
             return true;
             break;

         default:
             return false;
             break;
     }
     $zipper->make('test.zip')->folder( storage_path('app').$file_base_path)->close();
 }
