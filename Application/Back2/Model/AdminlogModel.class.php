<?php

namespace Back2\Model;

use Think\Model;

class AdminlogModel extends Model
{
    //获取管理员id
    static public function getAdminId(){
        $admin_name = I('admin_name')?:'';//管理员的ID
        $admin_info = M('admin')->field('admin_id')->where("admin_name = '{$admin_name}'")->find();
        $admin_id = $admin_info['admin_id'];
        return $admin_id;
    }

    //这只是个demo
    static public function demo(){//调用父类demo记录操作
        $adminid = 2;
        $console_id = 2;
        $action_name = 3;
        AdminlogModel::addAdminLog($adminid,$console_id,$action_name);
    }

    /**
     * 记录管理员操作日志
     * @param $adminid = 管理员ID
     * @param $console_id = 相关审核表的主键
     * @param $action_name = 具体操作是啥，int
     */
    static public function addAdminLog($adminid,$console_id,$action_name){
        $ins['admin_id'] = $adminid;
        $ins['console_id'] = $console_id;
        $ins['action_name'] = $action_name;
        $ins['createtime'] = time();
        $ins['action_ip'] = ip2long($_SERVER['HTTP_X_REAL_IP']);
        $model = M('adminlog');
        $res = $model->data($ins)->add();
        if (!$res){
            die();
        }
        return $res;
    }
}
