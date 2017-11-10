<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 13:46
 */

namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\AdminlogModel;
use Back2\Model\NameauditModel;
use Back2\Model\YanyiModel;
use Back2\Model\UserModel;
use Think\Controller;

class IdentityController extends Controller
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
        $data = NameauditModel::getData($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }


    public function getDetail(){
        $id = I('id')?:111;
        $data = UserModel::getDetail($id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }


    //身份认证审核
    public function audit(){
        $id = I('id')?:466;//默认为测试者的id，在nameaudit中的id
        $status = I('status')?:1;//默认为测试者的审核状态
        $user_id = I('user_id')?:698;//默认为测试这的user_id
        $reason = I('reason')?:'';
        $admin_name = I('admin_name')?:'';//管理员的ID
        $admin_info = M('admin')->field('admin_id')->where("admin_name = '{$admin_name}'")->find();
        $admin_id = $admin_info['admin_id'];

        $options = $this->options;
        //3个字段转换为数字
        $user_id += 0;
        $status += 0;
        $id += 0;

        $res1 = NameauditModel::changeStatus($id,$user_id,$status);
        $res2 = UserModel::changeStatus($user_id,$status);
        AdminlogModel::addAdminLog($admin_id,$id,2);

        if(!$res1 && !$res2){
            $data['message'] = '审核成功';
            if($status == 2){
                YanyiModel::setLiveRoom($user_id,$status,$options);
            }
            NameauditModel::sendMessage($user_id,$status,$options,$reason);
        }else{
            $data['message'] = '审核失败';
        }
        echo json_encode($data);
    }


}