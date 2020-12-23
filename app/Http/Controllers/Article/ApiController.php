<?php
namespace App\Http\Controllers\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Article\Param;
use ZipArchive;

class APiController extends Controller
{
    /**
      * 获取地区列表
      */
      public function citys($pid){
        $citys = City::where('pid',$pid)->get();
        return response()->json($citys);
    }

    /**
     * 获取所有车子品牌型号列表
     */
    public function cars(){
        $brands = DB::table('car_infos')->where('pid',0)->get();
        foreach ($brands as $key=>$brand) {
            $return[$brand->id]['brand'] = $brand;
            $return[$brand->id]['models'] = DB::table('car_infos')->where('pid',$brand->id)->get();
        }
        return response()->json($return);
     }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function params(Request $request)
    {
        $output = Param::all();
        return response()->json($output);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paramids(Request $request)
    {
        $output = [];
        if($ids = $request->ids){
            if(count($ids)>0){
                $output = Param::whereIn('id',$ids)->get();
            }
        }
        return response()->json($output);
    }

    public function test()
    {
        // $oss = new \App\Handlers\OssUploadImageHandler();
        // $oss_files = $oss->listArrays('',[
        //     'max-keys'=>1000,
        //     'prefix'=>'uploads/paragraphs/11',
        //     'delimiter'=>'',
        //     'marker'=>'',
        // ]);
        $base_path = storage_path('app/public/articles/12');


            $fileList = glob($base_path.'/*.txt');
          $filename = $base_path.'/articles12.zip';
          $zip = new ZipArchive();
          $zip->open($filename,ZipArchive::CREATE);  //打开压缩包
          $options = array('remove_path' =>$base_path);
          $zip->addGlob($base_path.'/*.txt',GLOB_BRACE, $options);
        //   foreach($fileList as $file){
        //     $zip->addFile($file,transcoding(basename($file)));  //向压缩包中添加文件
        //   }
          $zip->close(); //关闭压缩包
        // $content = $oss->getObject($oss_files['list'][1]['uid'],'');
        // dd($content);
    }
}