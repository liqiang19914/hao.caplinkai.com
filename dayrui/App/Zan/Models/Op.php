<?php namespace Phpcmf\Model\Zan;

// 自动执行方法
class Op extends \Phpcmf\Model
{

    public function run($tablename, $dirname) {

        if (!dr_in_array('support', \Phpcmf\Service::M('table')->get_cache_field($tablename)) ) {
            return dr_return_data(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $dirname));
        } elseif (!dr_in_array('oppose', \Phpcmf\Service::M('table')->get_cache_field($tablename)) ) {
            return dr_return_data(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $dirname));
        }

        $id = (int)\Phpcmf\Service::L('input')->get('id');
        if (!$id) {
            return dr_return_data(0, dr_lang('id参数不完整'));
        }

        $value = (int)\Phpcmf\Service::L('input')->get('value');
        $data = $this->db->table($tablename.'_index')->where('id', $id)->countAllResults();
        if (!$data) {
            return dr_return_data(0, dr_lang('模块内容不存在'));
        }

        $field = $value ? 'support' : 'oppose';
        $table = $tablename.'_'.$field;
        if (!$this->db->tableExists($table)) {
            return dr_return_data(0, dr_lang('应用[模块内容点赞]未安装到本模块[%s]', $dirname));
        }

        $agent = md5(\Phpcmf\Service::L('input')->get_user_agent().\Phpcmf\Service::L('input')->ip_address());
        if (!$this->uid) {
            $result = $this->db->table($table)->where('cid', $id)->where('uid', $this->uid)->where('agent', $agent)->get()->getRowArray();
        } else {
            $result = $this->db->table($table)->where('cid', $id)->where('uid', $this->uid)->get()->getRowArray();
        }

        if ($result) {
            // 已经操作了,我们就删除它
            $this->db->table($table)->where('id', intval($result['id']))->delete();
            $msg = dr_lang('操作取消');
        } else {
            if (!\Phpcmf\Service::M()->db->fieldExists('inputtime', $table)) {
                \Phpcmf\Service::M()->query('ALTER TABLE `'.$table.'` ADD `inputtime` int(10) unsigned DEFAULT \'0\' COMMENT \'操作时间\';');
            }
            $this->db->table($table)->insert(array(
                'cid' => $id,
                'uid' => $this->uid,
                'agent' => $agent,
                'inputtime' => SYS_TIME,
            ));
            $msg = dr_lang('操作成功');
        }

        // 更新数量
        $c = $this->db->table($table)->where('cid', $id)->countAllResults();
        $this->db->table($tablename)->where('id', $id)->set($field, $c)->update();
        \Phpcmf\Service::M('cache')->update_data_cache();

        // 返回结果
        return dr_return_data(1, $msg, $c);
    }


}