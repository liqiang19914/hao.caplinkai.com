<?php namespace Phpcmf\Controllers;


class Home extends \Phpcmf\App
{


    public function index() {

        $id = intval(\Phpcmf\Service::L('input')->get('id'));
        $dirname = dr_safe_replace(\Phpcmf\Service::L('input')->get('mid'));
        if ($dirname == 'MOD_DIR') {
            $this->_msg(0, dr_lang('app参数存在问题'));
        } elseif (!$dirname || !dr_is_app_dir(($dirname))) {
            $this->_msg(0, dr_lang('模块目录[%s]不存在', $dirname));
        }

        $row = \Phpcmf\Service::M()->table_site($dirname)->get($id);
        if (!$row) {
            $this->_json(0, '内容不存在');
        }

        $this->_json(1, 'ok', [
            'a' => (int)$row['oppose'],
            'b' => (int)$row['support']
        ]);
    }

    /**
     * 模块内容支持与反对
     */
    public function digg() {

        $mid = dr_safe_replace(\Phpcmf\Service::L('input')->get('app'));
        if ($mid == 'MOD_DIR') {
            $this->_msg(0, dr_lang('app参数存在问题'));
        } elseif (!$mid || !dr_is_module($mid)) {
            $this->_msg(0, dr_lang('模块目录[%s]不存在', $this->dirname));
        }

        $this->_module_init($mid);

        $rt = \Phpcmf\Service::M('op', 'zan')->run(SITE_ID.'_'.$mid, $mid);

        $this->_json($rt['code'], $rt['msg'], $rt['data']);
    }
    
    // 判断是否点赞过
    public function is_digg() {

        $mid = dr_safe_replace(\Phpcmf\Service::L('input')->get('app'));
        if ($mid == 'MOD_DIR') {
            $this->_msg(0, dr_lang('app参数存在问题'));
        } elseif (!$mid || !dr_is_module($mid)) {
            $this->_msg(0, dr_lang('模块目录[%s]不存在', $this->dirname));
        }

        if (!dr_in_array('support', \Phpcmf\Service::M('table')->get_cache_field(SITE_ID.'_'.$mid)) ) {
            $this->_json(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $mid));
        } elseif (!dr_in_array('oppose', \Phpcmf\Service::M('table')->get_cache_field(SITE_ID.'_'.$mid)) ) {
            $this->_json(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $mid));
        }

        $id = (int)\Phpcmf\Service::L('input')->get('id');
        if (!$id) {
            $this->_json(0, dr_lang('id参数不完整'));
        }

        $value = (int)\Phpcmf\Service::L('input')->get('value');

        $field = $value ? 'support' : 'oppose';
        $table = SITE_ID.'_'.$mid.'_'.$field;
        if (!\Phpcmf\Service::M()->db->tableExists($table)) {
            $this->_json(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $mid));
        }

        $agent = md5(\Phpcmf\Service::L('input')->get_user_agent().\Phpcmf\Service::L('input')->ip_address());
        if (!$this->uid) {
            $result = \Phpcmf\Service::M()->db->table($table)->where('cid', $id)->where('uid', $this->uid)->where('agent', $agent)->get()->getRowArray();
        } else {
            $result = \Phpcmf\Service::M()->db->table($table)->where('cid', $id)->where('uid', $this->uid)->get()->getRowArray();
        }

        if ($result) {
            $this->_json(1, '点赞过了', 1);
        }

        // 返回结果
        $this->_json(1, '没有点赞', 0);
    }

}
