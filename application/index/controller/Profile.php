<?php

namespace app\Index\controller;

use app\index\model\Apply;
use app\index\model\Information;
use app\index\model\Message;
use app\index\model\User;
use think\App;
use app\index\controller\Index;
use think\Request;
use think\Session;

class Profile extends Index
{
    public function index()
    {

        $this->isLogin();
        $session = new Session();
        $name = $session->get('user_id');
        $map = ['username'=>$name];
        $user = User::get($map);
        $lastLogin = date('Y-m-d',$user->last_login);
        $this->view->assign('name',$name);

        //查询申请信息
        $map2 = ['user_id'=>$user->id];
        $club_application = Apply::where($map2)->select();

        $apply_for = [];

        $i=0;
        foreach ($club_application as $info){
            $apply_for[$i] = array($info->Information->name);
            $i++;
        }

        if(!is_null($user->adminClub)){
            $admin_club = $user->adminClub->name;
        }else{
            $admin_club = '';
        }


        if($user->sex == '0'){
            $sex = '女';
        }elseif ($user->sex == '1'){
            $sex = '男';
        }elseif ($user->sex =='2'){
            $sex = '未填写';
        }else{
            $sex = '未填写';
        }
        return $this->fetch('/profile',[
            'name'=>$name,
            'schoolid'=>$user->schoolid,
            'email'=>$user->email,
            'last_login'=>$lastLogin,
            'realname'=>$user->realname,
            'sex' => $sex,
            'age'=> $user->age,
            'phone' => $user->phone,
            'app' => $club_application,
            'apply_for' =>$apply_for,
            'admin_club' =>$admin_club
        ]);
    }

//    社团申请进度
    public function clubApplications(Request $request){

        $this->isLogin();

        //处理传递参数
        $info = $request->param();

        $clubId = $info['id']; //返回的id为社团id
        $map4 = ['apply_for'=>$clubId];
        $apply = Apply::where($map4)->find();
//        if(is_null($apply)){
//            $this->error('未查询到记录,可能原因为你已经成功取消申请','/index/Profile','','10');
//        }
        $appId = $apply->id;
        $applicationId = $appId; //请求的记录的id

        //获得请求记录信息
        $map3 = ['id'=>$applicationId];
        $application = Apply::get($map3);
        $club_id = $application->apply_for;

        //获得用户信息
        $session = new Session();
        $user_id = $session->get('user_id');
        $map = ['username'=>$user_id];
        $user = User::get($map);

        //根据用户信息查询表,有匹配即返回
        try{
            $map2 = ['user_id'=>$user->id,'apply_for'=>$clubId];
            $club_application = Apply::where($map2)->find();
            $apply_for = $club_application->Information->name;
        }catch(\Exception $e){
        $this->error('申请已被取消或不存在!','/index/Profile');
        exit();
        }

        return $this->fetch('/club-application',[
            'app' => $club_application,
            'apply_for' => $apply_for
        ]);

    }

    public function cancelApplication(Request $request){

        $this->isLogin();

        $status = 0;

        $info = $request->param();
        $club_id = $info['id'];

        //获得用户信息
        $session = new Session();
        $user_id = $session->get('user_id');
        $map = ['username'=>$user_id];
        $user = User::get($map);

        //获得社团信息
        $map2 = ['id'=>$club_id];
        $club = Information::get($map2);
        $club_name = $club->name;

        //删除数据库中的申请记录
        $key=['user_id'=> $user->id,'apply_for'=>$club_id];
        $delect = Apply::where($key)->delete();
        if($delect){


            //写入message中
            //准备写入数据
            $notification = array(
                'id'=>NULL,
                'type'=>'2',
                'made_time'=>time(),
                'title'=>'你取消了'.$club_name.'的社团申请',
                'message'=>'你已经成功取消了'.$club_name.'的社团申请！',
                'for_user'=>$user_id,
                'publisher'=>'系统'
            );

            $insert = Message::create($notification);

            $status = 1;
            $message = '取消申请成功!';
        }else{
            $status = 0;
            $message = '取消申请失败';
        }
        return ['status'=>$status,'message'=>$message];
    }

