<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use OSS\Core\OssException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function test1(){

        $body = [
            'form_params'=>[
                'txt'=>'本公司常年从事软件开发工作，经验丰富',
                'th'=>3
            ]
        ];
        $header = [
            'headers' => [
                'Content-Type'=>'application/x-www-form-urlencoded',
                'Authorization' => '96BD53AD97644476891D41753BAFCFC5',
            ]
        ];
        $client = new Client([
            'base_uri' => 'http://apis.5118.com/',
            'headers' => [
                'Content-Type'=>'application/x-www-form-urlencoded',
                'Authorization' => 'APIKEY 96BD53AD97644476891D41753BAFCFC5',
            ]
        ]);
        dump($client);
        $brand_api = 'wyc/akey';
        $response = $client->request('POST', $brand_api,$body);
        dump($client);
        dump($response);
        $brands = json_decode($response->getBody()->getContents(),true);
        dump($brands);
    }

    public function test2(){
        $files = Storage::files('public/original/start/1');
        foreach($files as $file){
            $content = Storage::get($file);
            dump($content);
        }
    }

    public function test(){
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
        $file = 'upload.png';
        \SnappyImage::loadHTML($html)->setOption('width', 600)->save($file);

        $folder_name = "uploads/article/" . date("Ym", time()) . '/'.date("d", time()).'/';
        $filename = $folder_name . '_' . time() . '_' . str_random(10) . '.png' ;
        try{
            $upload = \OSS::uploadFile(env('OSS_BUCKET'), $filename, $file);
            $result = $upload['info']['url'];
            //删除upload.png
            unlink($file);
        }catch(OssException $e){
            Log::error($e);
            $result = false;
        }
        dd($result);

    }
}
