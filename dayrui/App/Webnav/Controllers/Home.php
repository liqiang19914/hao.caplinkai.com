<?php
namespace Phpcmf\Controllers;

/**
 * 二次开发时可以修改本文件，不影响升级覆盖
 */

class Home extends \Phpcmf\Home\Module
{

	public function index() {
		$this->_Index();
	}
    public function img(){
//        /index.php?s=webnav&c=home&m=img
        @ini_set("memory_limit",'-1');
        ini_set('max_execution_time', '0');
//加上一次取2条
        $lists = \Phpcmf\Service::M()->table('fhx_1_webnav')->select('id,thumb')->where('thumb like "//%"')->limit(5)->getAll();
        if(empty($lists)){
            exit('没有数据了');
        }
        foreach ($lists as $v){
            // 下载远程文件
            $rt = $this->downImg('http:'.$v['thumb']);
            if($rt){
                $rt['attachment'] = \Phpcmf\Service::M('Attachment')->get_attach_info();
                $rt = \Phpcmf\Service::L('upload')->down_file($rt);
                if ($rt['code']) {
                    $att = \Phpcmf\Service::M('Attachment')->save_data($rt['data']);
                    if ($att['code']) {
                        // 归档成功
var_dump('更新id为'.$v['id'].'的数据');
                        \Phpcmf\Service::M()->table('fhx_1_webnav')->update($v['id'],['thumb'=>$att['code']]);
                    }

                }

            }else{
                exit('http:'.$v['thumb'].'下载失败');
            }

        }
//        刷新当前页面
        dr_redirect(\Phpcmf\Service::L('Router')->url('webnav/home/img'));
    }
    private function downImg($imageUrl){
//获取访问图片链接域名
        $host = parse_url($imageUrl, PHP_URL_HOST);
// 使用cURL获取图片数据
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: '.$host,
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'sec-ch-ua: "Google Chrome";v="119", "Chromium";v="119", "Not?A_Brand";v="24"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-User: ?1',
            'Sec-Fetch-Dest: document',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: zh-CN,zh;q=0.9',
            'Cookie: SL_G_WPT_TO=zh; SL_GWPT_Show_Hide_tmp=1; SL_wptGlobTipTmp=1'
        ));
        $imageData = curl_exec($ch);
// 检查图片数据是否获取成功
        if (!empty($imageData)) {
            // 获取图片的MIME类型
            $mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            switch ( $mimeType) {
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                default:
                    $extension = 'jpg';
                    break;
                // 添加其他图片格式...
            }
            curl_close($ch);
            return ['file_ext'=>$extension,'file_content'=> $imageData];

        }else{
            curl_close($ch);
            return false;
        }

    }
    public function icon(){
exit();
        $html = file_get_contents('data.txt');

        $pattern = '/<i\s+class="([^"]+) icon-fw icon-lg"><\/i>\s*<span>([^<]+)<\/span>/';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $iconClass = trim($match[1]);
            $text = trim($match[2]);
            if($iconClass && $text){
                var_dump($text,$iconClass);

                \Phpcmf\Service::M()->db->table('1_webnav_category')->where('name',$text)->update(['tubiao'=>$iconClass]);
            }


        }


    }

}
