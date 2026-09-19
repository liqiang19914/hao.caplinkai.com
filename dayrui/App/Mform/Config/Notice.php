<?php

/**
 * http://www.xunruicms.com
 * 本文件是框架系统文件，二次开发时不可以修改本文件
 **/

/**
 *  通知动作注册配置
 *
 *  动作字符 => 动作名称
 *
 **/

$cfg = [


    'module_form_verify_1'      => '[所有]审核后通知表单作者',
    'module_form_verify_0'      => '[所有]被拒绝后通知表单作者',

    'module_form_verify_2'      => '[所有]审核后通知主体作者',
    'module_form_post_2'      => '[所有]前台提交（直接通过时）通知主体作者',
    'module_form_post_1'      => '[所有]前台提交（直接通过时）通知表单作者',


];

return $cfg;