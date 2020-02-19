<?php
namespace app\index\controller;

use app\index\model\User;
use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        return $this->redirect('/Index/Home');
    }


    //登陆细节
    //如果没登陆，则必须登陆
    protected function isLogin(){
        $ses = new Session();
        define('USER_ID',$ses->get('user_id'));
        //如果登陆常量为NULL,没登陆
        if (is_null(USER_ID)){
            $this-> error('未登录，请先登录！','/index/Login');
        }
    }

    //如果已经登陆，则不能登陆
    protected function alreadyLogin(){
        $ses = new Session();
        define('USER_ID',$ses->get('user_id'));
        //如果登陆常量为NULL,没登陆
        if (!is_null(USER_ID)){
            $this-> error('已经登陆，请不要重复操作！','/index');
        }
    }

    //查看是否已经完善信息
    protected function checkInformation(){
        $session = new Session();
        $username = $session->get('user_id');

        //表中查询信息是否完善
        $map =['username'=>$username];
        $user = User::get($map);
        if(is_null($user->realname)
            ||is_null($user->email)
            ||is_null($user->sex)
            ||is_null($user->age)
            ||is_null($user->schoolid)
            ||is_null($user->phone)
        ){
            return $this->error('信息未完善,无法继续操作,请先完善信息!','/index/Profile','','5');
        }

    }

    //查看当前状态
    public static function checkStatus(){
        $status = 0;
        $ses = new Session();
        define('USER_ID',$ses->get('user_id'));
        if (!is_null(USER_ID)){
            $status = 1;
        }
        return $status;
    }


    //渲染模板
    public function tplTest(Request $request){

        $info = $request->param();
        $type = $info['type'];
        if($type == 's'){
            return $this->success('测试-信息','/index','','500');
        }elseif ($type == 'e'){
            return $this->error('测试-信息','/index','','500');
        }else{
            return $this->redirect('/');
        }
    }

    public function testPython(){

        //测试python程序接口
        $dep = 'NNG';
        $arr = 'PEK';
        $date = '2020-1-12';
        unset($out);
        $output = shell_exec('C:\Program Files (x86)\Python\python.exe D:\Working files\Python_work\climbTrip\phpConnector.py');
        var_dump($output);
        echo '<br>';
    }


}
