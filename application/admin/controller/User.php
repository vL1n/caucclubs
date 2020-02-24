<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\validate\AdminValidate;
use app\admin\validate\UserValidate;
use think\Controller;
use think\Db;
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

    /**
     * @return array|mixed|\think\response\Json
     */
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
                'age'           =>$param['age'],
                'email'         =>$param['email'],
                'schoolid'      =>$param['schoolid'],
                'is_admin'      =>0,
                'operate_club'  =>0
            ];

            //写入数据库
            $res = $User->addUser($insertInfo);

            //写入log
            Log::write("添加用户：" . $param['username']);

            return json($res);

        }

        return $this->fetch('add');
    }

    public function editUser(){
        if(request()->isPost()){

            // 初始化参数
            $param = input('post.');

            // 初始化验证器
            $validate = new UserValidate();

            // 验证传入参数
            if(!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' =>$validate->getError()];
            }

            // 判断是否已经修改过密码
            $userid = $param['id'];
            $user = new \app\index\model\User();
            $res = $user->checkPassword($userid,$param['password']);

            $password = '';
            // 0为已更改密码，2为未更改，1为出错
            if($res['code'] == 0){
                $password = makePassword($param['password']);
            }elseif ($res['code'] == 2){
                $password = $param['password'];
            }

            // 准备存入数据
            $updateInfo = [
                'id'            =>$param['id'],
                'username'      =>$param['username'],
                'password'      =>$password,
                'realname'      =>$param['realname'],
                'status'        =>$param['status'],
                'phone'         =>$param['phone'],
                'sex'           =>$param['sex'],
                'age'           =>$param['age'],
                'email'         =>$param['email'],
                'schoolid'      =>$param['schoolid'],
            ];

            //写入数据库
            $res = $user->editUser($updateInfo);

            //写入log
            Log::write("编辑用户：" . $param['username']);

            return json($res);
        }

        $userid = input('param.id');;
        $user = new \app\index\model\User();
        $this->assign([
            'user' => $user->getUserById($userid)['data']
        ]);

        return $this->fetch('edit');
    }

    public function delUser(){

        if(request()->isAjax()) {

            $userId = input('param.id');

            $user = new \app\index\model\User();
            $res = $user->delUser($userId);

            Log::write("删除用户：" . $userId);

            return json($res);
        }
    }
}