    public function pdf($data){
        $user_info=$data['user_info'];
        $cn_name=isset($user_info['user_info']['cn_name'])?$user_info['user_info']['cn_name']:"";
        $last_name=isset($user_info['user_info']['last_name'])?$user_info['user_info']['last_name']:"";
        $first_name=isset($user_info['user_info']['first_name'])?$user_info['user_info']['first_name']:"";
        $dob=isset($user_info['user_info']['dob'])?$user_info['user_info']['dob']:"";
        $gender=isset($user_info['user_info']['gender'])?$user_info['user_info']['gender']:"";
        $cn_address=isset($user_info['user_info']['cn_address'])?$user_info['user_info']['cn_address']:"";
        $cn_address_translation=isset($user_info['user_info']['cn_address_translation'])?$user_info['user_info']['cn_address_translation']:"";
        $au_address=isset($user_info['user_info']['au_address'])?$user_info['user_info']['au_address']:"";
        $email=isset($user_info['user_info']['email'])?$user_info['user_info']['email']:"";
        $country_code=isset($user_info['user_info']['country_code'])?'+'.$user_info['user_info']['country_code']:"";
        $phone=isset($user_info['user_info']['phone'])?$user_info['user_info']['phone']:"";

        $passport_no=isset($user_info['user_info']['passport_no'])?$user_info['user_info']['passport_no']:"";

        if($passport_no){
            $exp_date=isset($user_info['user_info']['exp_date'])?date('m-d-Y',strtotime($user_info['user_info']['exp_date'])):"";
        }else{
            $exp_date='';
        }
        $visa_subclass=isset($user_info['user_info']['visa_subclass'])?$user_info['user_info']['visa_subclass']:"";
        if($visa_subclass){
            $visa_ex_date=isset($user_info['user_info']['visa_ex_date'])?date('m-d-Y',strtotime($user_info['user_info']['visa_ex_date'])):"";
        }else{
            $visa_ex_date='';
        }
        $membership=isset($user_info['user_info']['membership'])?$user_info['user_info']['membership']:"";
        if($membership){
            $cover_end_date=isset($user_info['user_info']['cover_end_date'])?date('m-d-Y',strtotime($user_info['user_info']['cover_end_date'])):"";
        }else{
            $cover_end_date='';
        }

        if($data['user_info']['user_coe']){
            $coe_html='
            <div></div>
             <table border="1" cellpadding="10">
               <tr style="border: 1px solid #464747;height:25px;">
                    <td style="background-color:#fad3c7;width:155px">课程名</td>
                    <td  style="background-color:#fad3c7;width:145px">学校名</td>
                    <td style="background-color:#fad3c7;width:280px">时间</td>
                    <td  style="background-color:#fad3c7;width:100px">是否完成</td>
               </tr>
            ';
            $str='';
            foreach ($data['user_info']['user_coe'] as $key => $value) {
                $str.='<tr style="border: 1px solid #464747;height:25px;">';
                $str.='<td style="width:155px">'.$value['courses']['en_name'].'</td>';
                $str.='<td  style="width:145px">'.$value['school']['name'].'</td>';
                $str.='<td style="width:280px">'.date('d-m-Y',strtotime($value['course_start_date'])).' to '.date('d-m-Y',strtotime($value['course_end_date'])).'('.getMonthNum($value['course_start_date'],$value['course_end_date']).'months)</td>';

                if(strpos($value['status'],'"active"')){
                    $status='进行中';
                }else if(strpos($value['status'],'"expired"')){
                    $status='过期';
                }else if(strpos($value['status'],'"cancellation"')){
                    $status='取消';
                }else{
                    $status='取消';
                }
                $str.='<td  style="width:100px">'.$status.'</td>';
                $str.='</tr>';
            }
            $user_info_user_coe=$coe_html.$str.'</table>';
        }else{
            $user_info_user_coe='';
        }
        $data['gap']=collection($data['gap'])->toArray();
        if($data['gap']){
            $gap_html='<div></div>
          <table border="1" cellpadding="10">
           <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:200px">GAP 原因</td>
                <td  style="background-color:#fad3c7;width:200px">GAP 性质</td>
                <td style="background-color:#fad3c7;width:280px">时间</td>
           </tr>';
            $str='';
            foreach ($data['gap'] as $key => $value) {
                $str.='<tr style="border: 1px solid #464747;height:25px;">';
                $str.='<td style="width:200px">'.$value['cn_reason'].'</td>';
                $str.='<td  style="width:200px">'.str_replace("<", '&lt;', $value['en_reason']).'</td>';
                $str.='<td style="width:280px">'.$value['start'].' to '.$value['end'].'('.getMonthNum($value['start'],$value['end']).'months)</td>';
                $str.='</tr>';
            }
            $user_info_user_gap=$gap_html.$str.'</table>';
        }else{
            $user_info_user_gap='';
        }
        $html='
        <img src="'.$user_info['logo'].'" class="-logo" style="  display: block; width: 120px; height: auto;"/>
          <h3 style="font-size: 24px;width: 120px;color: #7d7d7d;letter-spacing: 2px;">万友教育</h3>
          <h5 style="font-size: 12px;margin: 4px 0 8px;width: 120px;-webkit-transform: scale(0.85);-ms-transform: scale(0.85);transform: scale(0.85);color: #babab9;">ONE U EDUCATION</h5>
          <h5 style="  font-weight: 700;color: #525252;font-size: 20px;margin-top: 10px;">'.$cn_name.' 同学申请表</h5>
          <p style=" margin-top: 8px;font-size: 16px;">*此表由万友科技自动生成，如有需求联系我们订购*</p>
          <table border="1" cellpadding="10">
           <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">LastName</td>
                <td  style="width:220px"><p style="font-size:16px">'.$last_name.'</p></td>
                <td style="background-color:#fad3c7;width:120px">FirstName</td>
                <td  style="width:220px"><p style="font-size:16px">'.$first_name.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">DOB</td>
                <td  style="width:220px"><p style="font-size:16px">'.$dob.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Gender</td>
                <td  style="width:220px"><p style="font-size:16px">'.$gender.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">Chinses address:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$cn_address.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Address Translate:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$cn_address_translation.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">Australia address:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$au_address.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Notionality:</td>
                <td  style="width:220px"><p style="font-size:16px">Chinese</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">Email</td>
                <td  style="width:220px"><p style="font-size:16px">'.$email.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Mobile</td>
                <td  style="width:220px"><p style="font-size:16px">'.$country_code.' '.$phone.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">Passport No:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$passport_no.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Expiry Date:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$exp_date.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">Visa Type:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$visa_subclass.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Expiry Date:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$visa_ex_date.'</p></td>
           </tr>
            <tr style="border: 1px solid #464747;height:25px;">
                <td style="background-color:#fad3c7;width:120px">OSHC Provider:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$membership.'</p></td>
                <td style="background-color:#fad3c7;width:120px">Expiry Date:</td>
                <td  style="width:220px"><p style="font-size:16px">'.$cover_end_date.'</p></td>
           </tr>
         </table>
        '.$user_info_user_coe.$user_info_user_gap.'
         <div style="display: flex;font-size: 14px; -webkit-box-align: center;align-items: center;">
          <p class="slogan">技术支持&nbsp;:</p>
           <p>万友教育</p>
           <p>ONE U Education</p>
          <img src="https://crm.oneuedu.com/public/static/wedu/img/logo.jpg" style="width: 100px;height: 100px">
         </div>
';
        pdf($html,$last_name.$first_name.' '.$dob.' One U Education信息表.pdf');
    }

}
