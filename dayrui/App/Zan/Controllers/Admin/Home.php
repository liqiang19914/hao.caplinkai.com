<?php namespace Phpcmf\Controllers\Admin;

class Home extends \Phpcmf\Common
{

    public function index() {

        $module = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-content');
        if (!$module) {
            $this->_admin_msg(0, dr_lang('未安装任何内容模块'));
        }

        $data = [];
        foreach ($module as $t) {
            $m = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-'.$t['dirname']);
            $is_field = 0;
            // 是否有字段
            $table = \Phpcmf\Service::M()->dbprefix(SITE_ID.'_'.$t['dirname']);
            if (\Phpcmf\Service::M()->is_field_exists($table, 'support')
                && \Phpcmf\Service::M()->db->tableExists($table.'_support')
                && \Phpcmf\Service::M()->db->tableExists($table.'_oppose')
                && \Phpcmf\Service::M()->is_field_exists($table, 'oppose')) {
                $is_field = 1;
            }
            $data[] = [
                'name' => $t['name'],
                'icon' => $t['icon'],
                'dirname' => $t['dirname'],
                'is_field' => $is_field,
            ];
        }
        if (!$data) {
            $this->_admin_msg(0, dr_lang('未安装任何内容模块'));
        }

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '内容模块权限' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-gears'],
                    'help' => [863],
                ]
            ),
            'data' => $data,
        ]);
        \Phpcmf\Service::V()->display('module_qx.html');
    }

    // 创建模块字段
    public function add() {

        $mid = dr_safe_filename($_GET['dir']);
        $row = \Phpcmf\Service::M('module')->table('module')->where('dirname', $mid)->getRow();
        if (!$row) {
            $this->_json(0, dr_lang('此模块[%s]未安装', $mid));
        }

        $module = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-'.$mid);
        if (!$module) {
            $this->_json(0, dr_lang('此模块[%s]没有安装在当前站点', $mid));
        }

        $table = \Phpcmf\Service::M()->prefix.SITE_ID.'_'.$mid;

        // 复制文件
        /*
        $path = dr_get_app_dir($mid);
        $files = [
            $path.'Controllers/'.$name.'.php' => APPPATH.'Temp/.php',
            $path.'Controllers/Admin/'.$name.'.php' => APPPATH.'Temp/Mform/Admin$NAME$.php',
            $path.'Controllers/Admin/'.$name.'_verify.php' => FCPATH.'Temp/Mform/Admin$NAME$_verify.php',
        ];
        */

        // 创建字段
        if (!\Phpcmf\Service::M()->db->fieldExists('support', $table)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$table.'` ADD `support` int(10) unsigned DEFAULT \'0\' COMMENT \'支持数\';');
        }
        if (!\Phpcmf\Service::M()->db->fieldExists('oppose', $table)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$table.'` ADD `oppose` int(10) unsigned DEFAULT \'0\' COMMENT \'反对数\';');
        }

        // 创建表
        // 判断是否存在表
        $ctable = $table.'_support';
        if (!\Phpcmf\Service::M()->db->tableExists($ctable)) {
            \Phpcmf\Service::M()->db->simpleQuery(trim("CREATE TABLE IF NOT EXISTS `".$ctable."` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `cid` int(10) unsigned NOT NULL COMMENT '文档id',
      `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
      `agent` varchar(100) NOT NULL COMMENT '匿名',
      `inputtime` int(10) unsigned NOT NULL COMMENT '操作时间',
      PRIMARY KEY (`id`),
      KEY `cid` (`cid`),
      KEY `agent` (`agent`),
      KEY `inputtime` (`inputtime`),
      KEY `uid` (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支持操作记录表';"));
        }
        if (!\Phpcmf\Service::M()->db->fieldExists('inputtime', $ctable)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$ctable.'` ADD `inputtime` int(10) unsigned DEFAULT \'0\' COMMENT \'操作时间\';');
        }
        $ctable = $table.'_oppose';
        if (!\Phpcmf\Service::M()->db->tableExists($ctable)) {
            \Phpcmf\Service::M()->db->simpleQuery(trim("CREATE TABLE IF NOT EXISTS `".$ctable."` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `cid` int(10) unsigned NOT NULL COMMENT '文档id',
      `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
      `agent` varchar(100) NOT NULL COMMENT '匿名',
      `inputtime` int(10) unsigned NOT NULL COMMENT '操作时间',
      PRIMARY KEY (`id`),
      KEY `cid` (`cid`),
      KEY `agent` (`agent`),
      KEY `inputtime` (`inputtime`),
      KEY `uid` (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='反对操作记录表';"));
        }
        if (!\Phpcmf\Service::M()->db->fieldExists('inputtime', $ctable)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$ctable.'` ADD `inputtime` int(10) unsigned DEFAULT \'0\' COMMENT \'操作时间\';');
        }

        \Phpcmf\Service::M('cache')->sync_cache('');
        $this->_json(1, '安装成功');
    }

    // 删除模块字段
    public function del() {

        $mid = dr_safe_filename($_GET['dir']);
        $row = \Phpcmf\Service::M('Module')->table('module')->where('dirname', $mid)->getRow();
        if (!$row) {
            $this->_json(0, dr_lang('此模块[%s]未安装', $mid));
        }

        $module = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-'.$mid);
        if (!$module) {
            $this->_json(0, dr_lang('此模块[%s]没有安装在当前站点', $mid));
        }

        $table = \Phpcmf\Service::M()->prefix.SITE_ID.'_'.$mid;

        if (\Phpcmf\Service::M()->db->fieldExists('support', $table)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$table.'` DROP `support`;');
        }
        if (\Phpcmf\Service::M()->db->fieldExists('oppose', $table)) {
            \Phpcmf\Service::M()->query('ALTER TABLE `'.$table.'` DROP `oppose`;');
        }

        // 判断是否存在表
        $ctable = $table.'_support';
        if (\Phpcmf\Service::M()->db->tableExists($ctable)) {
            \Phpcmf\Service::M()->db->simpleQuery(trim("DROP TABLE IF EXISTS `".$ctable."`"));
        }
        $ctable = $table.'_oppose';
        if (\Phpcmf\Service::M()->db->tableExists($ctable)) {
            \Phpcmf\Service::M()->db->simpleQuery(trim("DROP TABLE IF EXISTS `".$ctable."`"));
        }


        \Phpcmf\Service::M('cache')->sync_cache('');
        $this->_json(1, '卸载成功');
    }

    // 调用代码
    public function show_index() {

        $dir = dr_safe_replace(\Phpcmf\Service::L('input')->get('dir'));

        $code = '<a href="javascript:dr_module_digg(\'{MOD_DIR}\', \'{$id}\', 1);" class="icon-btn">
    <i class="fa fa-thumbs-o-up"></i>
    <div> 有帮助 </div>
    <span class="badge badge-danger" id="module_digg_{$id}_1"> -- </span>
</a>
<a href="javascript:dr_module_digg(\'{MOD_DIR}\', \'{$id}\', 0);" class="icon-btn">
    <i class="fa fa-thumbs-o-down"></i>
    <div> 没帮助 </div>
    <span class="badge badge-danger" id="module_digg_{$id}_0"> -- </span>
</a>
<script>$(function() {
    $.get("/index.php?is_ajax=1&s=zan&mid={MOD_DIR}&id={$id}", function(data){
        if (data.code) {
            var s = data.data;
            $(\'#module_digg_{$id}_0\').html(s.a);
            $(\'#module_digg_{$id}_1\').html(s.b);
        }
}, \'json\');
});</script>';


        \Phpcmf\Service::V()->assign('code', $code);
        \Phpcmf\Service::V()->display('code.html');
        exit;
    }
}
