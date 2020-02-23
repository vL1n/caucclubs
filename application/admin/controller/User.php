<?php

namespace app\admin\controller;

use app\admin\validate\UserValidate;
use think\Controller;
use think\Request;
use tool\Log;

class User extends Base
{
    /**
     * @param $limit 为分页数据条数
     * @param $where 为查询条件
     * @return mixed|\think\response\Json
     */
    public function index()
    {
        if(request()->isAjax()){
            //分页，每页几条数据
            $limit = input('param.limit');
            $username = input('param.username');
            $schoolid = input('param.schoolid');

            //定义一个查询条件，默认为空，故默认情况下显示所有用户数据
            $where =[];
            //当用户名搜索框不为空时,渲染用户名搜索结果
            if(!empty($username)){
                $where[] = ['username', 'like', $username . '%'];
            };

            //当学号搜索不为空时，渲染学号搜索结果
            if(!empty($schoolid)){
                $where[] = ['schoolid', 'like', $schoolid . '%'];
            };

            //new 一个对象直通userModal
            $user = new \app\index\model\User();

            //返回的列表为
            $list = $user->getUsers($limit,$where);

            if($list['code'] == 0){
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);

        }
        return $this->fetch();
    }

    public function addUser(){

        if (request()->isPost()){
            $param = input('post.');

            //验证输入的内容是否有误
            $validate = new UserValidate();
            if(!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            //处理密码
            $param['password'] = makePassword($param['password']);

            //准备数据库
            $User = new \app\index\model\User();
            $insertInfo = [
                'id'            =>NULL,
                'username'      =>$param['username'],
                'password'      =>$param['password'],
                'realname'      =>$param['realname'],
                'status'        =>$param['status'],
                'phone'         =>$param['phone'],
                'sex'           =>$param['sex'],
                'email'         =>$param['email'],
                'schoolid'      =>$param['schoolid'],
                'is_admin'      =>0,
                'operate_club'  =>0
            ];

            $res = $User->addUser($insertInfo);
            Log::write("添加用户：" . $param['username']);

            return json($res);

        }

        return $this->fetch('add');
    }

    public function editUser(){

    }

    public function deleteUser(){

    }
}
