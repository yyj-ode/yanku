<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Back2\Model\ActivityModel;
use Back2\Model\AdminlogModel;
use Back2\Model\FatherModel;
use Think\Controller;

class ActivityController extends Controller
{
    public function index(){
        $data = ActivityModel::getData();
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //演库区/轮播区 轮播图添加
    public function add(){
        $data['activity_url'] = I('activity_url')?:'';
        if(!$data['activity_url']){
            echo 'meiyouurl';
            return false;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Activity/';
        $upload->autoSub = false;
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $data['error'] = $upload->getError();
            echo $data['error'];
            return json_encode($data['error']);
        }else{// 上传成功
//            echo '<pre>';print_r($info);die;
            $data['activity_img'] = $info['activity_img']['savepath'].$info['activity_img']['savename'];
//            echo '<pre>';print_r($data);die;
            $activityModel = M('Activity');
            $res = $activityModel->data($data)->add();
            if(!$res){
                echo '添加失败';
            }

        }
    }

    public function activityImgUpload(){
        $data['activity_url'] = I('activity_url')?:'';
        if(!$data['activity_url']){
            $this->error('没添加活动网址');
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Activity/';
        $upload->autoSub = false;
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $data['error'] = $upload->getError();
            $this->error( $data['error']);
        }else{// 上传成功
//            echo '<pre>';print_r($info);die;
            $up = $info['activity_img']['savepath'].$info['activity_img']['savename'];
            $resController = new \Version2\Controller\ResController();
            $data['activity_img'] = $resController->uploadOss($up);
//            $data['activity_img'] = $info['activity_img']['savepath'].$info['activity_img']['savename'];
            $activityModel = M('Activity');
            $res = $activityModel->data($data)->add();
            if($res){
                $activity_info = M('activity')->field('activity_id')->order('activity_id desc')->limit(1)->find();
                $activity_id = $activity_info['activity_id'];
                $admin_id = AdminlogModel::getAdminId();
                AdminlogModel::addAdminLog($admin_id,$activity_id,1);
                $this->success('轮播图添加成功');
            }else{
                $this->error('操作失败');
            }

        }


    }

    public function del(){
        $admin_id = AdminlogModel::getAdminId();
        $activity_id = I('activity_id');
        $activityModel = M('Activity');
        $res = $activityModel->where("activity_id = $activity_id")->delete();
        if($res){
//            echo '轮播图下架成功';die;
            $data = ActivityModel::getData();
            $data['status'] = '成功';
            AdminlogModel::addAdminLog($admin_id,$activity_id,8);
            echo json_encode($data);
        }else{
            $data['status'] = '失败';
            echo json_encode($data);
        }
    }

}