<?php namespace Phpcmf\Model\diygg; // diygg表示插件目录

class diygg extends \Phpcmf\Model
{
    // 默认广告类型
    public $adstyle = [
        1 => '图片',
        2 => '幻灯图片',
        3 => '代码',
        4 => '文字'
    ];
    // 默认时间限制
    public $timeset = [
        0 => '不限时间',
        1 => '设置时间内有效'
    ];


    // 读取文件
    public function jms_getfile($id) {
        if (is_file(WRITEPATH.'config/diygg-'.$id.'.html')) {
            $file = require WRITEPATH.'config/diygg-'.$id.'.html';
            return $file;
        }
        return '';
    }

    // 设置文件
    public function jms_setfile($data, $id) {
        $file = WRITEPATH.'config/diygg-'.$id.'.html';
        $fp = fopen($file, 'w');
        fwrite($fp, $data);
        fclose($fp);
    }

    // 广告分类数组
    public function get_typename() {
        $type = $this->db->table('diygg_type')->get()->getResultArray();
        $rt = [
            0 => '默认分类',
        ];
        if ($type) {
            foreach($type as $v) {
                $rt[$v['id']] = $v['typename'];
            }
        }
        return $rt;
    }

    // 组装option数据
    public function option_data($data) {
        $rt = '';
        if ($data && is_array($data)) {
            foreach ($data as $i => $t) {
                $rt .= $t.'|'.$i.PHP_EOL;
            }
        }
        return trim($rt);
    }

    // 获取广告位标识tagname
    public function get_tagname() {
        $rt = [];
        $tagname = $this->db->table('diygg')->select('tagname')->get()->getResultArray();
        foreach ($tagname as $t) {
            $rt[] = $t['tagname'];
        }
        return $rt;
    }

    // 生成广告
    public function generate_ad($data, $ts = 1) {

        if ($data['timeset'] == 1 && $ts) {
            $ntime = time();
            if($ntime > $data['endtime'] || $ntime < $data['starttime']) {
                $code = "<img src='".dr_get_file($data['overpic'])."'>";
            } else {
                $code = $this->output_adcode($data, $ts);
            }
        } else {
            $code = $this->output_adcode($data, $ts);
        }
        return $code;
    }

    // 输出广告代码
    private function output_adcode($data, $ts) {

        $adbody = ($data['adstyle'] == 3) ? $data['adbody'] : dr_string2array($data['adbody']);

        if ($data['adstyle'] == 1) {
            // 图片
            $code = "<a href='".$adbody[1]['link']."' target='_blank'><img src='".dr_get_file($adbody[1]['pic'])."' style='width:".$adbody[1]['width']."px;height:".$adbody[1]['height']."px;' alt='".$adbody[1]['descrip']."'></a>";
        }

        if ($data['adstyle'] == 2) {
            // 幻灯图片
            $wh = reset($adbody);
            $code = "<link rel='stylesheet' href='/static/default/diygg/css/swiper-bundle.min.css'><script type='text/javascript' src='/static/default/diygg/js/swiper-bundle.min.js'></script>";
            $code .= "<div class='swiper diygg-hdtp".$data['id']."' style='width:".$wh['width']."px;height:".$wh['height']."px;box-sizing:border-box;overflow:hidden;'>";
            $code .= "<div class='swiper-wrapper'>";
            foreach($adbody as $t) {
                $code .= "<div class='swiper-slide'><a href='".$t['link']."' target='_blank'><img src='".dr_get_file($t['pic'])."' style='width:".$wh['width']."px;height:".$wh['height']."px;' alt='".$t['descrip']."'></a></div>";
            }
            $code .= "</div><div class='swiper-pagination'></div></div>";
            $code .= "<script>var hdtpSwiper = new Swiper('.diygg-hdtp".$data['id']."', {autoplay: true, loop: true,});</script>";
        }

        if ($data['adstyle'] == 3) {
            // 代码
//            $adbody = str_replace('"', '\"',$adbody);
            $adbody = htmlspecialchars_decode($adbody);
            if ($ts) {
                $adbody = addslashes($adbody);
                $adbody = str_replace("\r", "\\r",$adbody);
                $adbody = str_replace("\n", "\\n",$adbody);
            }
            $code = $adbody;
        }

        if ($data['adstyle'] == 4) {
            // 文字
            $code = "<a href='".$adbody[1]['link']."' style='font-size:".$adbody[1]['size'].";color:".$adbody[1]['color'].";'>".$adbody[1]['title']."</a>";
        }

        return $code;
    }


}
