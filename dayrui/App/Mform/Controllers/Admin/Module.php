<?php namespace Phpcmf\Controllers\Admin;

/**
 * http://www.xunruicms.com
 * 本文件是框架系统文件，二次开发时不可以修改本文件
 **/

class Module extends \Phpcmf\Common
{

    private $dir;
    private $form;

    public function __construct() {
        parent::__construct();

        $this->dir = dr_safe_replace(\Phpcmf\Service::L('input')->get('dir'));

        $menu = [
            '内容模块' => ['module/module/index', 'fa fa-cogs'],
            '模块表单' => ['mform/module/index', 'fa fa-cogs'],
        ];

        $menu['表单配置'] = ['hide:mform/module/form_edit', 'fa fa-cog'];
        $menu['重建表单'] = ['ajax:mform/module/form_init_index', 'fa fa-refresh'];
        $menu['help'] = [98];

        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu($menu));

        // 表单验证配置
        $this->form = [
            'name' => [
                'name' => '表单名称',
                'rule' => [
                    'empty' => dr_lang('表单名称不能为空')
                ],
                'filter' => [],
                'length' => '200'
            ],
            'table' => [
                'name' => '表单别名',
                'rule' => [
                    'empty' => dr_lang('表单别名不能为空'),
                    'table' => dr_lang('表单别名格式不正确'),
                ],
                'filter' => [],
                'length' => '200'
            ],
        ];
    }

    // 模块管理
    public function index() {

        $module = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-content');
        if (!$module) {
            $this->_admin_msg(0, dr_lang('未安装任何内容模块'));
        }

        \Phpcmf\Service::V()->assign([
            'module' => $module,
        ]);
        \Phpcmf\Service::V()->display('module.html');
    }

    // 隐藏或者启用
    public function mhidden_edit() {

        $id = (int)\Phpcmf\Service::L('input')->get('id');
        $row = \Phpcmf\Service::M()->table('module_form')->get($id);
        if (!$row) {
            $this->_json(0, dr_lang('数据#%s不存在', $id));
        }

        $v = $row['disabled'] ? 0 : 1;
        \Phpcmf\Service::M()->table('module_form')->update($id, ['disabled' => $v]);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json(1, dr_lang($v ? '模块表单已被禁用' : '模块表单已被启用'), ['value' => $v]);
    }

    // 创建模块表单
    public function form_add() {

        if (IS_AJAX_POST) {
            $data = \Phpcmf\Service::L('input')->post('data');
            if (!preg_match('/^[a-z]+[a-z0-9\_]+$/i', $data['table'])) {
                $this->_json(0, dr_lang('表单别名不规范'));
            } elseif (\Phpcmf\Service::M('app')->is_sys_dir($data['table'])) {
                $this->_json(0, dr_lang('名称[%s]是系统保留名称，请重命名', $data['table']));
            }
            $this->_validation(0, $data);
            \Phpcmf\Service::L('input')->system_log('创建模块['.$this->dir.']表单('.$data['name'].')');
            $rt = \Phpcmf\Service::M('mform', 'mform')->create_form($this->dir, $data);
            if ($rt['code']) {
                \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, dr_lang('操作成功，请刷新后台页面'));
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        \Phpcmf\Service::V()->assign([
            'form' => dr_form_hidden()
        ]);
        \Phpcmf\Service::V()->display('module_form_add.html');
        exit;
    }

    // 修改模块表单
    public function form_edit() {

        $id = intval(\Phpcmf\Service::L('input')->get('id'));
        $data = \Phpcmf\Service::M()->table('module_form')->get($id);
        if (!$data) {
            $this->_admin_msg(0, dr_lang('模块表单（%s）不存在', $id));
        }

        $data['setting'] = dr_string2array($data['setting']);
        !$data['setting']['list_field'] && $data['setting']['list_field'] = [
            'title' => [
                'use' => 1,
                'name' => dr_lang('主题'),
                'func' => 'title',
                'width' => 0,
                'order' => 1,
            ],
            'uid' => [
                'use' => 1,
                'name' => dr_lang('账号'),
                'func' => 'uid',
                'width' => 100,
                'order' => 2,
            ],
            'inputtime' => [
                'use' => 1,
                'name' => dr_lang('录入时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 3,
            ],
        ];

        if (IS_AJAX_POST) {
            $data = \Phpcmf\Service::L('input')->post('data');
            if ($data['setting']['list_field']) {
                foreach ($data['setting']['list_field'] as $t) {
                    if ($t['func']) {
                        if (method_exists(\Phpcmf\Service::L('Function_list'), $t['func'])) {
                        } elseif (!function_exists($t['func'])) {
                            $this->_json(0, dr_lang('列表回调函数[%s]未定义', $t['func']));
                        } elseif (strpos($t['func'], 'dr_') === false && strpos($t['func'], 'my_') === false) {
                            $this->_json(0, '函数【'.$t['func'].'】必须以dr_或者my_开头');
                        }
                    }
                }
            }
            if ($data['setting']['order']) {
                if (strpos($data['setting']['order'], '(') or strpos($data['setting']['order'], ')')) {
                    $this->_json(0, dr_lang('后台列表的默认排序字段不允许特殊符号'));
                }
            }
            \Phpcmf\Service::M()->table('module_form')->update($id,
                [
                    'name' => $data['name'],
                    'setting' => dr_array2string($data['setting'])
                ]
            );

            \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
            \Phpcmf\Service::L('input')->system_log('修改模块['.$this->dir.']表单('.$data['name'].')配置');
            $this->_json(1, dr_lang('操作成功'));
        }

        // 主表字段
        $field = \Phpcmf\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'mform-'.$this->dir)
            ->where('relatedid', $id)
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Phpcmf\Service::L('Field')->sys_field(['id', 'uid', 'inputtime', 'inputip', 'displayorder']);

        // 关联信息
        $field['cid'] = [
            'name' => dr_lang('关联'),
            'ismain' => 1,
            'ismember' => 1,
            'fieldtype' => 'Cid',
            'fieldname' => 'cid',
            'setting' => []
        ];
        if (!$data['setting']['list_field']['cid']['func']) {
            $data['setting']['list_field']['cid']['func'] = 'ctitle';
        }

        $page = intval(\Phpcmf\Service::L('input')->get('page'));

        \Phpcmf\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => dr_form_hidden(['page' => $page]),
			'field' => dr_list_field_value($data['setting']['list_field'], $sys_field, $field),
            'diy_tpl' => is_file(dr_get_app_dir($this->dir).'Views/diy_'.$data['table'].'.html') ? dr_get_app_dir($this->dir).'Views/diy_'.$data['table'].'.html' : '',
        ]);
        \Phpcmf\Service::V()->display('module_form_edit.html');
    }

    // 删除表单
    public function del() {

        $id = (int)\Phpcmf\Service::L('input')->get('id');
        if (!$id) {
            $this->_json(0, dr_lang('你还没有选择呢'));
        }

        $rt = \Phpcmf\Service::M('mform', 'mform')->delete_form([$id]);
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        \Phpcmf\Service::L('input')->system_log('删除模块表单: '. $id);

        $this->_json(1, dr_lang('操作成功'));
    }

    // 验证数据
    private function _validation($id, $data) {

        list($data, $return) = \Phpcmf\Service::L('Form')->validation($data, $this->form);
        if ($return) {
            $this->_json(0, $return['error'], ['field' => $return['name']]);
        }
        if (\Phpcmf\Service::M()->table('module_form')->where('module', $this->dir)->is_exists($id, 'table', $data['table'])) {
            $this->_json(0, dr_lang('数据表名称已经存在'), ['field' => 'table']);
        }
    }


    // 表单初始化
    public function form_init_index() {

        $data = \Phpcmf\Service::M()->table('module_form')->getAll();
        if (!$data) {
            $this->_json(0, dr_lang('没有任何可用表单'));
        }

        $ct = $file = 0;
        foreach ($data as $t) {
            $par = \Phpcmf\Service::M()->dbprefix(dr_module_table_prefix($t['module'], SITE_ID)); // 父级表
            if (!\Phpcmf\Service::M()->is_table_exists($par)) {
                continue; // 当前站点没有安装
            }
            $rt = \Phpcmf\Service::M('mform', 'mform')->create_form_file($t['module'], $t['table'], 1);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }
            $file+= (int)$rt['msg'];
            $ct++;
            // 创建统计字段
            $fname = $t['table']."_total";
            if (!\Phpcmf\Service::M()->db->fieldExists($fname, $par)) {
                \Phpcmf\Service::M()->db->simpleQuery("ALTER TABLE `{$par}` ADD `{$fname}` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '表单".$t['name']."统计' , ADD INDEX (`".$fname."`) ;");
            }
        }

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json(1, dr_lang('本站点共（%s）个表单，重建（%s）个文件', $ct, $file));
    }


}
