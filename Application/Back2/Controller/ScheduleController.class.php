<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 14:15
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\AdminlogModel;
use Back2\Model\ScheduleModel;
use Think\Controller;
use Version2\Controller\MyController;

class ScheduleController extends Controller
{
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');

    public function audit(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $status =  I('status');
        if($status !== '0'){
            $status =  I('status')?:'0,1,2,3';
        }else{

        }
        $search_key = I('search_key')?:'';
        $start_time =I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $data = ScheduleModel::audit($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function statistics(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time =I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $status =  I('status')?:'0,1';
        $data = ScheduleModel::statistics($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function notAttendList(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $schedule_id = I('schedule_id')?:1;
        $data = ScheduleModel::notAttendList($schedule_id,$page,$rows);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //查看XX公告的报名用户列表
    public function enrollList(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $schedule_id = I('schedule_id')?:1;
        $data = ScheduleModel::enrollList($page,$rows,$schedule_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //查看XX公告的面试列表
    public function interviewList(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $schedule_id = I('schedule_id')?:1;
        $data = ScheduleModel::interviewList($page,$rows,$schedule_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

//    通告详情
    public function scheduleDetail(){
        $schedule_id = I('schedule_id')?:1;
        $data = ScheduleModel::scheduleDetail($schedule_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //通告修改
    public function scheduleEdit(){
        $schedule_id = I('schedule_id')?:1;
        $data = ScheduleModel::scheduleEdit($schedule_id);
//        echo '<pre>';print_r($data);die;
//        dumpp($data);
        echo json_encode($data);
    }

//    审核通告
    public function scheduleAudit(){
        $options = $this->options;
        $admin_id = AdminlogModel::getAdminId();
        $schedule_id = I('schedule_id')?:0;
        $user_id = I('user_id')?:0;
        if(!($schedule_id || $user_id)){
            $data['message'] = '审核失败,未传递用户id或通告id';
            echo json_encode($data);die;
        }
        $status = I('status')?:0;  //0.审核中，1招募中，2已截止，3拉黑
        $reason = I('reason')?:0;
        if((!$reason) && ($status ==3)){
            $data['message'] = '审核失败,未填写失败原因';
            echo json_encode($data);die;
        }

        $res = ScheduleModel::changeStatus($schedule_id,$status);

        if(!$res){
            $data['message'] = '审核成功';
            AdminlogModel::addAdminLog($admin_id,$schedule_id,10);
            ScheduleModel::sendMessage($user_id,$status,$options,$reason);
        }else{
            $data['message'] = '审核失败';
        }
        echo json_encode($data);

    }

//    发布通告
    public function scheduleAdd(){
        $data['user_id'] = 310;
        $data['status'] = 1;
        $data['schedule_title'] = I('schedule_title');
        $data['schedule_type'] = I('schedule_type');
        $data['createtime'] = time();
        $data['address'] = I('address');
        $data['acttime'] = strtotime(I('acttime'));
        $data['valcode'] = rand(1000,9999);
        $data['city'] = I('city');
        $data['sex'] = I('sex');
        $data['schedule_content'] = I('schedule_content','');
        $data['audit'] = I('audit');
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Schedule/';

        $info = $upload->upload();
        dumpp($info);
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(0);
            die();
        }else {// 上传成功 获取上传文件信息
            foreach ($info as $k => $v) {
                if ($k == 'schedule_img') {
                    $con['schedule_img'] = $v['savepath'] . $v['savename'];
                } elseif ($k == 'content') {
                    $con['schedule_content'] = '<URL>http://' . $_SERVER ['HTTP_HOST'] . '/Upload/' . $v['savepath'] . $v['savename'];
                }
            }


        }
        dumpp($data);
    }


    //修改通告时候的图片上传
    public function imgUpload(){
        $fatherController = new FatherController();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     '/upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Schedule/';
        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            die();
        }else{// 上传成功 获取上传文件信息
            $src = '';
            foreach ($info as $k=>$v){
                $up = $v['savepath'].$v['savename'];
                $src = $fatherController->uploadOss($up);
            }
            $arr = [
                "code"=> 0,
                "msg"=>"图片上传成功!",
                "data"=> [
                    "src"=>  '<URL>http://img.yankushidai.com/'.$src
                ]
            ];
        }
        echo json_encode($arr);
    }


    //修改通告
    //一定要判断2个修改没有给的时候，要怎么判断！！！！！！！！！！！！！！！！
    public function scheduleUpdate(){
        //判断该通告状态，如果是审核中的，就可以修改，否则就不能修改
        $schedule_id = I('schedule_id')?:0;
        $schedule_id+=0;
        $arr = M('schedule')->where("schedule_id = $schedule_id")->find();
        $status = $arr['status'];
        if($status != 0){
            echo json_encode(['message'=>'该通告已经通过审核，无法再次进行修改']);
            die;
        }

        $fatherController = new FatherController();//OSS对象存储用
        $admin_id = AdminlogModel::getAdminId();//记录管理员操作日志用
        //判断是否登录
        $admin_name = I('admin_name')?:'';
        if(!$admin_name){
            echo json_encode(['message'=>'您没登录哦']);
            die;
        }
        //判断该管理员是否有该权限

        //判断通告内容是否为图片
        $src = I('src');
        if($src != ''){
            $data['schedule_content'] = $src;
        }else{
            $schedule_content = I('schedule_content')?:0;
            if($schedule_content){
                $data['schedule_content'] = I('schedule_content');
            }
        }
        //创建通告图片
        $background = I('background')?:1;
        $text = I('text')?:'';
        if($text){
            $data['schedule_img'] = miximg($text,$background);
            //上传通告图片
            $fatherController->uploadOss($data['schedule_img']);//这一步失败了
        }

        //获取城市id
        $city = I('city');
        $cityfind = M('city')->field('city_id')->where("city_name = '{$city}'")->find();
        if ($cityfind!=''){
            $data['city'] = $cityfind['city_id'];
        }else{
            $insert = $city->data("city_name = '{$city}'")->add();
            $data['city'] = $insert;
        }

        //获取其他字段
        $data['schedule_id'] = I('schedule_id');
        $data['user_id'] = I('user_id');
        $data['schedule_title'] = I('schedule_title')?:0;
        if(!$data['schedule_title']){
            echo json_encode(['message'=>'通告名不能为空']);
            die;
        }
        $data['sex'] = I('sex')?:0;
        $data['schedule_type'] = I('schedule_type')?:0;

        $res = M('schedule')->data($data)->save();

        if($res){
            $result['message'] = '修改成功';
        }else{
            $result['message'] = '修改失败:可能是没有修改任何资料';
        }
        AdminlogModel::addAdminLog($admin_id,$data['schedule_id'],11);
        echo json_encode($result);
    }


    public function demo(){
        echo date('Y-m-d',1507788691);
    }
}