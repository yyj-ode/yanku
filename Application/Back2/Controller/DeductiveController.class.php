<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 14:31
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\AdminlogModel;
use Back2\Model\YanyiModel;
use Back2\Model\FatherModel;
use Think\Controller;
use Version2\Controller\MyController;
use Back2\Model\UserModel;

class DeductiveController extends Controller
{
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');

    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time =I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $status =  I('status')?:'1,2,3';
        $data = YanyiModel::getData($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function getDetail(){
        $yanyi_id = I('yanyi_id');
        if(!$yanyi_id){
            echo json_encode(array('message'=>'必须输入yanyi_id'));die;
        }
        $data = YanyiModel::getDetail($yanyi_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function audit(){
        $status = I('status')?:1;
        $yanyi_id = I('yanyi_id')?:115;
        $user_id = I('user_id')?:698;
        $reason = I('reason')?:'';
        $admin_name = I('admin_name')?:'';//管理员的ID
        $admin_info = M('admin')->field('admin_id')->where("admin_name = '{$admin_name}'")->find();
        $admin_id = $admin_info['admin_id'];
        $options = $this->options;
        //3个字段转换为数字
        $user_id += 0;
        $status += 0;
        $yanyi_id += 0;

        $res = YanyiModel::changeStatus($yanyi_id,$status,$reason);
        if(!$res){
            $data['message'] = '审核成功';
            YanyiModel::sendMessage($user_id,$status,$options,$reason);
            AdminlogModel::addAdminLog($admin_id,$yanyi_id,3);
        }else{
            $data['message'] = '审核失败';
        }
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);

    }

    public function add(){
        $result = 0;
        $admin_id = AdminlogModel::getAdminId();
        $type = I('type');
        $realname = I('realname');
        $yanyi_img = I('yanyi_img');
        $user_id = I('user_id');
        $this->ifPass($user_id);

//        $data['type'] = $type;
//        $data['realnmae'] = $realname;
//        $data['yanyi_img'] = $yanyi_img;
//        $data['user_id'] = $user_id;

//        dumpp($data);打印出的数组如下：
//        Array
//        (
//            [type] => Array
//            (
//                [0] => on
//                [1] => on
//                [2] => on
//                [3] => on
//                [4] => on
//        )
//            [realnmae] => 1111
//            [yanyi_img] => Yanyi/2017-09-15/59bb7c3c0c299.jpg,Yanyi/2017-09-15/59bb7c3c19d29.jpg,Yanyi/2017-09-15/59bb7c3f300cf.jpg,
//            [user_id] => 698
//        )

        //user表：修改用户类型为演员,user_type修改为1,并把真实姓名改成$realname
        $userModel = new UserModel();
        $userModel->query("update yk_user set user_type = 1 WHERE user_id=$user_id");
        $userModel->query("update yk_user set realname = '{$realname}' WHERE user_id=$user_id");

        //user_type表：添加用户类型user_id,type
        $usertypeModel = M('user_type');
        $usertype['user_id'] = $user_id;
        foreach($type as $k => $v){
            $k += 0;
            $usertype['type'] = $k;
            $usertypeModel->data($usertype)->add();
        }

        //yanyiaudit表添加认证信息
        $yanyiauditModel = M('yanyiaudit');
        $yanyiaudit['user_id'] = $user_id;
        $yanyiaudit['create_time'] = time();
        $yanyiaudit['status'] = 1;
        $yanyi_id = $yanyiauditModel->data($yanyiaudit)->add();

        //yanyi表：添加图片yanyi_img,user_id,yanyi_id
        $yanyiModel = M('yanyi');
        $yanyi['user_id'] = $user_id;
        $yanyi['yanyi_id'] = $yanyi_id;
        $yanyi_imgs = explode(',',$yanyi_img);
        foreach($yanyi_imgs as $k => $v){
            $yanyi['yanyi_img'] = $v;
            $result = $yanyiModel->data($yanyi)->add();
        }
        //记录管理员操作日志
        AdminlogModel::addAdminLog($admin_id,$yanyi_id,7);
        //这里以后改成返回一个json，让前台去跳回用户列表页
        if($result){

            header("Location: ".FatherModel::$html."/upload/deductiveApply.html");
        }else{
            header("Location: ".FatherModel::$html."/upload/deductiveApply.html");
        }

    }

    //判断用户是否已通过演绎认证
    public function ifPass($user_id){
        $user_id += 0;
        $yanyiModel = M('yanyiaudit');
        $data = $yanyiModel->where("user_id = $user_id and status = 2")->find();
        if($data != null){
            $data['code'] = '0';
            $data['msg'] = '';
            header("Location: ".FatherModel::$html."/deductiveApply.html");
            die;
        }
    }

    public function imgUpload(){
        //初始化图片地址字符串
        $data['data']['src'] = '';
        //先实例化fro写的基类
        $myController = new MyController();
        //上传剧照
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Yanyi/';
        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            $myController->json_rest(0);
            die();
        }else {// 上传成功 获取上传文件信息
            //这里应该先进行图片压缩，暂时先没写，以后得加上

            //对象存储，上传图片到阿里云OSS
            foreach ($info as $k=>$v){
                $up = $v['savepath'].$v['savename'];
                $data['data']['src'] .= $myController->uploadOss($up).',';
            }

        }
        $data['code'] = '0';
        $data['msg'] = '';
        echo json_encode($data);
    }
}