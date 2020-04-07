<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use App\Models\WebsitePush;
use Illuminate\Support\Facades\Storage;

class SendWebsitePush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $website_push;
    protected $platform;
    protected $base_url;
    protected $query;
    protected $post_data;
    protected $last;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($website_push_id,$platform,$base_url,$query,$post_data,$last)
    {
        $this->website_push = WebsitePush::find($website_push_id);
        $this->platform = $platform;
        $this->base_url = $base_url;
        $this->query = $query;
        $this->post_data = $post_data;
        $this->last = $last;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $website_push = $this->website_push;
        //判断状态是否需要执行
        if($website_push&&$website_push->status != 1){
            //停止执行
            return false;
        }

        $body = [
            'form_params'=>$this->post_data
        ];
        
        $client = new Client([
            'base_uri' => $this->base_url,
        ]);

        $url = '?'.urldecode(http_build_query($this->query));
        try {
            $response = $client->request('POST', $url,$body);
            $brands = json_decode($response->getBody()->getContents(),true);

            //根据不同平台判断返回值,保存返回数据
            if($this->platform == 'baidu_zz'){
                //百度站长
                if(isset($brands['error'])||($brands['remain'] == 0)){
                    //记录错误日志
                    $brands['urls'] = $this->post_data['urls'];
                    if($website_push->error){
                        $website_push->error[] = $brands;
                        $error = $website_push->error;
                    }else{
                        $error[] = $brands;
                    }
                    $website_push->error = $error;
                    $website_push->save();
                }
            }elseif($this->platform == 'shenma'){
                //神马搜索
                if($brands['returnCode'] != 200){
                    //记录错误日志
                    $brands['urls'] = $this->post_data['urls'];
                    if($website_push->error){
                        $website_push->error[] = $brands;
                        $error = $website_push->error;
                    }else{
                        $error[] = $brands;
                    }
                    $website_push->error = $error;
                    $website_push->save();
                }
            }elseif($this->platform == 'baidu_xz_t'){
                //百度熊掌(天级)
                if(isset($brands['error'])||($brands['remain_realtime'] == 0)){
                    //记录错误日志
                    $brands['urls'] = $this->post_data['urls'];
                    if($website_push->error){
                        $website_push->error[] = $brands;
                        $error = $website_push->error;
                    }else{
                        $error[] = $brands;
                    }
                    $website_push->error = $error;
                    $website_push->save();
                }
            }elseif($this->platform == 'baidu_xz_z'){
                //百度熊掌（周级）
                if(isset($brands['error'])||($brands['remain_batch'] == 0)){
                    //记录错误日志
                    $brands['urls'] = $this->post_data['urls'];
                    if($website_push->error){
                        $website_push->error[] = $brands;
                        $error = $website_push->error;
                    }else{
                        $error[] = $brands;
                    }
                    $website_push->error = $error;
                    $website_push->save();
                }
            }else{
                //其他情况，不处理
            }

            if($this->last){
                $website_push->status  = 3;
                $website_push->save();
            }
        } catch (Exception $e) {
            if($this->last){
                $website_push->status  = 3;
            }

            if($website_push->error){
                $website_push->error[] = $brands;
                $error = $website_push->error;
            }else{
                $error[] = $e;
            }
            
            $website_push->error = $error;
            $website_push->save();
        }
    }
}
