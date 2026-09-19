<?php namespace Phpcmf\Controllers\Member;


class Home extends \Phpcmf\Table
{
    public $module;
    public $my_module;

    public function __construct()
    {
        parent::__construct();
        $this->module = \Phpcmf\Service::L('cache')->get('module-'.SITE_ID.'-content');
        if (!$this->module) {
            $this->_msg(0, dr_lang('系统还没有可用的内容模块'));
            exit;
        }
        foreach ($this->module as $dir => $t) {
            if (!dr_in_array('support', \Phpcmf\Service::M('table')->get_cache_field(SITE_ID.'_'.$dir)) ) {
                unset($this->module[$dir]);
            }
            $table = SITE_ID.'_'.$t['dirname'].'_support';
            if (!\Phpcmf\Service::M()->db->tableExists($table)) {
                unset($this->module[$dir]);
            }
        }
        if (!$this->module) {
            $this->_msg(0, dr_lang('内容模块都没有安装点赞插件'));
            exit;
        }

        $dir = \Phpcmf\Service::L('Input')->get('module');
        if (!$dir || !isset($this->module[$dir])) {
            $one = reset($this->module);
            $this->my_module = $one['dirname'];
        } else {
            $this->my_module = $dir;
        }
    }

    // 模板显示
    private function _display($data) {

        // 初始化变量
        unset($data['param']['module']);
        unset($data['param']['total']);
        unset($data['param']['order']);

        // 列出内容模块
        foreach ($this->module as $i => $t) {
            $data['param']['module'] = $i;
            $this->module[$i]['url'] = dr_member_url(APP_DIR.'/home/'.\Phpcmf\Service::L('Router')->method, $data['param']);
        }

        $list = [];
        if ($data['list']) {
            foreach ($data['list'] as $i => $t) {
                if (!$t['title'] and !$t['url']) {
                    \Phpcmf\Service::M()->table_site($this->my_module.'_oppose')
                        ->where('uid', $this->uid)->where('cid', $t['cid'])->delete();
                    \Phpcmf\Service::M()->table_site($this->my_module.'_support')
                        ->where('uid', $this->uid)->where('cid', $t['cid'])->delete();
                    continue;
                }
                $t['url'] = dr_url_prefix($t['url'], $this->my_module);
                $list[] = $t;
            }
        }

        \Phpcmf\Service::V()->assign([
            'list' => $list,
            'module' => $this->module,
            'my_module' => $this->my_module,
            'del_url' =>\Phpcmf\Service::L('Router')->member_url(APP_DIR.'/home/delete', [
                'module' => $this->my_module,
                'action' =>\Phpcmf\Service::L('Router')->method
            ])
        ]);
        \Phpcmf\Service::V()->display('content_'.\Phpcmf\Service::L('Router')->method.'.html');
    }

    /**
     * 删除
     */
    public function delete() {

        !IS_POST && $this->_json(0, dr_lang('请求错误'));

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('所选数据不存在'));

        // 格式化
        $in = [];
        foreach ($ids as $i) {
            $i && $in[] = intval($i);
        }

        !$in && $this->_json(0, dr_lang('所选数据不存在'));


        $table = dr_module_table_prefix($this->my_module);
        $action = \Phpcmf\Service::L('Input')->get('action');
        switch ($action) {

            case 'support':
                // 跳转到module方法
                goto module;
                break;

            case 'oppose':
                // 跳转到module方法
                goto module;
                break;

            case 'public':
                module:
                // 收藏夹
                \Phpcmf\Service::M()->db->table($table.'_'.$action)->where('uid', $this->uid)->whereIn('cid', $in)->delete();
                break;

            default:
                $this->_json(0, dr_lang('未知项目'));
                break;

        }

        $this->_json(1, dr_lang('操作成功'));
    }

    /**
     * 支持
     */
    public function support() {

        $table = dr_module_table_prefix($this->my_module);

        $this->_init([
            'table' => $table.'_support',
            'join_list' => [$table, $table.'.id='.$table.'_support.cid', 'left'],
            'order_by' => $table.'_support.inputtime desc',
            'date_field' => $table.'_support.inputtime',
            'where_list' => $table.'_support.`uid`='.$this->uid,
        ]);

        list($tpl, $data) = $this->_List();

        $this->_display($data);
    }

    /**
     * 反对
     */
    public function oppose() {

        $table = dr_module_table_prefix($this->my_module);
        $this->_init([
            'table' => $table.'_oppose',
            'join_list' => [$table, $table.'.id='.$table.'_oppose.cid', 'left'],
            'order_by' => $table.'_oppose.inputtime desc',
            'date_field' => $table.'_oppose.inputtime',
            'where_list' => $table.'_oppose.`uid`='.$this->uid,
        ]);

        list($tpl, $data) = $this->_List();

        $this->_display($data);
    }

}
