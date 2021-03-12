<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Article\Article;
use App\Handlers\OssUploadImageHandler;
use Illuminate\Support\Facades\Storage;

class GenerateArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $param_contents;
    protected $article_custom_params;
    protected $replace_text;
    protected $province;
    protected $city;
    protected $county;
    protected $car;
    protected $template_custom_paragraph;
    protected $template_image;
    protected $fixed_paragraphs_file;
    protected $is_last;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last)
    {
        $this->id = $id;
        $this->param_contents = $param_contents;
        $this->article_custom_params = $article_custom_params;
        $this->replace_text = $replace_text;
        $this->province = $province;
        $this->city = $city;
        $this->county = $county;
        $this->car = $car;
        $this->template_custom_paragraph = $template_custom_paragraph;
        $this->template_image = $template_image;
        $this->fixed_paragraphs_file = $fixed_paragraphs_file;
        $this->is_last = $is_last;
    }

    /**
     * 队列标签
     *
     * @return array
     */
    // public function tags()
    // {
    //     return ['article', 'GenerateArticle:'.$this->article->id];
    // }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $param_contents = $this->param_contents;
        $article_custom_params = $this->article_custom_params;
        $replace_text = $this->replace_text;
        $province = $this->province;
        $city = $this->city;
        $county = $this->county;
        $car = $this->car;
        $template_custom_paragraph = $this->template_custom_paragraph;
        $template_image = $this->template_image;
        $fixed_paragraphs_file = $this->fixed_paragraphs_file;
        $is_last = $this->is_last;

        $directory = 'public/articles/'.$id;
        
        $oss = new OssUploadImageHandler();
        $file_base_path = $directory.'/';

        if($fixed_paragraphs_file){
            $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_file);
            $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
        }

        //随机替换模板中的段落
        foreach($template_custom_paragraph as $template_paragraph){
            $replace_text = str_replace($template_paragraph['identifier'],$template_paragraph['content'],$replace_text);
        }

        //替换模板中的图片
        if($template_image){
            $replace_text =  str_replace('{图片}',$template_image,$replace_text);
        }
        if($province){
            $replace_text =  str_replace('{province}',$province['name'],$replace_text);
            if($province['isTitle']){
                $sort[$province['sort']] = $province['name'];
            }
        }
        if($city){
            $replace_text =  str_replace('{city}',$city['name'],$replace_text);
            if($city['isTitle']){
                $sort[$city['sort']] = $city['name'];
            }
        } 
        if($county){
            $replace_text =  str_replace('{county}',$county['name'],$replace_text);
            if($county['isTitle']){
                $sort[$county['sort']] = $county['name'];
            }
        }
        
        if($car){
            $replace_text =  str_replace('{car_brand}',$car['car_brand'],$replace_text);
            $replace_text =  str_replace('{car_model}',$car['car_model'],$replace_text);
            $replace_text =  str_replace('{price}',$car['price'],$replace_text);

            if($car['isTitle']){
                $sort[$car['sort']] = $car['car_brand'].$car['car_model'];
            }
            if($car['priceIsTitle']){
                $sort[$car['price_sort']] = $car['price'].'万';
            }
        }
        
        foreach($param_contents as $key=>$param_content){
            if($article_custom_params[$key]['isTitle']){
                $sort[$article_custom_params[$key]['sort']] = $param_content;
            }
            $replace_text =  str_replace($article_custom_params[$key]['identifier'],$param_content,$replace_text);
        }

        ksort($sort);
        $title = implode('',$sort);
        //替换title
        
        $replace_text =  str_replace('{title}',$title,$replace_text);
        //文件名规则生成
        $file_path = $file_base_path.'/'.$title.'.txt';
        //生成文件
        Storage::put($file_path,$replace_text);

        if($is_last){
            Article::where('id',$id)->update([
                'status'=>1
            ]);
        }
    }
}
