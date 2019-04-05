<?php

namespace App\Jobs;

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
            $file_name = explode($start_directory,$file);
            $response = $this->sendOriginal($content,$this->th);
            if($response){
                //保存新生成的文件
                Storage::put($over_directory.$file_name,$response);
                sleep(1);
            }
        }

    }

    protected function sendOriginal($content,$th)
    {
        $body = [
            'form_params'=>[
                'txt'=>'本公司常年从事软件开发工作，经验丰富',
                'th'=>3
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
        if($brands['code'] == 0){
            return $brands['data'];
        }else{
            return false;
        }
    }
}
