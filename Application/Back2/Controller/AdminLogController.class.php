<?php

namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\AdminlogModel;
use Think\Controller;

class AdminlogController extends Controller
{
    public function demo(){//调用父类demo记录操作
        $adminid = 2;
        $console_id = 2;
        $action_name = 3;
        AdminlogModel::consloe_time($adminid,$console_id,$action_name);
    }
}