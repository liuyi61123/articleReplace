<?php
namespace App\Http\Controllers\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Article\Param;

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
        $oss = new \App\Handlers\OssUploadImageHandler();
        $oss_files = $oss->listArrays('',[
            'max-keys'=>1000,
            'prefix'=>'uploads/paragraphs/11',
            'delimiter'=>'',
            'marker'=>'',
        ]);
        dd($oss_files);

        $content = $oss->getObject($oss_files['list'][1]['uid'],'');
        // return $content;
        dd($content);
    }
}