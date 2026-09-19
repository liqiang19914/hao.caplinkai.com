<?php namespace Phpcmf\Model\Mform;

// 模型类
class Mform extends \Phpcmf\Model
{

    // 创建
    public function create_module_form($data) {

        $data['name'] = dr_safe_filename($data['name']);

        $sql = [
            "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
              `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
			  `author` varchar(50) NOT NULL COMMENT '作者名称',
			  `inputip` varchar(200) DEFAULT NULL COMMENT '录入者ip',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  `title` varchar(255) DEFAULT NULL COMMENT '表单主题',
	          `status` tinyint(1) DEFAULT NULL COMMENT '状态值',
	          `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
	          `displayorder` int(10) DEFAULT NULL COMMENT '排序值',
			  PRIMARY KEY `id` (`id`),
			  KEY `cid` (`cid`),
			  KEY `uid` (`uid`),
              KEY `catid` (`catid`),
			  KEY `author` (`author`),
			  KEY `status` (`status`),
			  KEY `displayorder` (`displayorder`),
			  KEY `inputtime` (`inputtime`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模块表单".$data['name']."表';"
            ,
            "CREATE TABLE IF NOT EXISTS `{tablename}_data_0` (
			  `id` int(10) unsigned NOT NULL,
			  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
              `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
			  UNIQUE KEY `id` (`id`),
			  KEY `cid` (`cid`),
              KEY `catid` (`catid`),
			  KEY `uid` (`uid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模块表单".$data['name']."附表';"
        ];

        // 为全部站点模块创建表单
        foreach (\Phpcmf\Service::C()->site_info as $sid => $v) {
            $par = $this->dbprefix(dr_module_table_prefix($data['module'], $sid)); // 父级表
            $pre = $par.'_form_'.$data['table']; // 当前表
            // 判断模块是否安装过
            if (!$this->is_table_exists($par)) {
                continue;
            }
            foreach ($sql as $s) {
                $this->db->simpleQuery(str_replace('{tablename}', $pre, dr_format_create_sql($s)));
            }
            // 加上统计字段
            if ($this->is_field_exists($par, $data['table']."_total")) {
                continue;
            }
            $this->db->simpleQuery("ALTER TABLE `{$par}` ADD `".$data['table']."_total` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '表单".$data['name']."统计' , ADD INDEX (`".$data['table']."_total`) ;");
        }

        // 删除原有的冗余字段
        $this->db->table('field')->where('relatedid', $data['id'])->where('relatedname', 'mform-'.$data['module'])->delete();

        // 默认字段
        $this->db->table('field')->insert(array(
            'name' => '主题',
            'fieldname' => 'title',
            'fieldtype' => 'Text',
            'relatedid' => $data['id'],
            'relatedname' => 'mform-'.$data['module'],
            'isedit' => 1,
            'ismain' => 1,
            'ismember' => 1,
            'issystem' => 1,
            'issearch' => 1,
            'disabled' => 0,
            'setting' => dr_array2string(array(
                'option' => array(
                    'width' => 300, // 表单宽度
                    'fieldtype' => 'VARCHAR', // 字段类型
                    'fieldlength' => '255' // 字段长度
                ),
                'validate' => array(
                    'xss' => 1, // xss过滤
                    'required' => 1, // 表示必填
                )
            )),
            'displayorder' => 0,
        ));
        $this->db->table('field')->insert(array(
            'name' => '作者',
            'fieldname' => 'author',
            'fieldtype' => 'Text',
            'relatedid' => $data['id'],
            'relatedname' => 'mform-'.$data['module'],
            'isedit' => 1,
            'ismain' => 1,
            'ismember' => 1,
            'issystem' => 1,
            'issearch' => 1,
            'disabled' => 0,
            'setting' => dr_array2string(array(
                'is_right' => 1,
                'option' => array(
                    'width' => 200, // 表单宽度
                    'fieldtype' => 'VARCHAR', // 字段类型
                    'fieldlength' => '255' // 字段长度
                ),
                'validate' => array(
                    'xss' => 1, // xss过滤
                )
            )),
            'displayorder' => 0,
        ));
    }

    // 删除模块表单
    public function delete_module_form($data) {

        $id = intval($data['id']);
        $table = $this->dbprefix(dr_module_table_prefix($data['module']).'_form_'.$data['table']);
        // 判断模块是否存在表
        if (!$this->is_table_exists($table)) {
            return;
        }

        // 删除字段
        $this->db->table('field')->where('relatedid', $id)->where('relatedname', 'mform-'.$data['module'])->delete();

        // 删除表
        $this->db->simpleQuery('DROP TABLE IF EXISTS `'.$table.'`');

        // 删除附表
        for ($i = 0; $i < 200; $i ++) {
            if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->getRowArray()) {
                break;
            }
            $this->db->simpleQuery('DROP TABLE IF EXISTS '.$table.'_data_'.$i);
        }

        // 模块表统计字段删除
        $par = $this->dbprefix(dr_module_table_prefix($data['module']));
        // 判断模块是否存在表
        if (!$this->is_table_exists($par)) {
            return;
        }

        if ($this->is_field_exists($par, $data['table']."_total")) {
            $this->db->simpleQuery("ALTER TABLE `{$par}` DROP `".$data['table']."_total`;");
        }

        // 挂钩点
        \Phpcmf\Hooks::trigger('module_form_uninstall_after', $data);
    }

