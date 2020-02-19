<?php

namespace app\Index\controller;

use app\index\model\Message;
use app\index\model\User;
use app\index\controller\Index;
use think\Request;
use think\Session;

class Messages extends Index
{
    public function index()
    {
        $this->isLogin();
        return $this->renderHtml();
    }

    public function renderHtml(){
        //获得使用者姓名
        $session = new Session();
        $user_id =$session->get('user_id');

        //获得使用者详细信息
        $map = ['username'=>$user_id];
        $user = User::get($map);
        $id = $user->id;
        $map = ['for_user'=>$id];

        //获得使用者收到的全部信息
        $message =  Message::where($map)->order('made_time desc')->limit(1)->paginate(3);

        //将信息赋值再返回模板
        return $this->fetch('/message',[
            'message' => $message,
        ]);
    }

    public function messages(Request $request){

        //获得页面请求的信息
        $info = $request->param();
        //取得信息的id
        $id = $info['id'];

        //找到该信息并通过赋值返回模板
        $message = Message::get($id);
        return $this->fetch('/message-detail',[
            'message'=>$message,
        ]);
    }

    public static function insertMessages($data = null,$with =[],$cache= false){


    }
}
