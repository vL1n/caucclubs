<?php

namespace app\Index\controller;

use app\index\model\Apply;
use app\index\model\Department;
use app\index\model\Information;
use app\index\controller\Index;
use app\index\model\Message;
use app\index\model\User;
use think\Request;
use think\Session;

class Signup extends Index
{
    public function index()
    {
        return $this->fetch('/signup');
    }

    public function signUpForClub(Request $request){

        //判断是否登陆
        $this->isLogin();

        //判断信息是否已经完善
        $this->checkInformation();

        //获得请求参数
        $info = $request->param();

        //生成查询键
        $map = ['id'=>$info['id']];

        //判断是否已经注册该社团
        //使用session判断申请人
        $session = new Session();
        $username = $session->get('user_id');
        $map3 = ['username'=>$username];
        $user = User::get($map3);
        $user_id = $user->id;

        //判断是否已经提交过申请
        $map2 = ['user_id'=>$user_id];
        $applyed_user_id = Apply::where($map2)->select();//申请信息中的用户存在本次申请的用户
        foreach ($applyed_user_id as $apply){
            if($apply->apply_for == $info['id']){
                return $this->error('你已经申请过本社团,请勿重复申请哦~','/index/profile'); //已经注册
            }
        }


        //在库中查询社团信息
        $club = Information::get($map);
        //根据查询到的社团信息查询社团的部门信息
        $map2 = ['of_club'=>$club->id];
        $department = Department::all($map2);
        $department_size = sizeof($department);


        //渲染模板
        return $this->fetch('/signup',[
            'club'=>$club,
            'department' =>$department,
            'department_ornot' => $department_size
        ]);
    }

    public function signUp(Request $request){

        $this->isLogin();

        //初始化返回信息
        $status = 0;
        $message = '请重试';

        //使用session判断申请人
        $session = new Session();
        $username = $session->get('user_id');
        $map = ['username'=>$username];
        $user = User::get($map);
        $user_id = $user->id;

        //获取传递参数
        $info = $request->param();


        //配置注册信息
        $apply_for = $info['club_id'];  //目标社团id
        $college = $info['college'];  //所在学院
        $major = $info['major'];  //所在专业
        $grade = $info['grade'];  //当前绩点
        $department = $info['department'];  //目标部门
        $self_introduction = $info['self_introduction'];  //自我介绍
        $join_reason = $info['join_reason'];  //加入原因
        $apply_date = time();  //申请时间戳

        //检查必填项是否留空
        //目标社团id不能留空，所在学院与所在专业不能留空
        if(!empty($apply_for)&&!empty($college)&&!empty($major)){

            //判断是否已经提交过申请


            //满足存在本次用户与本次社团都已申请过,即为重复提交
            $map2 = ['user_id'=>$user_id];
            $applyed_user_id = Apply::where($map2)->select();//申请信息中的用户存在本次申请的用户
            $key = 0;
            foreach ($applyed_user_id as $apply){
                if($apply->apply_for == $apply_for){
                    $key = 1; //已经注册
                }
            }
            if($key == 1){
                return $this->error('你已经提交过申请,请勿重复提交!','/index/profile');
            }
            else{//未提交过申请

                //准备储蓄数据
                $data = array(
                    'id'=>NULL,
                    'user_id'=>$user_id,
                    'apply_for'=>$apply_for,
                    'last_opera'=>$apply_date,
                    'apply_date'=>$apply_date,
                    'status'=>'1',
                    'join_reason'=>$join_reason,
                    'intention'=>$department,
                    'self_introduction'=>$self_introduction,
                    'grade'=>$grade,
                    'college' => $college,
                    'major'=>$major,
                    'status1_time'=>time()
                );

                //插入数据
                $insert = Apply::create($data);
                if($insert){

                    //将申请成功的信息存入个人信箱

                    $club = Information::get(['id'=>$apply_for]);
                    $club_name = $club->name;
                    //准备存入信息
                    $notification = array(
                        'id'=>NULL,
                        'type'=>'2',
                        'made_time'=>time(),
                        'title'=>$club_name.'--'.'社团注册申请成功!',
                        'message'=>'你已经成功提交加入'.$club_name.'的申请!请留意申请进度！',
                        'message_preview'=>'你已经成功提交加入'.$club_name.'的申请!',
                        'for_user'=>$user_id,
                        'publisher'=>'系统'
                    );

                    $insert2 = Message::create($notification);
                    if($insert2){
                        return $this->success('申请成功，请留意信息!','/');
                    }


                }else{

                    return $this->error('申请失败，请重试!','/index/Clubs');
                }

            }
        }else{
            $status =0;
            $message = '必填项不能为空!';
            return $this->error('必填项不能为空!','/index/Clubs');
        }
//        return ['status'=> $status,'message'=> $message];
    }

    public function isSignUp(Request $request){

        //处理传递参数
        $info = $request->param();
        $club_id = $info['id'];

        //使用session判断申请人
        $session = new Session();
        $username = $session->get('user_id');
        $map = ['username'=>$username];
        $user = User::get($map);
        $user_id = $user->id;

        //判断是否已经提交过申请
        $map2 = ['user_id'=>$user_id];
//        $map3 = ['apply_for'=>$club_id];
        $applyed_user_id = Apply::where($map2)->select();//申请信息中的用户存在本次申请的用户
        $res = 0;
        foreach ($applyed_user_id as $apply){
            if($apply->apply_for == $club_id){
                $res = 1; //已经注册
            }
        }
        return $res;

    }
}
