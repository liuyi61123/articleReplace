<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SnappyImage;
use App\Handlers\OssUploadImageHandler;
use Chumper\Zipper\Zipper;

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

         //自定义参数
         $my_params = $this->config['params'];

         //移除空的自定义参数
         $params = array();
         foreach($my_params as $key=>$param){
             if($param['content']){
                 //切割字符串
                 $param['content'] = explode("\n",trim($param['content']));
                 $params[] = $param;
             }
         }
         //查找模板信息
         $template = Template::find($this->template_id);
         $template_content = $template->content;
         $template_images = $template->images;

         $template_tmp_paragraphs = $template->paragraphs;
         $template_paragraphs = array();
         foreach($template_tmp_paragraphs as $key=>$template_tmp_paragraph){
              $template_paragraphs[$key]['content'] = explode("\n",trim($template_tmp_paragraph['content']));
              $template_paragraphs[$key]['name'] = $template_tmp_paragraph['name'];
              $template_paragraphs[$key]['count'] = substr_count($template_content,$template_tmp_paragraph['name']);
         }

         //查找模板中图片和段落出现的次数
         $template_images_count = substr_count($template_content,'图片');
         // $template_paragraphs_count = substr_count($template_content,'段落');
         //查找地址信息
         $city_name = DB::table('citys')->where('id',$this->config['city']['data'])->value('name');

         //客户姓名
         $names = ['赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨'];
         $sexs = ['先生','女士'];

         $file_base_path = $directory.'/';

         //遍历结果集
         $oss = new OssUploadImageHandler();

         foreach($this->config['countys']['data'] as $county){
             foreach($this->config['cars']['data'] as $car){
                 //查找汽车相关型号
                 $car_models = $car['models'];
                 $car_brand = DB::table('car_infos')->where('id',$car['brand'])->value('name');

                 foreach($car_models as $car_model){
                     if((isset($car_model['name']))&&($car_model_name = $car_model['name'])){
                         //计算车子价格
                         $price = rand($car_model['min'],$car_model['max']);
                         //客户姓名
                         $name = array_random($names).array_random($sexs);

                         //生成第二张图片
                         $image = $this->toImage($this->id,$car_brand.$car_model_name,$price,$name);

                         //替换内容
                         $replace_text =  str_replace('{name}',$name,$template_content);
                         $replace_text =  str_replace('{city}',$city_name,$replace_text);
                         $replace_text =  str_replace('{county}',$county,$replace_text);
                         $replace_text =  str_replace('{car_brand}',$car_brand,$replace_text);
                         $replace_text =  str_replace('{car_model}',$car_model_name,$replace_text);
                         $replace_text =  str_replace('{price}',$price,$replace_text);
                         $replace_text =  str_replace('{image}',$image,$replace_text);
                         //随机替换模板中的段落和图片
                         foreach($template_paragraphs as $template_paragraph){
                             for($i=0;$i<$template_paragraph['count'];$i++){
                                 $replace_text =  preg_replace('/'.$template_paragraph['name'].'/',array_random($template_paragraph['content']),$replace_text,1);
                             }
                         }

                         for($i=0;$i<$template_images_count;$i++){
                             $replace_text =  preg_replace('/{图片}/',array_random($template_images)['url'],$replace_text,1);
                         }

                         //判断自定义参数个数
                         switch (count($params))
                         {
                             case 0:
                                 $sort = array(
                                     $this->config['city']['sort']=>$city_name,
                                     $this->config['countys']['sort']=>$county,
                                     $this->config['cars']['sort']=>$car_brand.$car_model_name,
                                     $this->config['cars']['price_sort']=>$price.'万',
                                 );
                                 ksort($sort);
                                 $title = implode('',$sort);
                                 //替换title
                                 $replace_text =  str_replace('{title}',$title,$replace_text);

                                 //文件名规则生成
                                 $file_path = $file_base_path.'/'.$title.'.txt';
                                 //生成文件
                                 Storage::put($file_path,$replace_text);
                                 break;
                             case 1:
                                 foreach($params[0]['content'] as $param0){
                                     $sort = array(
                                         $this->config['city']['sort']=>$city_name,
                                         $this->config['countys']['sort']=>$county,
                                         $this->config['cars']['sort']=>$car_brand.$car_model_name,
                                         $this->config['cars']['price_sort']=>$price.'万',
                                         $params[0]['sort'] => $param0
                                     );
                                     ksort($sort);
                                     $title = implode('',$sort);
                                     //替换title
                                     $replace_text =  str_replace('{title}',$title,$replace_text);
                                     //文件名规则生成
                                     $file_path = $file_base_path.'/'.$title.'.txt';
                                     //生成文件
                                     Storage::put($file_path,$replace_text);
                                 }
                                 break;
                             case 2:
                                 foreach($params[0]['content'] as $param0){
                                      foreach($params[1]['content'] as $param1){
                                         $sort = array(
                                             $this->config['city']['sort']=>$city_name,
                                             $this->config['countys']['sort']=>$county,
                                             $this->config['cars']['sort']=>$car_brand.$car_model_name,
                                             $this->config['cars']['price_sort']=>$price.'万',
                                             $params[0]['sort'] => $param0,
                                             $params[1]['sort'] => $param1,
                                         );
                                         ksort($sort);
                                         $title = implode('',$sort);
                                         //替换title
                                         $replace_text =  str_replace('{title}',$title,$replace_text);

                                         //文件名规则生成
                                         $file_path = $file_base_path.'/'.$title.'.txt';
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
                                                 $this->config['city']['sort']=>$city_name,
                                                 $this->config['countys']['sort']=>$county,
                                                 $this->config['cars']['sort']=>$car_brand.$car_model_name,
                                                 $this->config['cars']['price_sort']=>$price.'万',
                                                 $params[0]['sort'] => $param0,
                                                 $params[1]['sort'] => $param1,
                                                 $params[2]['sort'] => $param2,
                                             );
                                             ksort($sort);
                                             $title = implode('',$sort);
                                             //替换title
                                             $replace_text =  str_replace('{title}',$title,$replace_text);

                                             //文件名规则生成
                                             $file_path = $file_base_path.'/'.$title.'.txt';
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

      /**
       * 根据参数生成html
       */
      public function toImage($id,$car,$price,$name){
          $html = $this->imageHtml($car,$price,$name);

          $file = $id.'upload.jpg';
          //判断图片文件是否存在，如果存在就先删除
          if(file_exists($file)) unlink($file);

          //生成图片
          SnappyImage::loadHTML($html)->setOption('width', 600)->save($file);

          //上传到oss
          $oss = new OssUploadImageHandler();
          //文件路径
          return  $oss->articleSave($file);
      }

      protected function imageHtml($car,$price,$name){

          //借款期限
          $period = array_random([12,6]);

          //上牌日期(随机2016-2017一个日期)
          $start_time = strtotime('2016-01-01');
          $end_time = strtotime('2017-01-01');
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
                  .title{background-color: #004b92;text-align: center;color: #ffffff;font-size: 20px;font-weight: bold;}
                  .content{width:100%;}
                  table{width:100%;}
                  table tr td { border:1px solid #000000; }
                  .lable{text-align:center; color: #004b92}
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
                          <td class="value">$period 个月</td>
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
                          <td class="value td_6">$need_price 元</td>
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
