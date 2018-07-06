<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取用户的名字。
     *
     * @param  string  $value
     * @return string
     */
     public function getConfigAttribute($value)
     {
         return json_decode($value);
         // return '<pre>'.json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
     }

    /**
     * 此文章相关的参数
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    
     /**
      * 生成
      */
      public function generate($data,$id){
          //自定义参数
          $params = $data['params'];
          //查找模板信息
          $template_content = Template::where('id',$data['template_id'])->value('content');
          //查找地址信息
          $city_name = DB::table('citys')->where('id',$data['city'])->value('name');

          $file_base_path = 'public/articles/'.$id.'/';

          $image1 = $this->toImage($id);

          //遍历结果集
          foreach($data['countys'] as $county){
              foreach($data['cars'] as $car){
                  //查找汽车相关型号
                  $pid = DB::table('car_infos')->where('name',$car)->value('id');
                  $car_models = DB::table('car_infos')->where('pid',$pid)->pluck('name');

                  foreach($car_models as $car_model){
                      foreach($params as $param){
                          if($param['content']){
                              foreach($param['content'] as $param_content){
                                 //替换内容
                                 $replace_text =  str_replace('{county}',$county,$template_content);
                                 $replace_text =  str_replace('{car}',$car,$replace_text);
                                 $replace_text =  str_replace('{car_model}',$car_model,$replace_text);
                                 $replace_text =  str_replace('{image1}',$image1,$replace_text);

                                 //文件名规则生成
                                 $file_path = $file_base_path.'/'.$city_name.$county.$car.$car_model.$param_content.'.txt';
                                 //生成文件
                                 Storage::put($file_path,$replace_text);
                              }
                          }else{
                              //替换内容
                              $replace_text =  str_replace('{county}',$county,$template_content);
                              $replace_text =  str_replace('{car}',$car,$replace_text);
                              $replace_text =  str_replace('{car_model}',$car_model,$replace_text);
                              $replace_text =  str_replace('{image1}',$image1,$replace_text);

                              //文件名规则生成
                              $file_path = $file_base_path.'/'.$city_name.$county.$car.$car_model.'.txt';
                              //生成文件
                              Storage::put($file_path,$replace_text);
                          }
                      }
                  }
              }
          }

          //压缩输出
          $zipper = new \Chumper\Zipper\Zipper;
          $base_path = storage_path('app/public/articles/'.$id);
          $files = glob($base_path.'/*.txt');
          $zipper->make($base_path.'/articles'.$id.'.zip')->add($files)->close();
          return $files;
      }

      /**
       * 根据参数生成html
       */
      public function toImage($id){
          $html = $this->imageHtml();
          \SnappyImage::loadHTML($html)->setOption('width', 600)->save(time().$id.'article.png');
          return $id.'article.png';
      }

      protected function imageHtml(){
          $html = <<<HTML
          <!DOCTYPE html>
          <html lang="en">
          <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
              <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
              <title>title</title>
              <style >
                  html,body{margin: 0px;padding: 0px;}
                  .title{background-color: red;text-align: center;color: #ffffff;font-size: 25px;}
                  .content{width:100%;}
                  table{width:100%;}
                  table tr td { border:1px solid red; }
                  .lable{color: red}
                  .value{text-align:center}
              </style>
          </head>
          <body>
              <div class="title">
                  <span>车辆贷款审批表</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0">
                      <tr>
                          <td class="lable">客户姓名</td>
                          <td class="value">王先生</td>
                          <td class="lable">户口</td>
                          <td class="va">上海</td>
                          <td class="lable">家庭状况</td>
                          <td class="value">已婚</td>
                      </tr>
                      <tr>
                          <td class="lable">借款金额</td>
                          <td class="value">20万</td>
                          <td class="lable">借款期限</td>
                          <td class="va">12个月</td>
                          <td class="lable">借款类型</td>
                          <td class="value">等额本息</td>
                      </tr>
                      <tr>
                          <td class="lable">客户姓名</td>
                          <td class="value">王先生</td>
                          <td class="lable">户口</td>
                          <td class="va">上海</td>
                          <td class="lable">家庭状况</td>
                          <td class="value">已婚</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>初审评估</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0">
                      <tr>
                          <td class="value" colspan="4">提额条件</td>
                          <td class="value">附加条件</td>
                          <td colspan="1"></td>
                      </tr>
                      <tr>
                          <td colspan="4">1车辆本身评估价格</td>
                          <td colspan="1">加9万</td>
                          <td></td>
                      </tr>
                      <tr>
                          <td colspan="4">1车辆本身评估价格</td>
                          <td colspan="1">加9万</td>
                          <td colspan="1"></td>
                      </tr>
                  </table>
              </div>
          </body>
          </html>
HTML;

        return $html;
      }
}
