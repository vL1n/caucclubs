<?php

namespace app\Index\controller;

use app\index\controller\Index;
use think\Request;

class Details extends Index
{
    public function index()
    {
        return $this->fetch('/club-detail');
    }

}
