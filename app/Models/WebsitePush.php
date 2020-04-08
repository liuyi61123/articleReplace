<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendWebsitePush;

class WebsitePush extends Model
{
    public static $platformMap = [
        'baidu_zz'  => [
            'name'=>'百度站长',
            'base_url'=>'http://data.zz.baidu.com/urls'
        ],
        'shenma'  => [
            'name'=>'神马',
            'base_url'=>'http://data.zhanzhang.sm.cn/push'
        ],
        'baidu_xz_t'  => [
            'name'=>'百度熊掌(天级)',
            'base_url'=>'http://data.zz.baidu.com/urls'
        ],
        'baidu_xz_z'  => [
            'name'=>'百度熊掌(周级)',
            'base_url'=>'http://data.zz.baidu.com/urls'
        ],
    ];

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
        'is_automatic' => 'boolean',
        'config' => 'array',
        'error' => 'array',
    ];

    /**
     * 手动提交
     */
    public static function manual($data)
    {
        $website = Website::findOrFail($data['website_id']);
        $websiteConfig = $website->config;
        $platform = $data['platform'];
        $platformMap = self::$platformMap;

        //获取对应平台配置信息
        if(($platform == 'baidu_xz_t')||($platform == 'baidu_xz_z')){
            $config = $websiteConfig['baidu_xz'];
        }else{
            $config = $websiteConfig[$platform];
        }
        
        if(in_array('',array_unique(array_values($config)))){
            return ['msg'=>'未配置平台信息'];
        }
        if($platform == 'baidu_xz_t'){
            $config['type'] = 'realtime';
        }elseif($platform == 'baidu_xz_z'){
            $config['type'] = 'batch';
        }
        if(isset($config['site'])){
            $config['site'] = urldecode($config['site']);
        }

        $urls = implode("\n",$data['urls']);
        $result = self::sendUrls($platformMap[$platform]['base_url'],$config,$urls);
        return $result;
    }

    /**
     * 自动提交
     */
    public function automatic()
    {
        $status = $this->status;
        if($status == 1){
            //正在执行中，不可重复执行
            return false;
        }

        $this->status = 1;
        $this->error = '';
        $this->save();

        $platformMap = self::$platformMap;

        $config = $this->config;
        $count_config = count($config);
        foreach($config as $key=>$item){
            $website = Website::findOrFail($item['website_id']);
            $websiteConfig = $website->config;

            //读取urls
            $exists = Storage::disk('public')->exists('websites/'.$item['website_id'].'.txt'); 
            if($exists){
                $urls = Storage::disk('public')->get('websites/'.$item['website_id'].'.txt'); 
                //按照number分割数组
                $urls = array_unique(array_filter(explode(PHP_EOL,$urls)));
                $urls = array_number_data($urls,$item['number']);
    
    
                $countu_urls = count($urls);
                foreach($urls as $k=>$url){
                    $post_data = ['urls'=>$url];
    
                    $count_platforms = count($item['platforms']);
                    foreach($item['platforms'] as $ke=>$platform){
                        //获取对应平台配置信息
                        if(str_is('baidu_xz*', $platform)){
                            $query = $websiteConfig['baidu_xz'];
                        }else{
                            $query = $websiteConfig[$platform];
                        }
        
                        if(in_array('',array_unique(array_values($query)))){
                            return ['msg'=>'未配置平台信息'];
                        }
                        if($platform == 'baidu_xz_t'){
                            $query['type'] = 'realtime';
                        }elseif($platform == 'baidu_xz_z'){
                            $query['type'] = 'batch';
                        }
                        if(isset($query['site'])){
                            $query['site'] = urldecode($query['site']);
                        }
        
                        $base_url = $platformMap[$platform]['base_url'];
        
                        if((($key+1) == $count_config)&&(($ke+1) == $count_platforms)&&(($k+1) == $countu_urls)){
                            //最后一次循环
                            $last = true;
                        }else{
                            $last = false;
                        }
        
                        $info = [
                            $this->id,$platform,$base_url,$query,$post_data,$last
                        ];
                        
                        // \Log::info($info);
                        SendWebsitePush::dispatch($this->id,$platform,$base_url,$query,$post_data,$last)->delay(now()->addSeconds($this->delay));
                        
                    }
                }
            }
        }
    }

    /**
     * 提交链接
     */
    public static function sendUrls($base_url,$query,$urls)
    {
        $body = [
            'form_params'=>[
                'urls'=>$urls,
            ]
        ];
        
        $client = new Client([
            'base_uri' => $base_url,
        ]);

        $url = '?'.urldecode(http_build_query($query));
        try {
            $response = $client->request('POST', $url,$body);
            $brands = json_decode($response->getBody()->getContents(),true);
            return $brands;
        } catch (Exception $e) {
            return $e;
        }
        
    }
}
