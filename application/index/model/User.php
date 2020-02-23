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
            if(!empty($has)) {
                return modelReMsg(-2, '', '用户名已经存在');
            }
        }
        catch (\Exception $e){
            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '添加用户成功');
    }
}
