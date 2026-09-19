<?php

/**
 * 菜单配置
 */


return [

    'admin' => [

        'app' => [

            'left' => [


                'app-plugin' => [
                    'link' => [
                        [
                            'name' => '模块内容点赞',
                            'icon' => 'fa fa-thumbs-o-up',
                            'uri' => 'zan/home/index',
                        ],

                    ]
                ],


            ],


        ],

    ],

    'member' => [


        'content-module' => [

            'link' => [
                [
                    'name' => '我的支持',
                    'icon' => 'fa fa-thumbs-o-up',
                    'uri' => 'zan/home/support',
                ],
                [
                    'name' => '我的反对',
                    'icon' => 'fa fa-thumbs-o-down',
                    'uri' => 'zan/home/oppose',
                ],
            ],
        ],

    ]
];