<?php

/**
 * URL解析规则
 * 例如：  114.html 对应 index.php?s=demo&c=show&id=114
 * 可以解析：  "114.html"  => 'index.php?s=demo&c=show&id=114',
 * 动态id解析：  "([0-9]+).html"  => 'index.php?s=demo&c=show&id=$1',
 */

return [
    "([a-z]+)\/tag\/(.+)\-([0-9]+)\.html" => "index.php?&s=tag&dir=$1&name=$2&page=$3",  // tag插件分页
    "([a-z]+)\/tag\/(.+)\.html" => "index.php?s=tag&dir=$1&name=$2",  // tag插件
    "tag\/(.+)\-([0-9]+)\.html" => "index.php?s=tag&name=$1&page=$2",  // tag插件分页
    "tag\/(.+)\.html" => "index.php?s=tag&name=$1",  // tag插件
    "tag" => "index.php?s=tag", // tag插件聚合首页
    "tag\/p([0-9]+)\.html" => "index.php?s=tag&page=$1", // tag插件聚合首页分页
    'sitemap\.txt' => 'index.php?s=sitemap&page=999', // 地图规则
    'sitemap([0-9]+)\.txt' => 'index.php?s=sitemap&page=999&p=$1', // 地图规则分页
    'sitemap\-([a-z]+)\-([0-9]+)\.txt' => 'index.php?s=sitemap&page=999&mid=$1&catid=$2', // 栏目地图规则
    'sitemap\-([a-z]+)\-([0-9]+)\-([0-9]+)\.txt' => 'index.php?s=sitemap&page=999&mid=$1&catid=$2&p=$3', // 栏目地图规则分页
    'sitemap\.xml' => 'index.php?s=sitemap&c=home&m=xml&page=998', // 地图规则
    'sitemap([0-9]+)\.xml' => 'index.php?s=sitemap&c=home&m=xml&page=998&p=$1', // 地图规则分页
    'sitemap\-([a-z]+)\-([0-9]+)\.xml' => 'index.php?s=sitemap&c=home&m=xml&page=999&mid=$1&catid=$2', // 栏目地图规则
    'sitemap\-([a-z]+)\-([0-9]+)\-([0-9]+)\.xml' => 'index.php?s=sitemap&c=home&m=xml&page=999&mid=$1&catid=$2&p=$3', // 栏目地图规则分页




    "list-([A-za-z0-9 \-\_]+)-([0-9]+)\.html" => "index.php?c=category&dir=$1&page=$2",  //【不带栏目路径】模块栏目列表(分页)（list-{dirname}-{page}.html）
    "list-([A-za-z0-9 \-\_]+)\.html" => "index.php?c=category&dir=$1",  //【不带栏目路径】模块栏目列表（list-{dirname}.html）
    "show-([0-9]+)\.html" => "index.php?c=show&id=$1",  //【不带栏目路径】模块内容页（show-{id}.html）

    "search\/([a-z]+)\/(.+)\.html" => "index.php?s=$1&c=search&rewrite=$2",  //【共享模块搜索】模块搜索页(分页)（search/{modname}/{param}.html）
    "search\/([a-z]+)\.html" => "index.php?s=$1&c=search",  //【共享模块搜索】模块搜索页（search/{modname}.html）


    "([a-z]+)\.html" => "index.php?s=$1",  //【独立模块】模块首页（{modname}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/([A-za-z0-9 \-\_]+)\/([0-9]+)\/p([0-9]+)\.html" => "index.php?s=$1&c=category&dir=$2&page=$4",  //【独立模块】模块栏目列表(分页)（{modname}/{dirname}/{id}/p{page}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/([A-za-z0-9 \-\_]+)\/([0-9]+)\.html" => "index.php?s=$1&c=category&dir=$2",  //【独立模块】模块栏目列表（{modname}/{dirname}/{id}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/([A-za-z0-9 \-\_]+)\/show\/([0-9]+)\/p([0-9]+)\.html" => "index.php?s=$1&c=show&id=$3&page=$4",  //【独立模块】模块内容页(分页)（{modname}/{dirname}/show/{id}/p{page}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/([A-za-z0-9 \-\_]+)\/show\/([0-9]+)\.html" => "index.php?s=$1&c=show&id=$3",  //【独立模块】模块内容页（{modname}/{dirname}/show/{id}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/search\/(.+)\.html" => "index.php?s=$1&c=search&rewrite=$2",  //【独立模块】模块搜索页(分页)（{modname}/search/{param}.html）（此规则由系统生成，不一定会准确，请开发者自行调整）
    "([a-z]+)\/search\.html" => "index.php?s=$1&c=search",  //【独立模块】模块搜索页（{modname}/search.html）（此规则由系统生成，不一定会准确，请开发者自行调整）

    "([A-za-z0-9 \-\_]+)\/p([0-9]+)\.html" => "index.php?c=category&dir=$1&page=$2",  //【带栏目路径】模块栏目列表(分页)（{dirname}/p{page}.html）
    "([A-za-z0-9 \-\_]+)\/([0-9]+)\.html" => "index.php?c=show&id=$2",  //【带栏目路径】模块内容页（{dirname}/{id}.html）
    "([A-za-z0-9 \-\_]+)" => "index.php?c=category&dir=$1",  //【带栏目路径】模块栏目列表（{dirname}）

];
