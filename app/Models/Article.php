<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SnappyImage;
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
          $my_params = $data['params'];

          //移除空的自定义参数
          $params = array();
          foreach($my_params as $param){
              if($param['content']){
                  $params[] = $param;
              }
          }
          //查找模板信息
          $template_content = Template::where('id',$data['template_id'])->value('content');
          //查找地址信息
          $city_name = DB::table('citys')->where('id',$data['city']['data'])->value('name');

          $file_base_path = 'public/articles/'.$id.'/';

          //遍历结果集
          $oss = new OssUploadImageHandler();
          $oss_base_url = 'http://article-1.oss-cn-hangzhou.aliyuncs.com/';
          foreach($data['countys']['data'] as $county){
              foreach($data['cars']['data'] as $car){
                  //查找汽车相关型号
                  $car_models = $car['models'];
                  $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');

                  foreach($car_models as $car_model){
                      if($car_model_name = $car_model['name']){
                          //计算车子价格
                          $price = rand($car_model['min'],$car_model['max']);

                          //从库中随机找出图片
                          $oss_imges = $oss->listArrays(env('OSS_BUCKET'),[
                              'max-keys'=>1000,
                              'prefix'=>'cars/'.$car_brand.'/'.$car_model_name,
                              'delimiter'=>'',
                              'marker'=>'',
                          ]);
                          if($oss_imges){
                               $image1 = $oss_base_url.array_random($oss_imges);
                          }else{
                               $image1 = 'http://article-1.oss-cn-hangzhou.aliyuncs.com/cars/宝马/x1/1.jpg';
                          }

                          //生成第二张图片
                          $image2 = $this->toImage($id,$car_brand.$car_model_name,$price);

                          //替换内容
                          $replace_text =  str_replace('{city}',$city_name,$template_content);
                          $replace_text =  str_replace('{county}',$county,$template_content);
                          $replace_text =  str_replace('{car_brand}',$car_brand,$replace_text);
                          $replace_text =  str_replace('{car_model}',$car_model_name,$replace_text);
                          $replace_text =  str_replace('{price}',$price,$replace_text);
                          $replace_text =  str_replace('{image1}',$image1,$replace_text);
                          $replace_text =  str_replace('{image2}',$image2,$replace_text);

                          //判断自定义参数个数
                          switch (count($params))
                          {
                              case 0:
                                  $sort = array(
                                      $data['city']['sort']=>$city_name,
                                      $data['countys']['sort']=>$county,
                                      $data['cars']['sort']=>$car_brand.$car_model_name,
                                      $data['cars']['price_sort']=>$price.'万',
                                  );
                                  ksort($sort);
                                  //文件名规则生成
                                  $file_path = $file_base_path.'/'.implode('',$sort).'.txt';
                                  //生成文件
                                  Storage::put($file_path,$replace_text);
                                  break;
                              case 1:
                                  foreach($params[0]['content'] as $param0){
                                      $sort = array(
                                          $data['city']['sort']=>$city_name,
                                          $data['countys']['sort']=>$county,
                                          $data['cars']['sort']=>$car_brand.$car_model_name,
                                          $data['cars']['price_sort']=>$price.'万',
                                          $params[0]['sort'] => $param0
                                      );
                                      ksort($sort);
                                      //文件名规则生成
                                      $file_path = $file_base_path.'/'.implode('',$sort).'.txt';
                                      //生成文件
                                      Storage::put($file_path,$replace_text);
                                  }
                                  break;
                              case 2:
                                  foreach($params[0]['content'] as $param0){
                                       foreach($params[1]['content'] as $param1){
                                          $sort = array(
                                              $data['city']['sort']=>$city_name,
                                              $data['countys']['sort']=>$county,
                                              $data['cars']['sort']=>$car_brand.$car_model_name,
                                              $data['cars']['price_sort']=>$price.'万',
                                              $params[0]['sort'] => $param0,
                                              $params[1]['sort'] => $param1,
                                          );
                                          ksort($sort);
                                          //文件名规则生成
                                          $file_path = $file_base_path.'/'.implode('',$sort).'.txt';
                                          //生成文件
                                          Storage::put($file_path,$replace_text);
                                      }
                                  }
                                  break;
                              case 3:
                                  foreach($params[0]['content'] as $param0){
                                      foreach($params[1]['content'] as $param1){
                                          foreach($params[2]['content'] as $param2){
                                              $sort = array(
                                                  $data['city']['sort']=>$city_name,
                                                  $data['countys']['sort']=>$county,
                                                  $data['cars']['sort']=>$car_brand.$car_model_name,
                                                  $data['cars']['price_sort']=>$price.'万',
                                                  $params[0]['sort'] => $param0,
                                                  $params[1]['sort'] => $param1,
                                                  $params[2]['sort'] => $param2,
                                              );
                                              ksort($sort);
                                              //文件名规则生成
                                              $file_path = $file_base_path.'/'.implode('',$sort).'.txt';
                                              //生成文件
                                              Storage::put($file_path,$replace_text);
                                          }
                                      }
                                  }
                                  break;
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
      public function toImage($id,$car,$price){
          $html = $this->imageHtml($car,$price);

          $file = 'upload.png';
          //生成图片
          SnappyImage::loadHTML($html)->setOption('width', 600)->save($file);
          //上传到oss
          $oss = new OssUploadImageHandler();
          return $oss->articleSave($file);
      }

      protected function imageHtml($car,$price){
          //客户姓名
          $names = ['赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨'];
          $sexs = ['先生','女士'];
          $name = array_random($names).array_random($sexs);

          //借款期限
          $period = array_random([12,6]);

          //上牌日期(随机2016-2017一个日期)
          $start_time = strtotime('2016-01-01');
          $end_time = strtotime('2016-01-01');
          $boarding = date('Y年m月',rand($start_time,$end_time));

          //汽车本身评估价格
          $evaluation_car = $price-8-rand(1,3);
          //额度评估
          $evaluation = round($price*(100+rand(10,20))/100,1);
          //客户需要
          $need_price = $price*10000;
          //总利息
          $all_interest = round($price*$period*10000*0.95/100,0);

          //每月利息
          $month_interest = round($price*10000*0.95/100,0);

          //每日利息
          $date_interest = round($month_interest/30,0);

          $html = <<<HTML
          <!DOCTYPE html>
          <html lang="en">
          <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
              <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
              <title>title</title>
              <style >
                  html,body{margin: 0px;padding: 0px;}
                  .title{background-color: #C00000;text-align: center;color: #ffffff;font-size: 20px;font-weight: bold;}
                  .content{width:100%;}
                  table{width:100%;}
                  table tr td { border:1px solid #000000; }
                  .lable{text-align:center; color: #C00000}
                  .value{text-align:center}
                  .td_2{width: 50%;}
                  .td_6{width: 16.6%;}
              </style>
          </head>
          <body>
              <div class="title">
                  <span>车辆贷款审批表</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0" bordercolor="#a0c6e5" style="border-collapse:collapse;">
                      <tr>
                          <td class="lable td_6">客户姓名</td>
                          <td class="value td_6"> $name </td>
                          <td class="lable td_6">户口</td>
                          <td class="value td_6">上海</td>
                          <td class="lable td_6">家庭状况</td>
                          <td class="value td_6">已婚</td>
                      </tr>
                      <tr>
                          <td class="lable">借款金额</td>
                          <td class="value">$price 万</td>
                          <td class="lable">借款期限</td>
                          <td class="va">$period 个月</td>
                          <td class="lable">借款类型</td>
                          <td class="value">等额本息</td>
                      </tr>
                      <tr>
                          <td class="lable">车辆型号</td>
                          <td class="value" colspan="3">$car</td>
                          <td class="lable">拍照</td>
                          <td class="value">沪大牌</td>
                      </tr>
                      <tr>
                          <td class="lable">上牌日期</td>
                          <td class="value">$boarding</td>
                          <td class="lable">全款买车</td>
                          <td class="value">是</td>
                          <td class="lable">车辆状况</td>
                          <td class="value">良好</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>初审评估</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0" bordercolor="#a0c6e5" style="border-collapse:collapse;">
                      <tr>
                          <td class="value td_2">提额条件</td>
                          <td class="value td_2">附加条件</td>
                      </tr>
                      <tr>
                          <td class="value">汽车本身评估价格</td>
                          <td class="lable">加 $evaluation_car 万</td>
                      </tr>
                      <tr>
                          <td class="value">汽车拍照为沪大牌</td>
                          <td class="lable">加8万</td>
                      </tr>
                      <tr>
                          <td class="value">登记证,身份证,行驶证,保险单,备用钥匙</td>
                          <td class="lable">原件齐全</td>
                      </tr>
                      <tr>
                          <td class="value">借款人为上海人</td>
                          <td class="lable">可加额度</td>
                      </tr>
                      <tr>
                          <td class="value">借款人在上海有房产</td>
                          <td class="lable">可加额度</td>
                      </tr>
                      <tr>
                          <td class="value">汽车保养良好,行驶少于10万公里</td>
                          <td class="lable">可加额度</td>
                      </tr>
                      <tr>
                          <td class="value">借款人征信良好流水正常</td>
                          <td class="lable">可加额度</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>评估结果</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0" bordercolor="#a0c6e5" style="border-collapse:collapse;">
                      <tr>
                          <td class="lable td_6">风控意见</td>
                          <td class="value td_6">通过</td>
                          <td class="lable td_6">额度评估</td>
                          <td class="value td_6">$evaluation 万</td>
                          <td class="lable td_6">客户需要</td>
                          <td class="value td_6">$price 万</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>$price 万还款计算表</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0" bordercolor="#a0c6e5" style="border-collapse:collapse;">
                      <tr>
                          <td class="lable td_6">借款金额</td>
                          <td class="value td_6">$need_price </td>
                          <td class="lable td_6">借款利率</td>
                          <td class="value td_6">0.95</td>
                          <td class="lable td_6">贷款时间</td>
                          <td class="value td_6">$period 个月</td>
                      </tr>
                      <tr>
                          <td class="lable">总利息</td>
                          <td class="value">$all_interest 元</td>
                          <td class="lable">每月利息</td>
                          <td class="value">$month_interest 元</td>
                          <td class="lable">折合每日</td>
                          <td class="value">$date_interest 元</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>注:以上信息仅供参考</span>
              </div>

          </body>
          </html>
HTML;

        return $html;
      }
}
