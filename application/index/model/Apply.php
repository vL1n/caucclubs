<?php

namespace app\index\model;

use think\Model;

class Apply extends Model
{
    public function Information()
    {
        return $this->belongsTo('Information','apply_for');
    }

}
