<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
    public function adminClub(){

        return $this->belongsTo('Information','admin_club');
    }

    /**
     * @param $limit
     * @param $where
     * @return array
     */
    public function getUsers($limit,$where){

        try {
            $res = $this->where($where)->paginate($limit);
        }
        catch (\Exception $e){

            return modelReMsg(-1,'',$e->getMessage());
        }

        return modelReMsg(0,$res,'ok');
    }

    /**
     * @param $user
     * @return array
     */
    public function addUser($user){

        try {
            $has = $this->where('username', $user['username'])->findOrEmpty()->toArray();
            $has2 = $this->where('schoolid',$user['schoolid'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '用户名已经存在');
            }elseif (!empty($has2)){
                return modelReMsg(-2,'','该学号已经被注册');
            }

            $this->insert($user);
        }
        catch (\Exception $e){
            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '添加用户成功');
    }

    public function checkPassword($id,$password){

        try {
            $has = $this->where('id',$id)->value('password');
            if($password == $has){
                return modelReMsg(2,'','');
            }
        }
        catch (\Exception $e){
            return modelReMsg(1,'','');
        }
        return modelReMsg(0,'','');
    }

    public function editUser($user){

        try {

            $this->where('id',$user['id'])->update($user);
        }
        catch (\Exception $e){
            return modelReMsg(1,'',$e->getMessage());
        }
        return modelReMsg(0,'','');
    }

    public function getUserById($id){

        try {
            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }

    public function delUser($userId){

        try {
            if (1 == $userId) {
                return modelReMsg(-2, '', '超级管理员不可删除');
            }

            $this->where('id', $userId)->delete();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '删除成功');
    }
}
