<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PseudoOriginal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $start_path;
    protected $over_path;
    protected $th;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($start_path,$over_path,$th)
    {
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
            $response = $this->sendOriginal($content,$this->th);
            $title = $this->sendOriginal($title,$this->th);
            if($response){
                //保存新生成的文件
                Storage::put($over_directory.$title.'.txt',$response);
                sleep(2);
            }
        }

    }

    protected function sendOriginal($content,$th)
    {
        $body = [
            'form_params'=>[
                'txt'=>$content,
                'th'=>$th
            ]
        ];
        $client = new Client([
            'base_uri' => 'http://apis.5118.com/',
            'headers' => [
                'Content-Type'=>'application/x-www-form-urlencoded',
                'Authorization' => 'APIKEY 96BD53AD97644476891D41753BAFCFC5',
            ]
        ]);

        $brand_api = 'wyc/akey';
        $response = $client->request('POST', $brand_api,$body);
        $brands = json_decode($response->getBody()->getContents(),true);
        if($brands['errcode'] == 0){
            return $brands['data'];
        }else{
            return false;
        }
    }
}