    // 创建表单文件
    public function create_form_file($dir, $table, $call = 0) {

        $dir = ucfirst($dir);
        $path = dr_get_app_dir($dir);
        if (!is_dir($path)) {
            return dr_return_data(1, 'ok');
        }

        $name = ucfirst($table);
        $temp = dr_get_app_dir('mform').'Code/';
        $files = [
            $path.'Controllers/'.$name.'.php' => $temp.'$NAME$.php',
            $path.'Controllers/Member/'.$name.'.php' => $temp.'Member$NAME$.php',
            $path.'Controllers/Admin/'.$name.'.php' => $temp.'Admin$NAME$.php',
            $path.'Controllers/Admin/'.$name.'_verify.php' => $temp.'Admin$NAME$_verify.php',
        ];

        $ok = 0;
        foreach ($files as $file => $form) {
            if (!is_file($file)) {
                $c = file_get_contents($form);
                $size = file_put_contents($file, str_replace('$NAME$', $name, $c));
                if (!$size && $call) {
                    unlink($file);
                    return dr_return_data(0, dr_lang('文件%s创建失败，无可写权限', str_replace(FCPATH, '', $file)));
                }
                $ok ++;
            }
        }

        return dr_return_data(1, $ok);
    }


    // 创建模块表单
    public function create_form($dir, $data) {

        // 插入表单数据
        $data['table'] = strtolower($data['table']);
        $rt = $this->table('module_form')->insert([
            'name' => $data['name'],
            'table' => $data['table'],
            'module' => $dir,
            'setting' => '',
            'disabled' => 0,
        ]);

        if (!$rt['code']) {
            return $rt;
        }

        $id = $data['id'] = $rt['code'];
        $data['module'] = $dir;

        // 创建文件
        $rt = $this->create_form_file($dir, $data['table']);
        if (!$rt['code']) {
            $this->table('module_form')->delete($id);
            return $rt;
        }

        // 创建表
        $this->create_module_form($data);

        return dr_return_data(1, 'ok');
    }

    // 删除模块表单
    public function delete_form($ids) {

        foreach ($ids as $id) {
            $row = $this->table('module_form')->get(intval($id));
            if (!$row) {
                return dr_return_data(0, dr_lang('模块表单不存在(id:%s)', $id));
            }
            $rt = $this->table('module_form')->delete($id);
            if (!$rt['code']) {
                return dr_return_data(0, $rt['msg']);
            }
            $name = ucfirst($row['table']);
            $path = dr_get_app_dir($row['module']);
            unlink($path.'Controllers/'.$name.'.php');
            unlink($path.'Controllers/Admin/'.$name.'.php');
            unlink($path.'Controllers/Member/'.$name.'.php');
            unlink($path.'Controllers/Admin/'.$name.'_verify.php');
            // 删除表数据
            $this->delete_module_form($row);
        }

        return dr_return_data(1, '');
    }

