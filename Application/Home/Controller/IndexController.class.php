<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('演库时代---演绎未来的通路！');
    }

    public function login(){
    	$this->display();

    }

    public function dologin(){
        $userstatus = I('post.userstatus');
        if($userstatus =='2'){
            $this -> error('当前用户已被禁用，请联系管理员处理!', __URL__ . '/login/');
        }

        $username = I('post.username');

        $admin = D('Admin')->where("admin_name='".$username."'")->find();

        

    }

    public function verify() 
    {
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,'png',130,50);
    }
}