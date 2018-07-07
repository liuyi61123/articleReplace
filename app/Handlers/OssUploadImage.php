<?php

namespace App\Handlers;

use OSS;
use Log;
use OSS\Core\OssException;

class OssUploadImageHandler
{
   public function save($file, $folder, $bucket='',$file_prefix='')
   {
        // 构建存储的文件夹规则
        // 文件夹切割能让查找效率更高。
       $folder_name = "uploads/$folder/" . date("Ym", time()) . '/'.date("d", time()).'/';

       // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
       $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

       // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
       // 值如：1_1493521050_7BVc9v9ujP.png
       $filename = $folder_name.$file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

       //调用oss上传文件
       try{
           $upload = OSS::uploadFile($bucket?$bucket:env('OSS_BUCKET'), $filename, $file);
           $result = $upload['info']['url'];
       }catch(OssException $e){
           Log::error($e);
           $result = false;
       }
       return $result;
   }


   /**
    * 删除文件
    */
    public function delete($url,$bucket=''){
        //截取url有效部分
        $file = substr($url,strpos($url,'aliyuncs.com/')+13);

        //删除文件
        try{
            OSS::deleteObject($bucket?$bucket:env('OSS_BUCKET'),$file);
            $result = true;
        }catch(OssException $e){
            Log::error($e);
            $result = false;
        }
        return $result;
    }
}