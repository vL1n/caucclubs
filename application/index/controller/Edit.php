<?php

namespace app\Index\controller;

use app\index\model\User;
use app\index\controller\Index;
use think\Request;
use think\Session;

class Edit extends Index
{
    public function index()
    {
        return $this->renderHtml();
    }
    public function renderHtml(){
        $session = new Session();
        $key = $session->get('user_id');
        $map = ['username'=>$key];
        $user = User::get($map);
        return $this->fetch('/edit',[
            'username'=>$user->username,
            'realname'=>$user->realname,
            'email'=>$user->email,
            'sex'=>$user->sex,
            'age'=>$user->age,
            'phone'=>$user->phone,
        ]);
    }
    public function editInfo(Request $request){

        $this->isLogin();
        $session = new Session();
        $loginName = $session->get('user_id');

        //获取请求信息
        $info = $request->param();

        //查询原始密码是否正确
        $map = ['username'=>$loginName];
        $user = User::get($map);
        if(md5($info['password-initial']) != $user->password){
            $status = 0;
            $message = '密码错误,请检查后重新输入!';
            return ['status'=> $status,'message'=> $message];
        }else{

            if($info['password-register'] != ''){
                $password = md5($info['password-register']);
            }elseif ($info['password-register'] == ''){
                $password = $user->password;
            }
            $dataSet =array(
                'id' => $user->id,
                'username' => $info['username'],
                'realname' => $info['realname'],
                'password' => $password,
                'email' => $info['email'],
                'sex' => $info['sex'],
                'age' => $info['age'],
                'phone' => $info['phone']
            );
            //更新数据库
            $update = User::update($dataSet);
            //判断更新是否成功
            if($update){
                $status = 1;
                $message = '修改成功';
            }else{
                $status = 0;
                $message = '修改失败，请重试!';
            }
            return ['status'=> $status,'message'=> $message];
        }
    }
}
