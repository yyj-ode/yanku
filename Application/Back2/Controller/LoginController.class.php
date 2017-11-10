<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 14:37
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Think\Controller;

class LoginController extends FatherController
{
    public function login(){
        $this->display();
    }

    public function doLogin(){
        $account = I('user');
        $password = I('pwd');
        $ifseccess = M('admin')->field("admin_id,password")->where("admin_name = '{$account}'")->find();
        $arr = [];

        if(md5($password)!=$ifseccess['password']){
            $arr['status'] = 0;
        }else{
            $this->admin_login($ifseccess['admin_id']);
            $redis = new \Redis();
            $redis ->connect("localhost",6379); //localhost也可以填你服务器的ip
            $redis->select(1);
            $time = time();
            $redis->setex($account.'admin_key',3600,md5($time));
            $arr['status'] = 1;
            $arr['admin_key'] =  $time;
            $arr['user'] =  $account;
        }
        echo json_encode($arr);
    }
    public function demo(){//调用父类demo记录操作
        $adminid = 2;
        $console_id = 2;
        $action_name = 3;
        $this->consloe_time($adminid,$console_id,$action_name);
    }
}