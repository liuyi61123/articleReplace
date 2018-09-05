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
           $upload = OSS::uploadFile($bucket?$bucket:config('oss.bucket'), $filename, $file);
           $result = $upload['info']['url'];
       }catch(OssException $e){
           Log::error($e);
           $result = false;
       }
       return $result;
   }

   public function articleSave($file, $bucket='')
   {
        // 构建存储的文件夹规则
        // 文件夹切割能让查找效率更高。
       $folder_name = "uploads/article/" . date("Ym", time()) . '/'.date("d", time()).'/';

       // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
       // 值如：1_1493521050_7BVc9v9ujP.png
       $filename = $folder_name . time() . '_' . str_random(10) . '.png';

       //调用oss上传文件
       try{
           $upload = OSS::uploadFile($bucket?$bucket:config('oss.bucket'), $filename, $file);
           $result = $upload['info']['url'];
           //是否替换https
          if(config('oss.prefix_https')){
             $result =  str_replace('http://','https://',$result);
          }
           //删除原文件
           unlink($file);
       }catch(OssException $e){
           Log::error($e);
           $result = false;
       }
       return $result;
   }

   /**
    * 获取oss文件列表
    */
   public function listObjects($bucket='',$option=[]){
       try{
           $result = OSS::listObjects($bucket?$bucket:config('oss.bucket'),$option);
       }catch(OssException $e){
           Log::error($e);
           $result = false;
       }

       return $result->getObjectList();
   }

   /**
    * 获取oss文件列表(数组)
    */
   public function listArrays($bucket='',$option=[]){
       $lists = $this->listObjects($bucket,$option);

       $arrays = array();
       foreach($lists as $key=>$list){
           $prefix_https = config('oss.prefix_https')?'https://':'http://';
           $arrays[] = array(
               'uid'=>$list->getKey(),
               'url'=>$prefix_https.config('oss.bucket_prefix').$list->getKey()
           );
       }
       $last = end($lists)->getKey();
       return ['list'=>$arrays,'last'=>$last];
   }

   /**
    * 删除文件
    */
    public function delete($url,$bucket=''){
        //截取url有效部分
        $file = substr($url,strpos($url,'aliyuncs.com/')+13);

        //删除文件
        try{
            OSS::deleteObject($bucket?$bucket:config('oss.bucket'),$file);
            $result = true;
        }catch(OssException $e){
            Log::error($file);
            Log::error($e);
            $result = false;
        }
        return $result;
    }
}
