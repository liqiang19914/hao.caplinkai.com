<?php

/**
 * 开发者自定义函数文件
 */

function diy_tag_url($url,$mid){
//    加上模型
    if(strpos($url,'/')===0){
        $url = '/'.$mid.$url;
    }else{
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'];
        $url = str_replace($path, '/'.$mid. $path, $url);
    }
    return $url;
}

function formatReadCount($count) {
    if ($count >= 1000000000) {
        return number_format($count / 1000000000, 1, '.', '') . 'B';
    } else if ($count >= 1000000) {
        return number_format($count / 1000000, 1, '.', '') . 'M';
    } else if ($count >= 1000) {
        return number_format($count / 1000, 1, '.', '') . 'K';
    } else {
        return $count;
    }
}
