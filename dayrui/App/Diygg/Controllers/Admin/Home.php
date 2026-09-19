<?php namespace Phpcmf\Controllers\Admin;

// 广告管理
class Home extends \Phpcmf\Table
{

    public function __construct()
    {
        parent::__construct();
        // 表单显示名称
        $this->name = dr_lang('广告管理');
        // 模板前缀(避免混淆)
        $this->tpl_prefix = 'home_';

        // 广告分类数据
        $tn = \Phpcmf\Service::M('diygg', 'diygg')->get_typename();
        $typeid = \Phpcmf\Service::M('diygg', 'diygg')->option_data($tn);

        // 时间限制数据
        $ts = \Phpcmf\Service::M('diygg', 'diygg')->timeset;
        $timeset = \Phpcmf\Service::M('diygg', 'diygg')->option_data($ts);

        // 用于表储存的字段，后台可修改的表字段，设置字段类别参考：http://help.xunruicms.com/1138.html
        $field = array (
            'typeid' =>
                array (
                    'name' => '广告分类',
                    'fieldname' => 'typeid',
                    'ismain' => '1',
                    'fieldtype' => 'Select',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'options' => $typeid,
                                    'value' => '',
                                    'fieldtype' => '',
                                    'fieldlength' => '',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                ),
            'adname' =>
                array (
                    'name' => '广告位名称',
                    'fieldname' => 'adname',
                    'ismain' => '1',
                    'fieldtype' => 'Text',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'fieldtype' => '',
                                    'fieldlength' => '',
                                    'value' => '',
                                    'width' => '600',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '1',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                    'issearch' => '1',
                ),
            'timeset' =>
                array (
                    'name' => '时间限制',
                    'fieldname' => 'timeset',
                    'ismain' => '1',
                    'fieldtype' => 'Radio',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'options' => $timeset,
                                    'is_field_ld' => '0',
                                    'value' => '0',
                                    'fieldtype' => '',
                                    'fieldlength' => '',
                                    'show_type' => '0',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                ),
            'starttime' =>
                array (
                    'name' => '开始时间',
                    'fieldname' => 'starttime',
                    'ismain' => '1',
                    'fieldtype' => 'Date',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'format2' => '0',
                                    'is_left' => '0',
                                    'value' => '',
                                    'width' => '',
                                    'color' => '',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                ),
            'endtime' =>
                array (
                    'name' => '结束时间',
                    'fieldname' => 'endtime',
                    'ismain' => '1',
                    'fieldtype' => 'Date',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'format2' => '0',
                                    'is_left' => '0',
                                    'value' => '',
                                    'width' => '',
                                    'color' => '',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                ),
            'overpic' =>
                array (
                    'name' => '过期占位图',
                    'fieldname' => 'overpic',
                    'ismain' => '1',
                    'fieldtype' => 'File',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'fieldtype' => '',
                                    'fieldlength' => '',
                                    'ext' => 'jpg,gif,png,jpeg',
                                    'size' => '2',
                                    'attachment' => '0',
                                    'image_reduce' => '',
                                    'is_ext_tips' => '0',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                ),
            'remark' =>
                array (
                    'name' => '备注',
                    'fieldname' => 'remark',
                    'ismain' => '1',
                    'fieldtype' => 'Textarea',
                    'setting' =>
                        array (
                            'option' =>
                                array (
                                    'value' => '',
                                    'fieldtype' => '',
                                    'fieldlength' => '',
                                    'width' => '400',
                                    'height' => '80',
                                    'css' => '',
                                ),
                            'validate' =>
                                array (
                                    'required' => '0',
                                    'pattern' => '',
                                    'errortips' => '',
                                    'check' => '',
                                    'filter' => '',
                                    'formattr' => '',
                                    'tips' => '',
                                ),
                        ),
                    'ismember' => '1',
                ),
        );

        // 初始化数据表
        $this->_init([
            'table' => 'diygg',  // （不带前缀的）表名字
            'field' => $field, // 可查询的字段
            'order_by' => 'id desc', // 列表排序，默认的排序方式
        ]);

        // 把公共变量传入模板
        \Phpcmf\Service::V()->assign([
            'adstyledata' => \Phpcmf\Service::M('diygg', 'diygg')->adstyle,
            // 搜索字段
            'field' => $field,
            // 后台的菜单
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    $this->name => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-wrench'],
                    '添加广告' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus'],
//                    '修改' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-edit'],
                    '分类管理' => ['diygg/adtype/index', 'fa fa-reorder'],
                    '添加分类' => ['diygg/adtype/add', 'fa fa-plus'],
                ])
        ]);
    }

    // 查看列表
    public function index() {
        list($tpl) = $this->_List();
        \Phpcmf\Service::V()->display($tpl);
    }

    // 添加内容
    public function add() {
        list($tpl) = $this->_Post(0);
        \Phpcmf\Service::V()->assign('adstyle', 1);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 修改内容
    public function edit() {
        list($tpl) = $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->display($tpl);
    }

    // 删除内容
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            function($rows) {
                // 删除前的验证
                return dr_return_data(1, 'ok', $rows);
            },
            function($rows) {
                // 删除后的处理
                return dr_return_data(1, 'ok');
            },
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

    // 保存内容
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {
        return parent::_Save($id, $data, $old, function($id, $data, $old){

            $post = \Phpcmf\Service::L('Input')->post('data', false);
            $data[1]['adstyle'] = $post['adstyle'];
            $data[1]['adbody'] = ($post['adstyle'] == 3) ? htmlspecialchars($post['adbody'], ENT_QUOTES) : dr_array2string($post['adbody']);

            // 验证数据
            if ($data[1]['timeset'] == 1) {
                if (!$data[1]['starttime']) {
                    return dr_return_data(0, '请填写广告开始时间！', ['field' => 'starttime']);
                }
                if (!$data[1]['endtime']) {
                    return dr_return_data(0, '请填写广告结束时间！', ['field' => 'endtime']);
                }
            }
            if (!$data[1]['adname']) {
                return dr_return_data(0, '请填写广告位名称！', ['field' => 'adname']);
            }

            // 保存之前执行的函数，并返回新的数据
            if (!$id) {
                // 当提交新数据时，把当前时间插入进去
                //$data[1]['inputtime'] = SYS_TIME;
            }

            return dr_return_data(1, null, $data);
        }, function ($id, $data, $old) {
            // 保存之后执行的动作
        });
    }

    public function getfield() {
        $rt = '';
        if (IS_POST) {
            $adstyle = (int)\Phpcmf\Service::L('Input')->post('adstyle');
            $id = (int)\Phpcmf\Service::L('Input')->post('id');
            $status = \Phpcmf\Service::L('Input')->post('status');

            if ($id) {// 修改
                $data = \Phpcmf\Service::M()->table($this->init['table'])->get($id);
                $value = $status ? $data['adbody'] : '';
            } else {// 新增
                $value = '';
            }

            if ($adstyle == 1) {
                $field = '{"name":"广告内容(图片)","fieldname":"adbody","ismain":"1","fieldtype":"Ftable","setting":{"option":{"is_add":"0","is_first_hang":"0","count":"1","first_cname":"","hang":{"1":{"name":""},"2":{"name":""},"3":{"name":""},"4":{"name":""},"5":{"name":""}},"field":{"pic":{"type":"3","name":"上传图片","width":"15%","option":"2-jpg,jpeg,png,gif"},"link":{"type":"1","name":"图片链接","width":"40%","option":""},"width":{"type":"1","name":"图片宽度px","width":"10%","option":""},"height":{"type":"1","name":"图片高度px","width":"10%","option":""},"descrip":{"type":"1","name":"图片描述","width":"25%","option":""}},"attachment":"0","image_reduce":"","width":"","css":""},"validate":{"required":"0","pattern":"","errortips":"","check":"","filter":"","formattr":"","tips":""}},"ismember":"1"}';
            } elseif ($adstyle == 2) {
                $field = '{"name":"广告内容(幻灯图片)","fieldname":"adbody","ismain":"1","fieldtype":"Ftable","setting":{"option":{"is_add":"1","is_first_hang":"0","count":"1","first_cname":"","hang":{"1":{"name":""},"2":{"name":""},"3":{"name":""},"4":{"name":""},"5":{"name":""}},"field":{"pic":{"type":"3","name":"上传图片","width":"15%","option":"2-jpg,jpeg,png,gif"},"link":{"type":"1","name":"图片链接","width":"40%","option":""},"width":{"type":"1","name":"图片宽度px","width":"10%","option":""},"height":{"type":"1","name":"图片高度px","width":"10%","option":""},"descrip":{"type":"1","name":"图片描述","width":"25%","option":""}},"attachment":"0","image_reduce":"","width":"","css":""},"validate":{"required":"0","pattern":"","errortips":"","check":"","filter":"","formattr":"","tips":"图片宽度、高度只需填写第一行"}},"ismember":"1"}';
            } elseif ($adstyle == 3) {
                $field = '{"name":"广告内容(代码)","fieldname":"adbody","ismain":"1","fieldtype":"Textarea","setting":{"option":{"value":"","fieldtype":"","fieldlength":"","width":"800","height":"140","css":""},"validate":{"required":"0","pattern":"","errortips":"","check":"","filter":"","formattr":"","tips":"复制代码，粘贴即可"}},"ismember":"1"}';
            } elseif ($adstyle == 4) {
                $field = '{"name":"广告内容(文字)","fieldname":"adbody","ismain":"1","fieldtype":"Ftable","setting":{"option":{"is_add":"0","is_first_hang":"0","count":"1","first_cname":"","hang":{"1":{"name":""},"2":{"name":""},"3":{"name":""},"4":{"name":""},"5":{"name":""}},"field":{"title":{"type":"1","name":"文字内容","width":"30%","option":""},"link":{"type":"1","name":"文字链接","width":"50%","option":""},"color":{"type":"1","name":"文字颜色","width":"10%","option":""},"size":{"type":"1","name":"文字大小","width":"10%","option":""}},"attachment":"0","image_reduce":"","width":"","css":""},"validate":{"required":"0","pattern":"","errortips":"","check":"","filter":"","formattr":"","tips":"1、文字颜色：例如: red或#EF8684；2、文字大小：例如: 14px"}},"ismember":"1"}';
            } else {
                $field = '';
            }
            $rt = dr_fieldform($field, $value, 0, 0);
        }
        return $rt;
    }

    public function adshow() {
        $id = (int)\Phpcmf\Service::L('input')->get('id');
        $data = \Phpcmf\Service::M()->table($this->init['table'])->get($id);

        if (!$data) {
            $this->_json(0, dr_lang('数据不存在'));
        }

        $content = ($data['adstyle'] == 3) ? $data['adbody'] : dr_string2array($data['adbody']);

        if ($data['adstyle'] == 1 || $data['adstyle'] == 2) {// 图片、幻灯图片
            $list = [
                ['name' => '广告图片', 'width' => '40%'],
                ['name' => '广告链接', 'width' => '30%'],
                ['name' => '图片宽度', 'width' => '10%'],
                ['name' => '图片高度', 'width' => '10%'],
                ['name' => '图片描述', 'width' => '10%'],
            ];
            foreach ($content as $i => $t) {
                $show[$i] = [
                    's1' => '<img src="'.dr_get_file($t['pic']).'">',
                    's2' => '<a href="'.$t['link'].'">'.$t['link'].'</a>',
                    's3' => $t['width'],
                    's4' => $t['height'],
                    's5' => $t['descrip'],
                ];
            }

        } elseif ($data['adstyle'] == 3) {// 代码
            $list = [
                ['name' => '广告代码', 'width' => '100%'],
            ];
            $show[] = [
                's1' => '<textarea class="form-control" style="height:260px; width:100%;">'.$content.'</textarea>',
            ];
        } elseif ($data['adstyle'] == 4) {// 文字
            $list = [
                ['name' => '广告文字', 'width' => '40%'],
                ['name' => '广告链接', 'width' => '40%'],
                ['name' => '文字颜色', 'width' => '10%'],
                ['name' => '文字大小', 'width' => '10%'],
            ];
            foreach ($content as $i => $t) {
                $show[$i] = [
                    's1' => '<span>'.$t['title'].'</span>',
                    's2' => '<a href="'.$t['link'].'">'.$t['link'].'</a>',
                    's3' => $t['color'],
                    's4' => $t['size'],
                ];
            }
        } else {
            $this->_json(0, dr_lang('无此类型'));
        }

        $adcode = \Phpcmf\Service::M('diygg', 'diygg')->generate_ad($data, 0);

        \Phpcmf\Service::V()->assign([
            'list' => $list,
            'show' => $show,
            'ad' => $adcode,
        ]);
        \Phpcmf\Service::V()->display('home_show.html');exit;
    }

    public function callcode() {
        $id = (int)\Phpcmf\Service::L('input')->get('id');
        $data = \Phpcmf\Service::M()->table($this->init['table'])->get($id);

        $adstyle = \Phpcmf\Service::M('diygg', 'diygg')->adstyle;

        $code = "<link href=\"/static/assets/global/css/admin.min.css?v=".rand(1000,9999)."\" rel=\"stylesheet\" type=\"text/css\" />";
        $code .= "<textarea style=\"width:100%;\" rows=\"8\">";
        $code .= "<!--【".$data['adname']."】".$adstyle[$data['adstyle']]."广告-->".PHP_EOL;
        $code .= "<script type=\"text/javascript\" src=\"/index.php?s=diygg&c=home&m=index&id={$id}\"></script>".PHP_EOL;
        $code .= "<!--【".$data['adname']."】".$adstyle[$data['adstyle']]."广告-->";
        $code .= "</textarea>";
        $code .= "<p>* 复制上方代码到模板指定位置即可！</p>";

        return '<div style="padding:10px;">'.$code.'</div>';
    }

}
