<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 数组按照一定个数，分割成二维数组
 */
function array_number_data($arr=[],$number=1)
{
    if(empty($arr)){
        return [];
    }
    $result = [];
    $i = 0;
    foreach($arr as $item){
        if(isset($result[$i])&&(count($result[$i]) >= $number)){
            //不处理了
            $i++;
        }
        $result[$i][] = trimall($item);
    }

    return $result;
}

//删除空格和回车
function trimall($str){
    $qian=array(" ","　","\t","\n","\r");
    return str_replace($qian, '', $str);   
}
