<?php
namespace App\Http\Controllers\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Article\Param;
use App\Models\Article\Paragraph;
use ZipArchive;
use App\Handlers\OssUploadImageHandler;

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
        $output = Param::get(['id','title','identifier']);
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

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paragraphs(Request $request)
    {
        $output = Paragraph::get(['id','title','identifier']);
        return response()->json($output);
    }

    public function test()
    {
        $oss = new OssUploadImageHandler();
        $list = $oss->allList('',[
            // 'max-keys'=>2,
            'prefix'=>'uploads/templates/202011',
            // 'delimiter'=>'',
            // 'marker'=>'',
        ]);
        dd($list);
    }
}