<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/2/28
 * Time: 8:24 PM
 */
namespace app\admin\controller;

use think\Controller;
use tool\Auth;

class Base extends Controller
{
    // 定义全局变量
    public $website = '';
    public $main_title = '';
    public $sub_title = '';
    public $admin_main_title = '';
    public $admin_sub_title = '';
    public $icon_path = '';
    public $logo_path = '';

    public function __construct()
    {
        parent::__construct();
        $this->website = Front::getSetting('website');
        $this->main_title = Front::getSetting('main_title');
        $this->sub_title = Front::getSetting('sub_title');
        $this->admin_main_title = Front::getSetting('admin_main_title');
        $this->admin_sub_title = Front::getSetting('admin_sub_title');
        $this->icon_path = Front::getSetting('icon_path');
        $this->logo_path = Front::getSetting('logo_path');
        $this->assign([
            'website'=>$this->website,
            'main_title'=>$this->main_title,
            'sub_title'=>$this->sub_title,
            'admin_main_title'=>$this->admin_main_title,
            'admin_sub_title'=>$this->admin_sub_title,
            'icon_path'=>$this->icon_path,
            'logo_path'=>$this->logo_path
        ]);
    }

    public function initialize()
    {
        if(empty(session('admin_user_name'))){

            $this->redirect(url('login/index'));
        }

        $controller = lcfirst(request()->controller());
        $action = request()->action();
        $checkInput = $controller . '/' . $action;

        $authModel = Auth::instance();
        $skipMap = $authModel->getSkipAuthMap();

        if (!isset($skipMap[$checkInput])) {

            $flag = $authModel->authCheck($checkInput, session('admin_role_id'));

            if (!$flag) {
                if (request()->isAjax()) {
                    return json(reMsg(-403, '', '无操作权限'));
                } else {
                    $this->error('无操作权限');
                }
            }
        }

        $this->assign([
            'admin_name' => session('admin_user_name'),
            'admin_id' => session('admin_user_id')
        ]);
    }
}