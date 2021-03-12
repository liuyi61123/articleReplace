<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Handlers\OssUploadImageHandler;
use App\Jobs\GenerateArticle;

class Article extends Model
{
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];

    /**
     * 此文章相关的参数
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function generate(){
        //删除原文件后，重新生成
        $directory = 'public/articles/'.$this->id;
        Storage::deleteDirectory($directory);
         
        //查找模板信息
        $template = Template::find($this->template_id);
        $template_content = $template->content;
        $replace_text = $template_content;
        $template_fixed_paragraphs = $template->fixed_paragraphs;//固定段落前缀

        //获取文件列表
        $fixed_paragraphs_files = [];
        if($template_fixed_paragraphs){
           $oss = new OssUploadImageHandler();
           $fixed_paragraphs_files = $oss->allList('',[
              'prefix'=>'uploads/paragraphs/'.$template_fixed_paragraphs,
          ]);
        }

        //获取图片列表
        $template_images = [];
        if($template->images){
          $oss = new OssUploadImageHandler();
          $template_images = $oss->allList('',[
             'prefix'=>'uploads/templates/'.$template->images,
          ]);
          if($template_images){
            $template_images = array_column($template_images,'url');
          }
        }

       $template_custom_paragraphs = $template->custom_paragraphs;

        //获取自定义参数
        $custom_params = [];
        foreach($this->config['custom_params'] as $custom_param){
           $article_custom_param = Param::find($custom_param['id']);
           $custom_params[] = array_merge($custom_param,$article_custom_param->toArray());
        }

        $template_fixed_params = $template->fixed_params;
        $fixed_params = $this->config['fixed_params'];

         //判断有那些固定参数
         if(count($template_fixed_params) == 1){
            if(in_array('car',$template_fixed_params)){

                $base_count = 0;
                foreach($fixed_params['cars']['data'] as $car){
                    //查找汽车相关型号
                    $car_models = $car['models'];
                    $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');
   
                    foreach($car_models as $car_model){
                        if((isset($car_model['name']))&&($car_model_name = $car_model['name'])){
                            $base_count ++;
                        }
                    }
                }

                $i = 1;
                foreach($fixed_params['cars']['data'] as $car){
                    //查找汽车相关型号
                    $car_models = $car['models'];
                    $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');
   
                    foreach($car_models as $car_model){
                        if((isset($car_model['name']))&&($car_model_name = $car_model['name'])){
                            //计算车子价格
                            $price = rand($car_model['min'],$car_model['max']);
                        
                            $car_arr = [
                                'sort'=>$fixed_params['cars']['sort'],
                                'price_sort'=>$fixed_params['cars']['price_sort'],
                                'isTitle'=>$fixed_params['cars']['isTitle'],
                                'priceIsTitle'=>$fixed_params['cars']['priceIsTitle'],
                                'car_brand'=>$car_brand,
                                'car_model'=>$car_model['name'],
                                'price'=>$price,
                            ];
                            
                            $is_last = $i==$base_count?true:false;
                            $this->customParams($is_last,$custom_params,$replace_text,'','','',$car_arr,$template_custom_paragraphs,$template_images,$fixed_paragraphs_files);
                            $i++;
                        }
                    }
                }
            }
            if(in_array('city',$template_fixed_params)){
                $province_name = DB::table('citys')->where('id',$fixed_params['province']['data'])->value('name');
                $city_name = DB::table('citys')->where('id',$fixed_params['city']['data'])->value('name');
                
                $base_count = count($fixed_params['countys']['data']);
                $i = 1;
                foreach($fixed_params['countys']['data'] as $county){
                    $county_arr = [
                        'sort'=>$fixed_params['countys']['sort'],
                        'isTitle'=>$fixed_params['countys']['isTitle'],
                        'name'=>$county,
                    ];

                    $province_arr = [
                        'sort'=>$fixed_params['province']['sort'],
                        'isTitle'=>$fixed_params['province']['isTitle'],
                        'name'=>$province_name,
                    ];

                    $city_arr = [
                        'sort'=>$fixed_params['city']['sort'],
                        'isTitle'=>$fixed_params['city']['isTitle'],
                        'name'=>$city_name,
                    ];

                    $is_last = $i==$base_count?true:false;
                    $this->customParams($is_last,$custom_params,$replace_text,$province_arr ,$city_arr,$county_arr,'',$template_custom_paragraphs,$template_images,$fixed_paragraphs_files);
                    $i++;
                }
            }
        }elseif(count($template_fixed_params) == 2){
            //两个固定参数
            $province_name = DB::table('citys')->where('id',$fixed_params['province']['data'])->value('name');
            $city_name = DB::table('citys')->where('id',$fixed_params['city']['data'])->value('name');
            
            $car_count = 0;
            foreach($fixed_params['cars']['data'] as $car){
                //查找汽车相关型号
                $car_models = $car['models'];
                $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');

                foreach($car_models as $car_model){
                    if((isset($car_model['name']))&&($car_model_name = $car_model['name'])){
                        $car_count ++;
                    }
                }
            }

            $city_count = count($fixed_params['countys']['data']);
            $base_count = $city_count * $car_count;

            $i = 1;
            foreach($fixed_params['countys']['data'] as $county){
                foreach($fixed_params['cars']['data'] as $car){
                    //查找汽车相关型号
                    $car_models = $car['models'];
                    $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');
   
                    foreach($car_models as $car_model){
                        if((isset($car_model['name']))&&($car_model_name = $car_model['name'])){
                            //计算车子价格
                            $price = rand($car_model['min'],$car_model['max']);
                            $county_arr = [
                                'sort'=>$fixed_params['countys']['sort'],
                                'isTitle'=>$fixed_params['countys']['isTitle'],
                                'name'=>$county,
                            ];
        
                            $province_arr = [
                                'sort'=>$fixed_params['province']['sort'],
                                'isTitle'=>$fixed_params['province']['isTitle'],
                                'name'=>$province_name,
                            ];
        
                            $city_arr = [
                                'sort'=>$fixed_params['city']['sort'],
                                'isTitle'=>$fixed_params['city']['isTitle'],
                                'name'=>$city_name,
                            ];
                            $car_arr = [
                                'sort'=>$fixed_params['cars']['sort'],
                                'price_sort'=>$fixed_params['cars']['price_sort'],
                                'isTitle'=>$fixed_params['cars']['isTitle'],
                                'priceIsTitle'=>$fixed_params['cars']['priceIsTitle'],
                                'car_brand'=>$car_brand,
                                'car_model'=>$car_model['name'],
                                'price'=>$price,
                            ];

                            $is_last = $i==$base_count?true:false;
                            $this->customParams($is_last,$custom_params,$replace_text,$province_arr,$city_arr,$county_arr,$car_arr,$template_custom_paragraphs,$template_images,$fixed_paragraphs_files);
                            $i++;
                        }
                    }
                }
            }
        }else{
            //没有固定参数，判断自定义参数
            $this->customParams(true,$custom_params,$replace_text,'','','','',$template_custom_paragraphs,$template_images,$fixed_paragraphs_files);
        }
    }

    protected function customParams($is_fixed_last,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraphs,$template_images,$fixed_paragraphs_files)
    {
         $base_replace_text = $replace_text;
         $oss = new OssUploadImageHandler();
         $count_fixed_paragraphs = count($fixed_paragraphs_files);
 
         $k = 1;
         $i = 1;
         $sort = array();
         $is_last = false;
 
         switch(count($article_custom_params))
         {
             case 1:
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                $count_all = count($custom_params_content0);
                foreach($custom_params_content0 as $param_content0){
                    $fixed_paragraphs_file = '';
                    if($is_fixed_last&&($i == $count_all)){
                        $is_last = true;
                    }else{
                        $is_last = false;
                    }
 
                     if($count_fixed_paragraphs >0){
                         if($k>= $count_fixed_paragraphs){
                             $k = 1;
                         }
                         $fixed_paragraphs_file = $fixed_paragraphs_files[$k]['uid'];
                     }
 
                     $param_contents = [
                         $param_content0
                     ];

                    //随机图片和段落
                    $template_custom_paragraph = [];
                    foreach($template_custom_paragraphs as $custom_paragraph){
                        $paragraph_tmp = Paragraph::find($custom_paragraph);

                        $paragraph_tmp_content = explode("\n",trim($paragraph_tmp->content));
                        $template_custom_paragraph[] = [
                            'identifier'=>$paragraph_tmp->identifier,
                            'content'=>array_random($paragraph_tmp_content),
                        ];
                    }
                    $template_image = !empty($template_images)?array_random($template_images):'';

                    GenerateArticle::dispatch($this->id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last);
                    $replace_text = $base_replace_text;
                    $k++;
                    $i++;
                }
                 break;
             case 2:
                 $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                 $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                 
                 $count_all = count($custom_params_content0)*count($custom_params_content1);
                 foreach($custom_params_content0 as $param_content0){
                     foreach($custom_params_content1 as $param_content1){
                         $fixed_paragraphs_file = '';
                         if($is_fixed_last&&($i == $count_all)){
                             $is_last = true;
                         }else{
                             $is_last = false;
                         }
 
                         if($count_fixed_paragraphs >0){
                             if($k>= $count_fixed_paragraphs){
                                 $k = 1;
                             }
                             $fixed_paragraphs_file = $fixed_paragraphs_files[$k]['uid'];
                         }
     
                         $param_contents = [
                             $param_content0,
                             $param_content1
                         ];

                        //随机图片和段落
                        $template_custom_paragraph = [];
                        foreach($template_custom_paragraphs as $custom_paragraph){
                            $paragraph_tmp = Paragraph::find($custom_paragraph);

                            $paragraph_tmp_content = explode("\n",trim($paragraph_tmp->content));
                            $template_custom_paragraph[] = [
                                'identifier'=>$paragraph_tmp->identifier,
                                'content'=>array_random($paragraph_tmp_content),
                            ];
                        }
                        $template_image = !empty($template_images)?array_random($template_images):'';

                        GenerateArticle::dispatch($this->id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last);
                        $replace_text = $base_replace_text;
                        $k++;
                        $i++;
                     }
                 }
                 break;
             case 3:
                 $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                 $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                 $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                 
                 $count_all = count($custom_params_content0)*count($custom_params_content1)*count($custom_params_content2);
                 foreach($custom_params_content0 as $param_content0){
                     foreach($custom_params_content1 as $param_content1){
                         foreach($custom_params_content2 as $param_content2){
                             $fixed_paragraphs_file = '';
                             if($is_fixed_last&&($i == $count_all)){
                                 $is_last = true;
                             }else{
                                 $is_last = false;
                             }        
 
                             if($count_fixed_paragraphs >0){
                                 if($k>= $count_fixed_paragraphs){
                                     $k = 1;
                                 }
                                 $fixed_paragraphs_file = $fixed_paragraphs_files[$k]['uid'];
                             }
         
                             $param_contents = [
                                 $param_content0,
                                 $param_content1,
                                 $param_content2
                             ];

                             //随机图片和段落
                            $template_custom_paragraph = [];
                            foreach($template_custom_paragraphs as $custom_paragraph){
                                $paragraph_tmp = Paragraph::find($custom_paragraph);

                                $paragraph_tmp_content = explode("\n",trim($paragraph_tmp->content));
                                $template_custom_paragraph[] = [
                                    'identifier'=>$paragraph_tmp->identifier,
                                    'content'=>array_random($paragraph_tmp_content),
                                ];
                            }
                            $template_image = !empty($template_images)?array_random($template_images):'';

                            GenerateArticle::dispatch($this->id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last);
                            $replace_text = $base_replace_text;
                            $k++;
                            $i++;
                         }
                     }
                 }
                 break;
             case 4:
                 $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                 $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                 $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                 $custom_params_content3 = explode("\n",trim($article_custom_params[3]['content']));
                 
                 $count_all = count($custom_params_content0)*count($custom_params_content1)*count($custom_params_content2)*count($custom_params_content3);
                 foreach($custom_params_content0 as $param_content0){
                     foreach($custom_params_content1 as $param_content1){
                         foreach($custom_params_content2 as $param_content2){
                             foreach($custom_params_content3 as $param_content3){
                                 $fixed_paragraphs_file = '';
                                 if($is_fixed_last&&($i == $count_all)){
                                     $is_last = true;
                                 }else{
                                     $is_last = false;
                                 }
             
                                 if($count_fixed_paragraphs >0){
                                     if($k>= $count_fixed_paragraphs){
                                         $k = 1;
                                     }
                                     $fixed_paragraphs_file = $fixed_paragraphs_files[$k]['uid'];
                                 }
             
                                 $param_contents = [
                                     $param_content0,
                                     $param_content1,
                                     $param_content2,
                                     $param_content3,
                                 ];

                                //随机图片和段落
                                $template_custom_paragraph = [];
                                foreach($template_custom_paragraphs as $custom_paragraph){
                                    $paragraph_tmp = Paragraph::find($custom_paragraph);

                                    $paragraph_tmp_content = explode("\n",trim($paragraph_tmp->content));
                                    $template_custom_paragraph[] = [
                                        'identifier'=>$paragraph_tmp->identifier,
                                        'content'=>array_random($paragraph_tmp_content),
                                    ];
                                }
                                $template_image = !empty($template_images)?array_random($template_images):'';

                                GenerateArticle::dispatch($this->id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last);
                                $replace_text = $base_replace_text;
                                $k++;
                                $i++;
                             }
                         }
                     }
                 }
                 break;
             case 5:
                 $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                 $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                 $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                 $custom_params_content3 = explode("\n",trim($article_custom_params[3]['content']));
                 $custom_params_content4 = explode("\n",trim($article_custom_params[4]['content']));
                 
                 $count_all = count($custom_params_content0)*count($custom_params_content1)*count($custom_params_content2)*count($custom_params_content3)*count($custom_params_content4);
                 foreach($custom_params_content0 as $param_content0){
                     foreach($custom_params_content1 as $param_content1){
                         foreach($custom_params_content2 as $param_content2){
                             foreach($custom_params_content3 as $param_content3){
                                 foreach($custom_params_content4 as $param_content4){
                                     $fixed_paragraphs_file = '';
                                     if($is_fixed_last&&($i == $count_all)){
                                         $is_last = true;
                                     }else{
                                         $is_last = false;
                                     }
                 
                                     if($count_fixed_paragraphs >0){
                                         if($k>= $count_fixed_paragraphs){
                                             $k = 1;
                                         }
                                         $fixed_paragraphs_file = $fixed_paragraphs_files[$k]['uid'];
                                     }
                 
                                    $param_contents = [
                                         $param_content0,
                                         $param_content1,
                                         $param_content2,
                                         $param_content3,
                                         $param_content4,
                                    ];

                                     //随机图片和段落
                                    $template_custom_paragraph = [];
                                    foreach($template_custom_paragraphs as $custom_paragraph){
                                        $paragraph_tmp = Paragraph::find($custom_paragraph);

                                        $paragraph_tmp_content = explode("\n",trim($paragraph_tmp->content));
                                        $template_custom_paragraph[] = [
                                            'identifier'=>$paragraph_tmp->identifier,
                                            'content'=>array_random($paragraph_tmp_content),
                                        ];
                                    }
                                    $template_image = !empty($template_images)?array_random($template_images):'';

                                    GenerateArticle::dispatch($this->id,$param_contents,$article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraph,$template_image,$fixed_paragraphs_file,$is_last);
                                    $replace_text = $base_replace_text;
                                    $k++;
                                    $i++;
                                 }
                             }
                         }
                     }
                 }
                 break;
             default:
                 break;
         };
         
     }
}
