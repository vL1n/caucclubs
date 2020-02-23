<?php


namespace app\admin\validate;


use think\Validate;

class UserValidate extends Validate
{
    protected $rule =   [
        'username'      => 'require',
        'realname'      => 'require',
        'password'      => 'require',
        'schoolid'      => 'require',
        'email'         => 'require',
        'status'        => 'require',
        'phone'         => 'require',
        'sex'           => 'require'
    ];

    protected $message  =   [
        'username.require'      => '用户名不能为空',
        'password.require'      => '密码不能为空',
        'realname.require'      => '真实姓名不能为空',
        'status.require'        => '状态不能为空',
        'schoolid.require'      => '学号不能为空',
        'email.require'         => '邮箱地址不能为空',
        'phone.require'         => '联系方式不能为空',
        'sex.require'           => '性别不能为空'
    ];

    protected $scene = [
        'edit'  =>  ['username', 'realname', 'status','schoolid','email','phone','sex']
    ];

}