<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $user_id = 'wxoz7jOwFs14YJIPAvoYocZwI39NW4';
        $package = 'package1';
        return md5($user_id.$package);
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
        return  \SnappyImage::loadHTML($html)->setOption('width', 600)->inline('download.png');
    }
}
