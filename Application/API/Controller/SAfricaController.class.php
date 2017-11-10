<?php
/**
 * Created by PhpStorm.
 * User: chairmanwong
 * Date: 2017/6/13
 * Time: 下午2:45
 */

namespace API\Controller;

use API\Model\UserModel;
use Think\Controller;
header('Access-Control-Allow-Origin:*');


class SAfricaController extends ResController
{
    public function sendsms(){
        $mobile = I('post.mobile');
        $username = "bjykwlkj"; //用户名
        $pwd = "142121"; //密码
        $data = send_sms($username,$pwd,$mobile);
//        echo $data;
    }
    public function register(){
        $mobile = I('post.mobile');
        $con['mobile'] = $mobile;
        $password = I('post.pwd');
        $code = I('post.code');
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(5);
        $re_code = $redis->get($mobile);
        if ($code == $re_code){
            $redis->del($mobile);
            $model_user = M('user');
            $login = $model_user->field('nickname')->where($con)->find();
            if(!empty($login)){
                $this->json_rest(10);
                die();
            }else{
                $model = new UserModel();
                $con['password_salt'] = $model->mkSalt();
                $con['password'] = $model->mkPassword($password);
                $con['nickname'] = $con['mobile'];
                $insert = $model_user->add($con);
                $data['user_id'] = $insert;
                $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA','client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4','org_name'=>'1134170411178481','app_name'=>'yanku');
                $token = new HXController($options);
                $result = $token->createUser("$insert",'yanku321');
                empty($result)?$this->ajaxReturn(array('result'=>"2",'message'=>"注册失败"),'JSON'):$this->ajaxReturn(array('result'=>"1",'message'=>"注册成功"),'JSON');;
            }
        }else{
            $this->ajaxReturn(array('result'=>"0",'message'=>"验证失败"),'JSON');
            die();
        }

    }
    public function login(){
        $mobile = I('post.mobile');
        $password = I('post.pwd');
        if (preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            $model_user = D('user');
            // 利用用户检索记录
            $cond['mobile'] = $mobile;
            $login = $model_user->where($cond)->find();
            if ($login && sha1($login['password_salt'] . $password) === $login['password']) {
                $user = M('user');
                $user = $user->field('user_id,nickname,user_img')->where("mobile=$mobile")->find();
                $token = substr(md5(time()), 13);
                session($user["user_id"],$token);
                $user = $this->img_url($user,"user_img");
                $data = array('result'=>"1",'data'=>$user);
                $this->ajaxReturn($data,'JSON');
            } else {
                $this->ajaxReturn(array('result'=>"11"),'JSON');
                die();
            }
        } else {
            $this->ajaxReturn(array('result'=>"11"),'JSON');
            die();
        }
    }
    public function comment(){
          $start = I('post.start',0);
        if ($start==0){
            $start = $start*10;
        }else{
            $start = $start*10+1;
        }
	    $com = M('comments');
        $data['info'] = $com->field('count(yk_comments.id) as count,floor(count(yk_comments.id)/10) as page')->find();
        $data['comment'] = $com->field('yk_user.user_id,yk_user.nickname,yk_user.user_img,yk_comments.id,yk_comments.content')
                    ->join("LEFT JOIN yk_user ON yk_comments.user_id=yk_user.user_id")
                    ->limit($start,10)
                    ->select();
        $data['comment'] = $this->img_urls($data['comment'],'user_img');
        if ($data){
            $this->ajaxReturn(array('result'=>"1",'data'=>$data),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0",'data'=>$data),'JSON');
        }
    }
    public function addcom(){
        $con['user_id']=I('post.user_id');
        $con['content'] = I('post.content');
        $com = M('comments');
        $com = $com->data($con)->add();
        if ($com){
            $this->ajaxReturn(array('result'=>"1"),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function video(){
        $sortvideo = D('sort_video');
        $data = $sortvideo->field('yk_sort_video.video_id,yk_sort_video.sort_img,yk_sort_video.sort_video,yk_sort_video.view_count,yk_sort_video.title,yk_user.nickname')
                          ->join('LEFT JOIN yk_user ON yk_sort_video.user_id=yk_user.user_id')
                          ->select();
        $data = $this->img_urls($data,'sort_img');
        if ($data){
            $this->ajaxReturn(array('result'=>"1",'data'=>$data),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function videoplay(){
        $video_id = I('post.video_id');
        $sortvideo = M('sort_video');
        $res = $sortvideo->where("video_id=$video_id")->setInc('view_count');
        if ($res){
            $this->ajaxReturn(array('result'=>"1"),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function live(){
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(5);
        $redis->select(2);
        $key = $redis->exists(321);
        if ($key){
            $data['onlive'] = 1;
        }else{
            $data['onlive'] = 0;
        }
        $user_id = I('post.user_id');
        $data = M('attention');
        $attu = $data->where("user_id = $user_id AND attu_id=321")->find();
        if ($attu){
            $res['attu'] = 1;
        }else{
            $res['attu'] = 0;
        }
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $re_code = $redis->get(309);
        $re_code = json_decode($re_code,true);
        $res['nickname'] = $re_code['nickname'];
        $res['user_img'] = $re_code['user_img'];
        $res['praise'] = $re_code['praise'];
        $res['count'] = $re_code['count'];
//        $res['pull'] = $re_code['pull'];
        $res['sum'] = $re_code['sum'];
        if ($res){
            $this->ajaxReturn(array('result'=>"1",'data'=>$res),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function attu(){
        $user_id = I('post.user_id');
        $con['user_id'] = $user_id;
        $con['attu_id'] = 321;
        $con['addtime'] = time();
        $count = M('attention');
        $count = $count->data($con)->add();
        if ($count){
            $this->ajaxReturn(array('result'=>"1"),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function forget_pwd(){
        $mobile = I('post.mobile');
        $m = M('user');
        $m = $m->field('mobile')->where("mobile=$mobile")->find();
        if ($m){
            $mobile = I('post.mobile');
            $username = "bjykwlkj"; //用户名
            $pwd = "142121"; //密码
            $data = send_sms($username,$pwd,$mobile);
            $this->ajaxReturn(array('result'=>"1"),'JSON');
        }else{
            $this->ajaxReturn(array('result'=>"0"),'JSON');
        }
    }
    public function change_pwd(){
        $mobile = I('post.mobile');
        $password = I('post.pwd');
        $code = I('post.code');
        $con['mobile'] = $mobile;
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(5);
        $re_code = $redis->get($mobile);
        if ($code == $re_code){
            $model_user = M('user');
            $login = $model_user->field('user_id')->where($con)->find();
            $model = new UserModel();
            $data['password_salt'] = $model->mkSalt();
            $data['password'] = $model->mkPassword($password);
            $login = $login['mobile'];
            $resule = $model_user->where("mobile=$mobile")->save($data); // 根据条件更新记录
            if ($resule){
                $this->ajaxReturn(array('result'=>"1"),'JSON');
            }else{
                $this->ajaxReturn(array('result'=>"0"),'JSON');
            }
        }else{
            $this->ajaxReturn(array('result'=>"10"),'JSON');
        }
    }
}

