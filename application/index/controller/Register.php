<?php

namespace app\Index\controller;

use app\index\model\User;
use app\index\controller\Index;
use think\Request;
use think\Session;

class Register extends Index
{
    public function index()
    {
        //$this->alreadyLogin();
        return $this->fetch('/register');
    }

    public function register(Request $request){
        $ses = new Session();
        define('USER_ID',$ses->get('user_id'));
        if (!is_null(USER_ID)){
            $this-> error('你已经拥有账号并且登陆了，请不要重复注册哦~','/index');
        }
        else{
            $status = 0;
            $info = $request->param();


            $uname = $info['username'];// 用户名
            $upwd = md5($info['password-register']);// 密码
            $uemail = $info['emailaddress-register']; // 邮箱
            //$usex = $info['sex'];//性别
            $uschoolId = $info['schoolid']; //学号

            if(!empty($uname)&&!empty($upwd)&&!empty($uemail)&&!empty($uschoolId)){
                // 判断该用户是否已经注册

                $map = ['schoolid'=>$uschoolId];
                $user = User::get($map);

                //如果存在学号,回退界面
                if($user){
                    $status = 1;
                    $message = '该学号已被注册，请直接登陆';
                    return ['status'=> $status,'message'=> $message];
                }
                else{
                    // 注册
                    $data=array('id'=>NULL,'username'=>$uname,'password'=>$upwd,'email'=>$uemail,'schoolid'=>$uschoolId);

                    $insert = User::create($data);
                    // 如果注册成功
                    if($insert){
                        $status = 1;
                        $message = '注册成功，点击确定跳转登陆页面~~';
                        // 如果注册失败
                    }else{
                        $status = 0;
                        $message = '注册失败，请重试!';

                    }
                }
            }
            else{
                $status = 0;
                $message = '必填项不能为空!';
            }
            return ['status'=> $status,'message'=> $message];
        }
    }
}
