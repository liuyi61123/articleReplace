<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SnappyImage;

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
//          $image1 = 'image';

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
          $image_name = time().$id.'article.png';
          SnappyImage::loadHTML($html)->setOption('width', 600)->save($image_name);
          return $image_name;
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
                          <td class="value td_6">王先生</td>
                          <td class="lable td_6">户口</td>
                          <td class="value td_6">上海</td>
                          <td class="lable td_6">家庭状况</td>
                          <td class="value td_6">已婚</td>
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
                          <td class="lable">车辆型号</td>
                          <td class="value" colspan="3">本田雅阁 2015款 2.4 VIP版</td>
                          <td class="lable">拍照</td>
                          <td class="value">沪大牌</td>
                      </tr>
                      <tr>
                          <td class="lable">出厂日期</td>
                          <td class="value">2015年11月</td>
                          <td class="lable">上牌日期</td>
                          <td class="value">2016年4月</td>
                          <td class="lable">全款买车</td>
                          <td class="value">是</td>
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
                          <td class="lable">加15万</td>
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
                          <td class="value td_6">25万</td>
                          <td class="lable td_6">客户需要</td>
                          <td class="value td_6">23万</td>
                      </tr>
                  </table>
              </div>

              <div class="title">
                  <span>23万 还款计算表</span>
              </div>
              <div class="content">
                  <table border="1" cellspacing="0" bordercolor="#a0c6e5" style="border-collapse:collapse;">
                      <tr>
                          <td class="lable td_6">借款金额</td>
                          <td class="value td_6">230000</td>
                          <td class="lable td_6">借款利率</td>
                          <td class="value td_6">0.95</td>
                          <td class="lable td_6">贷款时间</td>
                          <td class="value td_6">12个月</td>
                      </tr>
                      <tr>
                          <td class="lable">总利息</td>
                          <td class="value">26220元</td>
                          <td class="lable">每月利息</td>
                          <td class="value">2185元</td>
                          <td class="lable">折合每日</td>
                          <td class="value">72元</td>
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
