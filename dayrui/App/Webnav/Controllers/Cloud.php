<?php

namespace Phpcmf\Controllers;

//技术支持 w w w . 8 0 z x . c o m
class Cloud extends \Phpcmf\Home\Module
{
    public function update(){
        if (!function_exists('fsockopen')) {
            $this->error('本站：PHP环境不支持fsockopen，升级失败');
        }
        // 执行下载文件
        $version=config('version');
        $tempPath=app()->getRuntimePath().'temp';

        if(!is_dir($tempPath)){
            mkdir($tempPath,0777,true);
        }

        $file = $tempPath.'/'.md5($version['appversion']).'.zip';

        set_time_limit(0);
        touch($file);
        $domain=$_SERVER['HTTP_HOST'];
        $license=@file_get_contents(app()->getRootPath().'config/license.key');
        // 做些日志处理
        if ($fp = fopen("http://cloud.laikephp.com/index/index/update_file?domain={$domain}&license={$license}&name={$version['name']}&php=".PHP_VERSION."&id={$version['id']}", "rb")) {
            if (!$download_fp = fopen($file, "wb")) {
                $this->error('本站：无法写入远程文件');
            }
            while (!feof($fp)) {
                if (!is_file($file)) {
                    // 如果临时文件被删除就取消下载
                    fclose($download_fp);
                    $this->error('本站：临时文件被删除');
                }
                fwrite($download_fp, fread($fp, 1024 * 8 ), 1024 * 8);
            }
            fclose($download_fp);
            fclose($fp);
//下载完成执行解压
            if (!class_exists('ZipArchive')) {
                $this->error('PHP环境解压类ZipArchive没有开启');
            }

            $zip = new \ZipArchive;//新建一个ZipArchive的对象
            /*
            通过ZipArchive的对象处理zip文件
            $zip->open这个方法的参数表示处理的zip文件名。
            如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
            */
            if ($zip->open($file) === TRUE) {
                $zip->extractTo(app()->getRootPath());//解压缩到某路径下
                $zip->close();//关闭处理的zip文件
                unlink($file);
               $this->success('升级成功');
            }else{
                unlink($file);
                $this->error('打开升级包失败');
            }

        } else {
            unlink($file);
            $this->error('本站：fopen打开远程文件失败');
        }

    }
}
