<?php

namespace app\Index\controller;

use app\index\controller\Index;
use app\index\model\User;
use think\Request;
use think\Session;

class Login extends Index
{
    public function index()
    {
        $this->alreadyLogin();
        return $this->fetch('/login');
    }

    public function check(Request $request){
        //设置status
        $status = 0;

        //获取表单提交的数据，并保存在变量
        $data = $request -> param();
        //$email = $data['emailaddress'];
        $username = $data['username'];
        $password = md5($data['password']);

        //在user表中查询
        $map = ['username'=> $username];
        $user = User::get($map);

        //将用户名与密码分开验证

        //如果未查询到该用户
        if(is_null($user)){

            //设置返回信息
            $message = '输入信息不正确!';
        }elseif ($user->password != $password) {

            //设置密码不正确的提示信息
            $message = '密码错误';
        }elseif($user->status == '0'){

            //账号已经被停用
            $message = '此账号已被停用';
        }else{

            //如果用户名密码都通过验证了，说明是合法用户
            //设置返回信息
            $status =1;
            $message = '验证通过，点击确定跳转...';

            //更新表里数据
            $user -> setInc('login_count');
            $user ->save(['last_login'=>time()]);

            //将用户登录信息保存到session中
            $sessionInUsing = new Session();
            $sessionInUsing->set('user_id',$username);
            $sessionInUsing->set('user_info',$data);
            //Session::set('user_id',$username);
            //Session::set('user_info',$data);


        }
        return ['status'=> $status, 'message'=> $message];

    }

    public function logout(){

        //删除session值
        $sessionInUsing = new Session();
        $sessionInUsing->delete('user_id');
        $sessionInUsing->delete('user_info');
        //Session::delete('user_id');
        //Session::delete('user_info');

        //成功，返回界面
        $this->success('注销成功，正在返回...','../index/home');
    }

    public function getUserName(){

        $sessionInUsing = new Session();
        $uname = $sessionInUsing->get('user_id');
        // Session::get('user_id');
        return $uname;
    }

    public function forgetPwd(){


    }
}
