<?php
/**
 * 公告代码，插件函数
 */

// 回调函数-显示广告分类
function dr_jms_typeid2name($value, $param = [], $data = []) {
    $type = \Phpcmf\Service::M('diygg', 'diygg')->get_typename();
    return $type[$value];
}

// 回调函数-显示广告类型
function dr_jms_adstyle2name($value, $param = [], $data = []) {
    $adstyle = \Phpcmf\Service::M('diygg', 'diygg')->adstyle;
    return $adstyle[$value];
}

// 回调函数-显示时间限制
function dr_jms_timeset2name($value, $param = [], $data = []) {
    $timeset = \Phpcmf\Service::M('diygg', 'diygg')->timeset;
    return $timeset[$value];
}
