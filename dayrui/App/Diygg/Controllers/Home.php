<?php namespace Phpcmf\Controllers;

class Home extends \Phpcmf\App
{

    public function index() {
        $id = (int)\Phpcmf\Service::L('input')->get('id');
        $data = \Phpcmf\Service::M()->table('diygg')->get($id);

        $code = \Phpcmf\Service::M('diygg', 'diygg')->generate_ad($data);

        return "<!--\r\ndocument.write(\"".$code."\");\r\n-->\r\n";

    }

}
