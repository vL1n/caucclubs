<?php

namespace app\Index\controller;

use app\index\model\Apply;
use app\index\model\Information;
use app\index\model\User;
use app\index\controller\Index;
use think\Request;

class Home extends Index
{
    public function index()
    {

        //查询所有社团
        $clubs = Information::where('id','<>','0')->select();
        $count_user = User::count('id','<>','0');
        $count_application = Apply::count('id','<>','0');
        $count_club = count($clubs);

        //渲染模板
        return $this->fetch('/index',[
            'clubs'=>$clubs,
            'count_club'=>$count_club,
            'count_user'=>$count_user,
            'count_application'=>$count_application,
        ]);
    }

}