    public function link_menu($form, $table, $mdir, $config, $left) {
        // 表单入库
        if ($form) {
            foreach ($form as $t) {
                $mark = 'app-mform-verify-'.$mdir.'-'.$t['table'];
                $menu = $this->db->table($table.'_menu')->where('mark', $mark)->get()->getRowArray();
                $save = [
                    'uri' => $mdir.'/'.$t['table'].'_verify/index',
                    'mark' => $mark,
                    'name' => $menu && $menu['name'] ? $menu['name'] : dr_lang('%s%s', $config['name'], $t['name']),
                    'icon' => $menu && $menu['icon'] ? $menu['icon'] : dr_icon($t['setting']['icon']),
                    'displayorder' => $menu ? intval($menu['displayorder']) : '-1',
                ];
                $menu ? \Phpcmf\Service::M('menu')->_edit($table, $menu['id'], $save) : \Phpcmf\Service::M('menu')->_add($table, $left['id'], $save);
            }
        }
    }

    public function link_delete($module, $dir) {

        $this->db->table('field')->where('relatedid', $module['id'])->where('relatedname', 'mform-'.$dir)->delete();
        $this->db->table('admin_menu')->like('mark', 'app-mform-verify-'.$dir)->delete();
        $this->db->table('module_form')->where('module', $dir)->delete();
    }

    public function link_uninstall($table, $dir) {

        // 删除表单
        $form = $this->db->table('module_form')->where('module', $dir)->get()->getResultArray();
        if ($form) {
            foreach ($form as $t) {
                $mytable = $table.'_form_'.$t['table'];
                // 主表
                $this->db->simpleQuery('DROP TABLE IF EXISTS `'.$mytable.'`');
                // 附表
                for ($i = 0; $i < 200; $i ++) {
                    if (!$this->db->query("SHOW TABLES LIKE '".$mytable.'_data_'.$i."'")->getRowArray()) {
                        break;
                    }
                    $this->db->simpleQuery('DROP TABLE IF EXISTS `'.$mytable.'_data_'.$i.'`');
                }
            }
        }
    }

