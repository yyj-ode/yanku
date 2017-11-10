<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 11:14
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\AdminlogModel;
use Think\Controller;
use Back2\Model\UserModel;
use Back2\Model\FatherModel;
use Version2\Controller\MyController;

class UserController extends Controller
{
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');

    public function index(){
        //先给默认值
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time =I('start_time')?:'1970-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $status =  I('status')?:'0,1';
        $data = UserModel::getData($page, $rows, $start_time, $end_time, $search_key,$status);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //查看XX用户的所有报名记录
    public function enrollList(){
        $user_id = I('user_id')?:1;
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $data = UserModel::enrollList($page,$rows,$user_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //查看XX用户的所有面试
    public function interviewList(){
        $user_id = I('user_id')?:1;
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $data = UserModel::interviewList($page,$rows,$user_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

//    查看用户头像
    public function getUserImg(){
        $user_id = I('user_id')?:698;
        $data = UserModel::getUserImg($user_id);
        $arr['user_img'] = $data;
        $arr['user_id'] = $user_id;
//        echo $arr['user_img'];
        echo json_encode($arr);
    }

    //审核用户头像
    public function userImgAudit(){
        $user_id = I('user_id')?:698;
        $status = I('status')?:1;
        $options = $this->options;
        //3个字段转换为数字
        $user_id += 0;
        $status += 0;
        //status:  1是通过审核，2是没通过审核
//        echo $status;die;

        $res = UserModel::imgAudit($user_id,$status);
        if(!$res){
            $data['message'] = '审核成功';
            UserModel::sendMessage($user_id,$status,$options);
        }else{
            $data['message'] = '审核失败';
        }
//        echo $data['message'];die;
        echo json_encode($data);
    }

    //向全体用户推送消息
    public function sendMessageToAllUsers(){
        $options = $this->options;
        $content = I('content')?:'演库想您了';
        $group = I('group')?:0;
//        echo $group;die;
        if(!$group){    //如果没有选定用户群组
            echo json_encode(array('status'=>'发送失败，没有选定用户群组'));die;
        }
        if(!$content){
//            echo '发送失败';die;
            echo json_encode(array('status'=>'发送失败'));die;
        }
        UserModel::sendMessageToAllUsers($options,$content,$group);
//        echo '发送成功';die;
        echo json_encode(array('status'=>'发送成功'));
    }

    //后台添加用户
    public function add(){
        //先实例化fro写的基类
        $myController = new MyController();
        $admin_id = AdminlogModel::getAdminId();
        //上传头像
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'User/';
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            header("Location: http://39.108.104.233/userAdd.html");
        }else {// 上传成功 获取上传文件信息
            //这里应该先进行图片压缩，暂时先没写，以后得加上

//            对象存储，上传图片到阿里云OSS
            foreach ($info as $k=>$v){
                $up = $v['savepath'].$v['savename'];
                $data['user_img'] = $myController->uploadOss($up);
            }

            $data['mobile'] = I('mobile') ?: 'xx';
            $password = I('password') ?: 'xx';
            $passwordSalt = substr(md5(time()), 0, 5);
            $data['password'] = sha1($passwordSalt.$password);
            $data['nickname'] = I('nickname') ?: '';
            $data['sex'] = I('sex') ?: 0;
            $data['city'] = '';
            $data['kubi'] = 50;
            $data['registertime'] = time();
            $data['lasttime'] = time();
            $cityModel = M('user_city');
            $userModel = M('user');
            $where['city_name'] = I('city')?:'';

            $cityfind = $cityModel->field('city_id')->where($where)->find();

            if ($cityfind != '') {
                $data['city'] = $cityfind['city_id'];
                $insert = $userModel->data($data)->add();
            } else {
                $insert = $cityModel->data($where)->add();
                $data['city'] = $insert;
                $insert = $userModel->data($data)->add();
            }
            if ($insert) {
                AdminlogModel::addAdminLog($admin_id,$insert,6);
                header("Location: ".FatherModel::$html."/userAdd.html");
            } else {
                header("Location: ".FatherModel::$html."/userAdd.html");
            }
        }
    }

    //给指定艺人添加指定数量的僵尸粉
    public function addFans(){
        $attu_id = I('attu_id');
        $num = I('num')?:2;
        $num += 0;
        $attu_id += 0;

        $users_already = M('attention')->field('user_id')->where("attu_id = $attu_id")->select();
        $users_already = array_column($users_already,'user_id');
//        dumpp($users_already);
        $users_already = implode(',',$users_already);

        if($users_already == ''){
            $data['user_id'] = 698;
            $data['attu_id'] = $attu_id;
            $data['addtime'] = time();
            $res = M('attention')->data($data)->add();
            if($res){
                echo '已添加元永健为他的粉丝';die;
            }else{
                echo '添加失败';die;
            }
        }

        $users_ready = M('user')->field('user_id')->where("user_id not in ($users_already) and user_id != $attu_id")->limit($num)->select();
        $users_ready = array_column($users_ready,'user_id');
        foreach($users_ready as $k => $v){
            $data['user_id'] = $v;
            $data['attu_id'] = $attu_id;
            $data['addtime'] = time();
            $res = M('attention')->data($data)->add();
            if(!$res){
                echo '添加失败';
                die;
            }
        }

        echo '添加成功';
        echo '<br>';
        dumpp($users_ready);

    }

    //给所有艺人随机添加1-5个僵尸粉
    public function randomAddFans(){
        $users = M('yanyiaudit')->field('distinct(user_id)')->where("status = 2")->select();
        $users = array_column($users,'user_id');
//        dumpp($users);
        foreach($users as $k => $v){
            $attu_id = $v;
            $num = rand(1,5);
            $this->afterRandomAddFans($attu_id,$num);
        }
    }

    //↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    public function afterRandomAddFans($attu_id = 0,$num = 0){
        //判断这个用户是否存在
        $if_exists = M('user')->where("user_id = $attu_id")->find();
        if($if_exists == null){
            echo $attu_id.'这个用户已经被删掉了';
            echo '<br>';
            return 1;
        }

        $users_already = M('attention')->field('user_id')->where("attu_id = $attu_id")->select();
        $users_already = array_column($users_already,'user_id');
//        dumpp($users_already);
        $users_already = implode(',',$users_already);

        if($users_already == ''){
            $data['user_id'] = 698;
            $data['attu_id'] = $attu_id;
            $data['addtime'] = time();
            $res = M('attention')->data($data)->add();
            if($res){
                echo '已添加元永健为'.$data['attu_id'].'的粉丝';
                echo '<br>';
                return 1;
            }else{
                echo '添加失败';
                echo '<br>';
                return 1;
            }
        }

        $users_ready = M('user')->field('user_id')->where("user_id not in ($users_already) and user_id != $attu_id AND mobile like '233%'")->limit($num)->select();
        $users_ready = array_column($users_ready,'user_id');
        foreach($users_ready as $k => $v){
            $data['user_id'] = $v;
            $data['attu_id'] = $attu_id;
            $data['addtime'] = time();
            $res = M('attention')->data($data)->add();
            if(!$res){
                echo '添加失败';
                echo '<br>';
                return 1;
            }
        }

        echo $attu_id.'成功添加'.$num.'个粉丝';
        echo '<br>';

    }

    //拉黑用户
    public function lahei(){
        $redis = new \Redis();
        $redis ->connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(1);
        $user_id = I('user_id')?:698;
        $user_id += 0;
        $data['status'] = 1;
        $res = M('user')->where("user_id = $user_id")->save($data);
        if($res){
            $redis->delete($user_id);
            echo "拉黑成功";
        }else{
            echo "拉黑失败";
        }
    }

    //取消拉黑
    public function quxiaolahei(){
        $redis = new \Redis();
        $redis ->connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(1);
        $user_id = I('user_id')?:698;
        $user_id += 0;
        $data['status'] = 0;
        $res = M('user')->where("user_id = $user_id")->save($data);
        if($res){
            echo "取消拉黑成功";
        }else{
            echo "取消拉黑失败";
        }
    }
}