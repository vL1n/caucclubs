<?php

namespace app\admin\controller;

use app\admin\model\Setting;
use think\Controller;
use think\Request;

class Front extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function saveSettings(){

    }

    public static function getSetting($setting){

        $front = new Setting();
        $res = $front->getSettings($setting);
        if($res['code'] == 0){
            return $res['data'];
        }

    }

}