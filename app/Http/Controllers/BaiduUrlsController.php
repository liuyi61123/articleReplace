<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\BaiduUrl;
use Illuminate\Support\Facades\Storage;

class BaiduUrlsController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            //渲染列表
            $output = BaiduUrl::paginate(20);
            return response()->json($output);
        }else{
            return view('baidu_urls.index');
        }
    }

    public function store(Request $request)
    {
        $urls = $request->input('urls');
        $urls = array_unique(explode("\n",$urls));
        if(count($urls)>50){
            return response()->json(['status'=>400,'msg'=>'最多50条']);
        }
        $urls = implode('|',$urls);

        $body = [
            'form_params'=>[
                'key'=>config('baidu_url.key'),
                'urls'=>$urls
            ]
        ];
        $client = new Client([
            'base_uri' => config('baidu_url.base_url'),
        ]);

        $brand_api = config('baidu_url.request_url');
        try {
            $response = $client->request('POST', $brand_api,$body);
            $brands = json_decode($response->getBody()->getContents(),true);
            if($brands['StateCode'] == 1){
                BaiduUrl::create([
                    'urls'=>$urls,
                    'task_id'=>$brands['TaskID']
                ]);
                return response()->json(['status'=>200,'msg'=>'请求成功']);
            }else{
                return response()->json(['status'=>400,'msg'=>$brands['Reason'].';'.config('baidu_url.key')]);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['status'=>400,'msg'=>'接口异常']);
        }

    }

    public function create()
    {
        return view('baidu_urls.create');
    }

    public function search($id)
    {
        $file_name = 'public/baidu_url/'.$id.'.txt';
        $baidu_url = BaiduUrl::findOrFail($id);
        $task_id = $baidu_url->task_id;
        Log::info('$task_id:'.$task_id);

        $body = [
            'form_params'=>[
                'taskid'=>$task_id
            ]
        ];
        $client = new Client([
            'base_uri' => config('baidu_url.base_url'),
        ]);

        $brand_api = config('baidu_url.search_url');
        try {
            $response = $client->request('POST', $brand_api,$body);
            $brands = json_decode($response->getBody()->getContents(),true);
            if($brands['StateCode'] == 1){

                $data = $brands['Result']['Data'];
                $str = '';
                foreach($data as $datum){
                    if($datum['Result']['IsRecord']&&($datum['StateCode'] == 1)){
                        //查询成功
                        $str .= '成功#'.$datum['Url'].'#'.$datum['Result']['RecordTitle'].'#'.$datum['Result']['RecordDepict'].'#'.$datum['Result']['SnapTime'].PHP_EOL;
                    }else{
                        $str .= '失败#'.$datum['Url'].'#'.'#'.'#'.PHP_EOL;
                    }
                }
                //存储文本
                Storage::put($file_name,$str);

                $baidu_url->status = 1;
                $baidu_url->save();

                return response()->json(['status'=>200,'msg'=>'成功']);
            }else{
                return response()->json(['status'=>400,'msg'=>$brands['Reason']]);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['status'=>400,'msg'=>'接口异常']);
        }
    }

    public function destroy($id)
    {
        //删除数据库
        BaiduUrl::destroy($id);

        //删除对应的文件
        $file_name = 'public/baidu_url/'.$id.'.txt';
        Storage::delete($file_name);

        return response()->json(['status'=>200]);
    }
}
