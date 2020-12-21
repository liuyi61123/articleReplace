<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SnappyImage;
use Chumper\Zipper\Zipper;
use App\Handlers\OssUploadImageHandler;

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
         $template_images = $template->images;
         //查找模板中图片和段落出现的次数
         $template_images_count = substr_count($template_content,'图片');

         $template_fixed_paragraphs = $template->fixed_paragraphs;//固定段落前缀

         //获取文件列表
         $fixed_paragraphs_files = [];
         if($template_fixed_paragraphs){
            $oss = new OssUploadImageHandler();
            $list = $oss->listArrays('',[
               'max-keys'=>1000,
               'prefix'=>'uploads/paragraphs/'.$template_fixed_paragraphs,
               'delimiter'=>'',
               'marker'=>'',
           ]);
           unset($list['list'][0]);
           $fixed_paragraphs_files = $list['list'];
         }
         $template_tmp_paragraphs = $template->custom_paragraphs;
         $template_custom_paragraphs = array();
         foreach($template_tmp_paragraphs as $key=>$template_tmp_paragraph){
            if($template_tmp_paragraph['name']){
                $template_custom_paragraphs[$key]['content'] = explode("\n",trim($template_tmp_paragraph['content']));
                $template_custom_paragraphs[$key]['name'] = $template_tmp_paragraph['name'];
                $template_custom_paragraphs[$key]['count'] = substr_count($template_content,$template_tmp_paragraph['name']);
            }
        }

        //获取自定义参数
        $custom_params = [];
        foreach($this->config['custom_params'] as $custom_param){
            $article_custom_param = Param::find($custom_param['id']);
            $custom_params[] = array_merge($custom_param,$article_custom_param->toArray());
        }

        $template_fixed_params = $template->fixed_params;
        $fixed_params = $this->config['fixed_params'];

        $file_base_path = $directory.'/';
        //判断有那些固定参数
        if(count($template_fixed_params) == 1){
            if(in_array('car',$fixed_params)){
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
                            $this->customParams($custom_params,$replace_text,'','','',$car_arr,$template_custom_paragraphs,$template_images,$template_images_count,$fixed_paragraphs_files);
                        }
                    }
                }
            }
            if(in_array('city',$fixed_params)){
                $province_name = DB::table('citys')->where('id',$fixed_params['province']['data'])->value('name');
                $city_name = DB::table('citys')->where('id',$fixed_params['city']['data'])->value('name');
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

                    $this->customParams($custom_params,$replace_text,$province_arr ,$city_arr,$county_arr,'',$template_custom_paragraphs,$template_images,$template_images_count,$fixed_paragraphs_files);
                }
            }
        }elseif(count($template_fixed_params) == 2){
            //两个固定参数
            $province_name = DB::table('citys')->where('id',$fixed_params['province']['data'])->value('name');
            $city_name = DB::table('citys')->where('id',$fixed_params['city']['data'])->value('name');
            
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
                            $this->customParams($custom_params,$replace_text,$province_arr,$city_arr,$county_arr,$car_arr,$template_custom_paragraphs,$template_images,$template_images_count,$fixed_paragraphs_files);
                        }
                    }
                }
            }
        }else{
            //没有固定参数，判断自定义参数
            $this->customParams($custom_params,$replace_text,'','','','',$template_custom_paragraphs,$template_images,$template_images_count,$fixed_paragraphs_files);
        }

        //压缩输出
        $zipper = new Zipper;
        $base_path = storage_path('app/public/articles/'.$this->id);
        $files = glob($base_path.'/*.txt');
        $zipper->make($base_path.'/articles'.$this->id.'.zip')->add($files)->close();

        //修改文章状态
        DB::table('articles')->where('id',$this->id)->update([
            'status'=>1
        ]);
        return $files;
    }

    protected function customParams($article_custom_params,$replace_text,$province,$city,$county,$car,$template_custom_paragraphs,$template_images,$template_images_count,$fixed_paragraphs_files)
    {
        $directory = 'public/articles/'.$this->id;
        $oss = new OssUploadImageHandler();
        $count_fixed_paragraphs = count($fixed_paragraphs_files);
        $file_base_path = $directory.'/';
        switch(count($article_custom_params))
        {
            case 1:
                $k = 1;
                $sort = array();
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                foreach($custom_params_content0 as $param_content0){
                    //替换固定段落内容
                    if($count_fixed_paragraphs >0){
                        if($k>= $count_fixed_paragraphs){
                            $k = 1;
                        }
                        $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_files[$k]['uid']);
                        $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
                        $k++;
                    }

                    //随机替换模板中的段落和图片
                    foreach($template_custom_paragraphs as $template_paragraph){
                        for($i=0;$i<$template_paragraph['count'];$i++){
                            $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                        }
                    }

                    for($i=0;$i<$template_images_count;$i++){
                        $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
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
                    
                    if($article_custom_params[0]['isTitle']){
                        $sort[$article_custom_params[0]['sort']] = $param_content0;
                    }

                    ksort($sort);
                    $title = implode('',$sort);
                    //替换title
                    $replace_text =  str_replace($article_custom_params[0]['identifier'],$param_content0,$replace_text);
                    $replace_text =  str_replace('{title}',$title,$replace_text);
                    //文件名规则生成
                    $file_path = $file_base_path.'/'.$title.'.txt';
                    //生成文件
                    Storage::put($file_path,$replace_text);
                }
                break;
            case 2:
                $k = 1;
                $sort = array();
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                foreach($custom_params_content0 as $param_content0){
                    foreach($custom_params_content1 as $param_content1){
                        //替换固定段落内容
                        if($count_fixed_paragraphs >0){
                            if($k>= $count_fixed_paragraphs){
                                $k = 1;
                            }
                            $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_files[$k]['uid']);
                            $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
                            $k++;
                        }
                        //随机替换模板中的段落和图片
                        foreach($template_custom_paragraphs as $template_paragraph){
                            for($i=0;$i<$template_paragraph['count'];$i++){
                                $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                            }
                        }

                        for($i=0;$i<$template_images_count;$i++){
                            $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
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

                        if($article_custom_params[0]['isTitle']){
                            $sort[$article_custom_params[0]['sort']] = $param_content0;
                        }  
                        if($article_custom_params[1]['isTitle']){
                            $sort[$article_custom_params[1]['sort']] = $param_content1;
                        }  
                        ksort($sort);
                        $title = implode('',$sort);

                        //替换title
                        $replace_text =  str_replace($article_custom_params[0]['identifier'],$param_content0,$replace_text);
                        $replace_text =  str_replace($article_custom_params[1]['identifier'],$param_content1,$replace_text);
                        $replace_text =  str_replace('{title}',$title,$replace_text);
                        //文件名规则生成
                        $file_path = $file_base_path.'/'.$title.'.txt';
                        //生成文件
                        Storage::put($file_path,$replace_text);
                    }
                }
                break;
            case 3:
                $sort = array();
                $k = 1;
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                foreach($custom_params_content0 as $param_content0){
                    foreach($custom_params_content1 as $param_content1){
                        foreach($custom_params_content2 as $param_content2){
                            //替换固定段落内容
                            if($count_fixed_paragraphs >0){
                                if($k>= $count_fixed_paragraphs){
                                    $k = 1;
                                }
                                $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_files[$k]['uid']);
                                $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
                                $k++;
                            }
                            //随机替换模板中的段落和图片
                            foreach($template_custom_paragraphs as $template_paragraph){
                                for($i=0;$i<$template_paragraph['count'];$i++){
                                    $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                                }
                            }

                            for($i=0;$i<$template_images_count;$i++){
                                $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
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
                            if($article_custom_params[0]['isTitle']){
                                $sort[$article_custom_params[0]['sort']] = $param_content0;
                            }  
                            if($article_custom_params[1]['isTitle']){
                                $sort[$article_custom_params[1]['sort']] = $param_content1;
                            } 
                            if($article_custom_params[2]['isTitle']){
                                $sort[$article_custom_params[2]['sort']] = $param_content2;
                            } 
                            ksort($sort);
                            $title = implode('',$sort);

                            //替换title
                            $replace_text =  str_replace($article_custom_params[0]['identifier'],$param_content0,$replace_text);
                            $replace_text =  str_replace($article_custom_params[1]['identifier'],$param_content1,$replace_text);
                            $replace_text =  str_replace($article_custom_params[2]['identifier'],$param_content2,$replace_text);
                            $replace_text =  str_replace('{title}',$title,$replace_text);
                            //文件名规则生成
                            $file_path = $file_base_path.'/'.$title.'.txt';
                            //生成文件
                            Storage::put($file_path,$replace_text);
                        }
                    }
                }
                break;
            case 4:
                $sort = array();
                $k = 1;
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                $custom_params_content3 = explode("\n",trim($article_custom_params[3]['content']));
                foreach($custom_params_content0 as $param_content0){
                    foreach($custom_params_content1 as $param_content1){
                        foreach($custom_params_content2 as $param_content2){
                            foreach($custom_params_content3 as $param_content3){
                                //替换固定段落内容
                                if($count_fixed_paragraphs >0){
                                    if($k>= $count_fixed_paragraphs){
                                        $k = 1;
                                    }
                                    $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_files[$k]['uid']);
                                    $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
                                    $k++;
                                }
                                //随机替换模板中的段落和图片
                                foreach($template_custom_paragraphs as $template_paragraph){
                                    for($i=0;$i<$template_paragraph['count'];$i++){
                                        $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                                    }
                                }

                                for($i=0;$i<$template_images_count;$i++){
                                    $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
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
                                if($article_custom_params[0]['isTitle']){
                                    $sort[$article_custom_params[0]['sort']] = $param_content0;
                                }  
                                if($article_custom_params[1]['isTitle']){
                                    $sort[$article_custom_params[1]['sort']] = $param_content1;
                                } 
                                if($article_custom_params[2]['isTitle']){
                                    $sort[$article_custom_params[2]['sort']] = $param_content2;
                                } 
                                if($article_custom_params[3]['isTitle']){
                                    $sort[$article_custom_params[3]['sort']] = $param_content3;
                                } 
                                ksort($sort);
                                $title = implode('',$sort);

                                //替换title
                                $replace_text =  str_replace($article_custom_params[0]['identifier'],$param_content0,$replace_text);
                                $replace_text =  str_replace($article_custom_params[1]['identifier'],$param_content1,$replace_text);
                                $replace_text =  str_replace($article_custom_params[2]['identifier'],$param_content2,$replace_text);
                                $replace_text =  str_replace($article_custom_params[3]['identifier'],$param_content3,$replace_text);
                                $replace_text =  str_replace('{title}',$title,$replace_text);
                                //文件名规则生成
                                $file_path = $file_base_path.'/'.$title.'.txt';
                                //生成文件
                                Storage::put($file_path,$replace_text);
                            }
                        }
                    }
                }
                break;
            case 4:
                $sort = array();
                $k = 1;
                $custom_params_content0 = explode("\n",trim($article_custom_params[0]['content']));
                $custom_params_content1 = explode("\n",trim($article_custom_params[1]['content']));
                $custom_params_content2 = explode("\n",trim($article_custom_params[2]['content']));
                $custom_params_content3 = explode("\n",trim($article_custom_params[3]['content']));
                foreach($custom_params_content0 as $param_content0){
                    foreach($custom_params_content1 as $param_content1){
                        foreach($custom_params_content2 as $param_content2){
                            foreach($custom_params_content3 as $param_content3){
                                foreach($custom_params_content4 as $param_content4){
                                    //替换固定段落内容
                                    if($count_fixed_paragraphs >0){
                                        if($k>= 1000){
                                            $k = 1;
                                        }
                                        $fixed_paragraphs_content = $oss->getObject($fixed_paragraphs_files[$k]['uid']);
                                        $replace_text =  str_replace('{fixed_paragraph}',$fixed_paragraphs_content,$replace_text);
                                        $k++;
                                    }
                                    //随机替换模板中的段落和图片
                                    foreach($template_custom_paragraphs as $template_paragraph){
                                        for($i=0;$i<$template_paragraph['count'];$i++){
                                            $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                                        }
                                    }

                                    for($i=0;$i<$template_images_count;$i++){
                                        $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
                                    }
                                    if($province['isTitle']){
                                        $sort[$province['sort']] = $province['data'];
                                    } 
                                    if($city['isTitle']){
                                        $sort[$city['sort']] = $city['data'];
                                    }
                                    if($countys['isTitle']){
                                        $sort[$countys['sort']] = $countys['data'];
                                    }
                                    if($car['isTitle']){
                                        $sort[$car['sort']] = $car['brand'].$car['model'];
                                    }
                                    if($car['priceIsTitle']){
                                        $sort[$car['price_sort']] = $car['price'].'万';
                                    }
                                    if($article_custom_params[0]['isTitle']){
                                        $sort[$article_custom_params[0]['sort']] = $param_content0;
                                    }  
                                    if($article_custom_params[1]['isTitle']){
                                        $sort[$article_custom_params[1]['sort']] = $param_content1;
                                    } 
                                    if($article_custom_params[2]['isTitle']){
                                        $sort[$article_custom_params[2]['sort']] = $param_content2;
                                    } 
                                    if($article_custom_params[3]['isTitle']){
                                        $sort[$article_custom_params[3]['sort']] = $param_content3;
                                    } 
                                    if($article_custom_params[4]['isTitle']){
                                        $sort[$article_custom_params[4]['sort']] = $param_content4;
                                    }
                                    ksort($sort);
                                    $title = implode('',$sort);

                                    //替换title
                                    $replace_text =  str_replace($article_custom_params[0]['identifier'],$param_content0,$replace_text);
                                    $replace_text =  str_replace($article_custom_params[1]['identifier'],$param_content1,$replace_text);
                                    $replace_text =  str_replace($article_custom_params[2]['identifier'],$param_content2,$replace_text);
                                    $replace_text =  str_replace($article_custom_params[3]['identifier'],$param_content3,$replace_text);
                                    $replace_text =  str_replace($article_custom_params[4]['identifier'],$param_content4,$replace_text);
                                    $replace_text =  str_replace('{title}',$title,$replace_text);
                                    //文件名规则生成
                                    $file_path = $file_base_path.'/'.$title.'.txt';
                                    //生成文件
                                    Storage::put($file_path,$replace_text);
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
