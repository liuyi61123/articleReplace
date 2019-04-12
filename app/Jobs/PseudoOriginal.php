<?php

namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PseudoOriginal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $channel;
    protected $start_path;
    protected $over_path;
    protected $th;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($channel,$start_path,$over_path,$th)
    {
        $this->channel = $channel;
        $this->start_path = $start_path;
        $this->over_path = $over_path;
        $this->th = $th;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $start_directory = 'public/original/start/'.$this->start_path.'/';
        $over_directory = 'public/original/over/'.$this->over_path.'/';

        $files = Storage::files($start_directory);
        foreach($files as $file){
            $content = Storage::get($file);
            $file_name = explode($start_directory,$file)[1];
            $title = explode('.',$file_name)[0];
            $suffix = explode('.',$file_name)[1];

            $response_content = $this->sendOriginal($this->channel,$content,$this->th);
            $response_title = $this->sendOriginal($this->channel,$title,$this->th);

            if($response_content&&$response_title){
                //保存新生成的文件
                Storage::put($over_directory.$response_title.'.'.$suffix,$response_content);
                sleep(1);
            }
        }
    }

    protected function sendOriginal($channel,$text,$th)
    {   
        if($channel == 'naipan'){
            return $this->sendOriginalNaipan($text,$th);
        } elseif($channel == '5118'){
            return $this->sendOriginal5188($text,$th);
        } else{
            return false;
        }
    }

    protected function sendOriginal5188($text,$th)
    {
        $body = [
            'form_params'=>[
                'txt'=>$text,
                'th'=>$th
            ]
        ];
        $client = new Client([
            'base_uri' => config('pseudo_original.5118.base_url'),
            'headers' => [
                'Content-Type'=>'application/x-www-form-urlencoded',
                'Authorization' => 'APIKEY '.config('pseudo_original.5118.key'),
            ]
        ]);

        $brand_api = config('pseudo_original.5118.api');
        try {
            $response = $client->request('POST', $brand_api,$body);
            $brands = json_decode($response->getBody()->getContents(),true);
            if($brands['errcode'] == 0){
                return $brands['data'];
            }else{
                Log::warning($brands['errcode']);
                return $text;
            }
        } catch (Exception $e) {
            report($e);
    
            return false;
        }
    }

    protected function sendOriginalNaipan($text,$th)
    {
        $body = [
            'form_params'=>[
                'regname'=>config('pseudo_original.naipan.regname'),
                'regsn'=>config('pseudo_original.naipan.regsn'),
                'content'=>$text
            ]
        ];
        $client = new Client([
            'base_uri' => config('pseudo_original.naipan.base_url'),
            'headers' => [
                'Content-Type'=>'application/x-www-form-urlencoded',
            ]
        ]);

        $brand_api = config('pseudo_original.naipan.api');
        try {
            $response = $client->request('POST', $brand_api,$body);
            $brands = json_decode($response->getBody()->getContents(),true);
            if($brands['result'] == 1){
                return $brands['content'];
            }else{
                Log::warning($brands['message']);
                return $text;
            }
        } catch (Exception $e) {
            report($e);
    
            return false;
        }
    }
}
