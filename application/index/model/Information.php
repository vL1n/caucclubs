<?php

namespace app\index\model;

use think\Model;

class Information extends Model
{
    public function adminUser(){

        return $this->belongsTo('User','adminid');
    }
}
