<?php

// 自动加载识别文件

return [

    /**
     * 命名空间映射关系
     */
    'psr4' => [


    ],

    /**
     * 类名映射关系
     */
    'classmap' => [

        'Phpcmf\Admin\Mform'        => dr_get_app_dir('mform').'Control/Admin/Mform.php',
        'Phpcmf\Member\Mform'         => dr_get_app_dir('mform').'Control/Member/Mform.php',
        'Phpcmf\Home\Mform'         => dr_get_app_dir('mform').'Control/Home/Mform.php',

    ],


];