<?php

namespace app\admin\model;

use think\Exception;
use think\Model;

class Setting extends Model
{
    protected $table = 'cauc_setting';

    public function getSettings($setting){

        try {
            $settings = $this->where('id','<>',0)->value($setting);
        }
        catch (\Exception $e){

            return modelReMsg(-1,'',$e->getMessage());
        }

        return modelReMsg(0,$settings,'ok');
    }
}
