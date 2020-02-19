<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
    public function adminClub(){

        return $this->belongsTo('Information','admin_club');
    }
}
