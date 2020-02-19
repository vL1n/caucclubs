<?php

namespace app\Index\controller;

use app\index\model\Information;
use app\index\controller\Index;
use think\Request;

class Clubs extends Index
{
    public function index()
    {
        return $this->clubList();
    }

    public function clubList(){

        //查询所有社团
        $clubs = Information::where('id','<>','0')->paginate(3);
        $clubs_tag = Information::where('id','<>','0')->select();

//        print_r($clubs);
        return $this->fetch('/club-list',[
            'clubs' => $clubs,
            'clubs_tag' =>$clubs_tag
        ]);
    }

    public function getClubsList(){

        //获取所有社团数据
        $clubsList = Information::all();
        $data = $clubsList;

        //返回json数据
        return $data;
    }

    public function renderClubDetails(Request $request){

        //获得页面请求的信息
        $info = $request->param();

        //取得信息的id
        $id = ['id'=>$info['id']];

        //从数据库中根据id取回数据
        $club = Information::get($id);
        //渲染模板
        return $this->fetch('/club-detail',[
            'club' =>$club
        ]);

    }

    public function pageSearch(Request $request){

        //获得页面请求的信息
        $info = $request->param();

        //取得信息的id
        $name = ['name'=>$info['search']];

        $condition = '%'.$info['search'].'%';

        //从数据库查找信息
        $clubs = Information::where('name','like',$condition)->paginate(3);

        $res = json_encode($clubs);
        $check = $res[9];
        //如果查询到
        if($check){

            $status = 1;
            return ['status'=>$status,'clubs'=>$clubs];
        }else{// 未查询到

            $status = 0;
            return ['status'=>$status,'clubs'=>$clubs];
        }


    }
}