    private function _link_install($module, $mpath, $dir, $table) {
        if (is_file($mpath.'Config/Form.php')) {
            $form = require $mpath.'Config/Form.php';
            if ($form) {
                foreach ($form as $ftable => $t) {
                    // 插入表单数据
                    $rt = $this->table('module_form')->insert([
                        'name' => $t['form']['name'],
                        'table' => $ftable,
                        'module' => $dir,
                        'setting' => $t['form']['setting'],
                        'disabled' => 0,
                    ]);
                    if ($rt['code']) {
                        // 插入sql
                        $this->db->simpleQuery(str_replace('{tablename}', $table.'_form_'.$ftable, dr_format_create_sql($t['table'][1])));
                        $this->db->simpleQuery(str_replace('{tablename}', $table.'_form_'.$ftable.'_data_0', dr_format_create_sql($t['table'][0])));
                        // 插入自定义字段
                        foreach ([1, 0] as $is_main) {
                            $f = $t['field'][$is_main];
                            if ($f) {
                                foreach ($f as $field) {
                                    \Phpcmf\Service::M('module')->_add_field($field, $is_main, $rt['code'], 'mform-'.$dir);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function link_install($module, $mpath, $dir, $table) {
        // 创建表单
        if (dr_count($module['site']) == 1) {
            // 表示第一个站就创建表单
            $this->_link_install($module, $mpath, $dir, $table);
        } else {
            // 创建模块已经存在的表单
            $form = $this->db->table('module_form')->where('module', $dir)->get()->getResultArray();
            if ($form) {
                $this->db->resetDataCache();// 清除缓存，影响字段存在的重复
                foreach ($form as $t) {
                    // 表示存在多个站
                    $sid = 0;
                    foreach ($module['site'] as $site => $tt) {
                        if ($this->is_table_exists($this->dbprefix($site.'_'.$dir).'_form_'.$t['table'])) {
                            // 表示已经在其他站创建过了,我们就复制它以前创建的表结构
                            $sid = $site;
                            break;
                        }
                    }
                    if ($sid) {
                        // 开始创建表单
                        $mytable = $table.'_form_'.$t['table'];
                        $cptable = $this->dbprefix($sid.'_'.$dir).'_form_'.$t['table'];
                        // 主表
                        $sql = $this->db->query("SHOW CREATE TABLE `".$cptable."`")->getRowArray();
                        $sql = str_replace(
                            array($sql['Table'], 'CREATE TABLE'),
                            array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                            $sql['Create Table']
                        );
                        $sql = dr_format_create_sql(str_replace('{tablename}', $mytable, $sql));
                        $this->db->query($sql);
                        // 附表
                        $sql = $this->db->query("SHOW CREATE TABLE `".$cptable."_data_0`")->getRowArray();
                        $sql = str_replace(
                            array($sql['Table'], 'CREATE TABLE'),
                            array('{tablename}', 'CREATE TABLE IF NOT EXISTS'),
                            $sql['Create Table']
                        );
                        $sql = dr_format_create_sql(str_replace('{tablename}', $mytable.'_data_0', $sql));
                        $this->db->query($sql);
                    } else {
                        // 没有表就删除表单
                        //log_message('error', '没有表就删除表单');
                        $this->db->table('module_form')->where('id', $t['id'])->delete();
                    }
                }
            } else {
                $this->_link_install($module, $mpath, $dir, $table);
            }
        }
    }

    // 缓存
    public function link_cache($mdir, $cache) {
        $form = $this->table('module_form')->where('module', $mdir)->where('disabled', 0)->order_by('id ASC')->getAll();
        if ($form) {
            foreach ($form as $t) {
                $t['field'] = [];
                // 模块表单的自定义字段
                if (!$this->table('field')
                    ->where('relatedname', 'mform-'.$mdir)
                    ->where('relatedid', intval($t['id']))
                    ->where('fieldname', 'author')->counts()) {
                    $this->db->table('field')->insert(array(
                        'name' => '作者',
                        'fieldname' => 'author',
                        'fieldtype' => 'Text',
                        'relatedid' => $t['id'],
                        'relatedname' => 'mform-'.$mdir,
                        'isedit' => 1,
                        'ismain' => 1,
                        'ismember' => 1,
                        'issystem' => 1,
                        'issearch' => 1,
                        'disabled' => 0,
                        'setting' => dr_array2string(array(
                            'is_right' => 1,
                            'option' => array(
                                'width' => 200, // 表单宽度
                                'fieldtype' => 'VARCHAR', // 字段类型
                                'fieldlength' => '255' // 字段长度
                            ),
                            'validate' => array(
                                'xss' => 1, // xss过滤
                            )
                        )),
                        'displayorder' => 0,
                    ));
                }
                $field = $this->db->table('field')->where('disabled', 0)->where('relatedid', intval($t['id']))->where('relatedname', 'mform-'.$mdir)->orderBy('displayorder ASC, id ASC')->get()->getResultArray();
                if ($field) {
                    foreach ($field as $f) {
                        $f['setting'] = dr_string2array($f['setting']);
                        $t['field'][$f['fieldname']] = $f;
                    }
                }
                $t['setting'] = dr_string2array($t['setting']);
                // 排列table字段顺序
                $t['setting']['list_field'] = dr_list_field_order($t['setting']['list_field']);
                $cache['form'][$t['table']] = $t;
            }
        }
        return $cache;
    }

    // 保存数据
    public function save_content($mid, $tid, $index, $data, $data2 = []) {

        $data['status'] = isset($data['status']) ? intval($data['status']) : 1;
        $data['uid'] = isset($data['uid']) ? intval($data['uid']) : (int)$this->member['uid'];
        $data['author'] = isset($data['author']) ? trim($data['author']) : $this->member['username'];
        $data['inputip'] = isset($data['inputip']) ? $data['inputip'] : \Phpcmf\Service::L('input')->ip_info();
        $data['inputtime'] = isset($data['inputtime']) ? $data['inputtime'] : SYS_TIME;
        $data['tableid'] = 0;
        $data['displayorder'] = isset($data['displayorder']) ? $data['displayorder'] : 0;
        $data['cid'] = intval($index['id']); // 内容id
        $data['catid'] = intval($index['catid']); // 栏目id
        // 插入主表
        $rt = $this->table_site($mid."_form_".$tid)->insert($data);
        if (!$rt['code']) {
            return $rt;
        }

        if ($data2) {
            // 如果要使用附表分表就 按一定量进行分表设置 比如50000
            $data['tableid'] = \Phpcmf\Service::M()->get_table_id($rt['code']);
            $this->table_site($mid."_form_".$tid)->update($data['id'], ['tableid' => $data['tableid']]);
            if ($data['tableid'] > 0) {
                // 判断附表是否存在,不存在则创建
                $this->is_data_table($mid."_form_".$tid.'_data_', $data['tableid']);
            }

            $data2['id'] = $rt['code'];
            $data2['uid'] = (int)$data['uid'];
            $data2['cid'] = intval($index['id']); // 内容id
            $data2['catid'] = intval($index['catid']); // 栏目id
            // 插入附表
            $rt2 = $this->table_site($mid."_form_".$tid."_data_".$data['tableid'])->insert($data2);
            if (!$rt2['code']) {
                // 删除主表
                $this->table_site($mid."_form_".$tid)->delete($data['id']);
                return $rt2;
            }
        }

        $total = $this->table_site($mid."_form_".$tid)->where('status', 1)->where('cid', $data['cid'])->counts();
        $this->table_site($mid)->update($data['cid'], [
            $tid.'_total' => $total,
        ]);

        return $rt;
    }


    // 模块表单内容地址
    public function show_url($form, $id, $mid = '', $page = 0) {

        // 模块目录识别
        defined('MOD_DIR') && MOD_DIR && $dir = MOD_DIR;
        $mid && $dir = $mid;

        $module = \Phpcmf\Service::L('cache')->get('module-' . SITE_ID . '-' . $dir);

        return \Phpcmf\Service::L('router')->url_prefix('php', $module, [], SITE_FID) . 'c=' . $form . '&m=show&id=' . $id . ($page > 1 || strlen($page) > 1 ? '&page=' . $page : '');
    }


    // 模块表单提交地址
    public function post_url($form, $cid, $mid = '') {

        // 模块目录识别
        defined('MOD_DIR') && MOD_DIR && $dir = MOD_DIR;
        $mid && $dir = $mid;

        $module = \Phpcmf\Service::L('cache')->get('module-' . SITE_ID . '-' . $dir);

        return \Phpcmf\Service::L('router')->url_prefix('php', $module, [], SITE_FID) . 'c=' . $form . '&m=post&cid=' . $cid;
    }


    // 模块表单列表地址
    public function list_url($form, $cid, $mid = '', $page = 0) {

        // 模块目录识别
        defined('MOD_DIR') && MOD_DIR && $dir = MOD_DIR;
        $mid && $dir = $mid;

        $module = \Phpcmf\Service::L('cache')->get('module-' . SITE_ID . '-' . $dir);

        return \Phpcmf\Service::L('router')->url_prefix('php', $module, [], SITE_FID) . 'c=' . $form . '&m=index&cid=' . $cid . ($page > 1 || strlen($page) > 1 ? '&page=' . $page : '');
    }

    /**
     * 删除模块内容时的联动
     */
    public function delete_content($id, $siteid, $dirname) {

        if (!$id) {
            return;
        }

        $module = \Phpcmf\Service::L('cache')->get('module-' . SITE_ID . '-' . $dirname);
        if ($module['form']) {
            foreach ($module['form'] as $m) {
                $table = $this->dbprefix(dr_module_table_prefix($dirname).'_form_'.$m['table']);
                // 判断模块是否存在表
                if ($this->is_table_exists($table)) {
                   $this->db->table($table)->where('cid', $id)->delete();
                   $this->db->table($table.'_data_0')->where('cid', $id)->delete();
                }
            }
        }

    }
}