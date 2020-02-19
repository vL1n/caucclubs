<?php

namespace app\Index\controller;

use app\index\model\Information;
use app\index\model\Foundation;
use app\index\model\Message;
use app\index\model\User;
use app\index\controller\Index;
use think\Request;
use think\Session;

class Clubsignup extends Index
{
    public function index()
    {
        $this->isLogin();
        $ses = new Session();
        $map = ['username'=>$ses->get('user_id')];
        $user = User::get($map);
        if(is_null($user->realname)||is_null($user->schoolid)||is_null($user->email)){
            $this->error('请先完善你的个人资料再进行负责人认证!','/index/profile');
        }
        else{
            return $this->fetch('/club-signup',[
                'username'=>$user->username,
                'schoolid'=>$user->schoolid,
                'realname'=>$user->realname,
            ]);
        }
    }

    public function clubSignUp(Request $request){

        $this->isLogin();
        $status = 0;

        //使用session获得当前申请者ID
        $ses = new Session();
        //根据ID获得申请者信息
        $map = ['username'=>$ses->get('user_id')];
        $user = User::get($map);

        //获得请求传递的参数
        $info =$request->param();
        $key = ['name'=>$info['clubname']];
        $key2 = ['clubname'=>$info['clubname']];

        //验证申请者密码
        if(md5($info['password']) != $user->password){
            $status = 0;
            $message = '密码错误，请重试!';
            return ['status'=>$status,'message'=>$message];
        }

        //根据参数在社团信息表和社团负责人认证申请表中查询是否已存在
        $club_info = Information::get($key);
        $club_setup = Foundation::get($key2);
        if($club_info){
            $status = 0;
            $message = '该社团已经存在,请联系社团的负责人!';
            return ['status'=>$status,'message'=>$message];
        }elseif ($club_setup){
            $status = 0;
            $message = '已有该社团的负责人认证申请记录，请勿重复提交!';
            return ['status'=>$status,'message'=>$message];
        }

        //处理申请信息
        $data = array(
            'id'=>NULL,
            'applyuser' =>$user->username,
            'phone' => $info['phone'],
            'clubname' => $info['clubname'],
            'comments' => $info['comments'],
        );

        //提交信息
        $insert = Foundation::create($data);

        //查看是否成功
        if($insert){

            //发送系统信息给申请者
            $sent = $this->infoApplicant($user->id);
            //发送成功
            if($sent){
                $status= 1;
                $message = '申请提交成功，请等待管理员验证，请留意信箱!';
                return ['status'=>$status,'message'=>$message];
            }else{ //发送失败
                $status = 0;
                $message = '申请提交失败，请重试!';
                return ['status'=>$status,'message'=>$message];
            }


        }else{
            $status = 0;
            $message = '申请提交失败，请重试!';
            return ['status'=>$status,'message'=>$message];
        }

    }

    //发送系统信息给申请者
    public function infoApplicant($id){


        //准备信息
        $type = '2'; // 1：社团通知  2：系统通知
        $time = time();
        $title = '管理员中心回复';
        $message = '管理员已经收到了你的负责人认证申请，并且将会在2小时内处理该申请，请耐心等待';
        $soOn = '...';
        $cutMessage = substr($message, 0,20);
        $messagePreview = $cutMessage.$soOn;
        $publisher = '系统';
        $userid = $id;

        //插入信息
        $data = array(
            'id'=>NULL,
            'type'=> $type,
            'made_time'=>$time,
            'title'=> $title,
            'message'=> $message,
            'message_preview' => $messagePreview,
            'for_user' => $userid,
            'publisher' => $publisher,
        );

        $insert = Message::create($data);
        if ($insert){
            return 1;
        }else{
            return 0;
        }
    }
}