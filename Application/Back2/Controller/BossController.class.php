<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 13:46
 */

namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use app\version1\controller\Boss;
use Back2\Model\AdminlogModel;
use Back2\Model\BossModel;
use Back2\Model\YanyiModel;
use Back2\Model\NameauditModel;
use Back2\Model\UserModel;
use Think\Controller;

class BossController extends Controller
{
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:12;
        $start_time =I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $status =  I('status')?:'1,2,3';
//        echo $status;
        $data = BossModel::getData($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function getDetail(){
        $boss_id = I('boss_id');
//        echo $boss_id;die;
        if(!$boss_id){
            echo json_encode(array('message'=>'必须输入boss_id'));die;
        }
        $data = BossModel::getDetail($boss_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function getOrganizationDetail(){
        $boss_id = I('boss_id');
//        echo $boss_id;die;
        if(!$boss_id){
            echo json_encode(array('message'=>'必须输入boss_id'));die;
        }
        $data = BossModel::getOrganizationDetail($boss_id);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    //个人boss认证审核
    public function audit(){
        $admin_id = AdminlogModel::getAdminId();
        $boss_id = I('boss_id')?:1;//默认为测试者的id，在nameaudit中的id
        $status = I('status')?:1;//默认为测试者的审核状态
        $user_id = I('user_id')?:698;//默认为测试这的user_id
        $reason = I('reason')?:'';
        $options = $this->options;
        //3个字段转换为数字
        $user_id += 0;
        $status += 0;
        $boss_id += 0;
//        echo $boss_id;echo ',';echo $status;die;

        $res1 = BossModel::changeStatus($boss_id,$user_id,$status);
        $res2 = UserModel::changeStatus($user_id,$status);
        if(!$res1 && !$res2){
            $data['message'] = '审核成功';
            AdminlogModel::addAdminLog($admin_id,$boss_id,5);
            if($status == 2){
                YanyiModel::setLiveRoom($user_id,$status,$options);
                $nameaudit['user_id'] = $user_id;
                $nameaudit['create_time'] = time();
                $nameaudit['console_time'] =time();
                $nameaudit['status'] = 2;
                M('nameaudit')->data($nameaudit)->add();

            }
            BossModel::sendMessage($user_id,$status,$options,$reason);
        }else{
            $data['message'] = '审核失败';
        }
//        dumpp($data);
        echo json_encode($data);
    }

    //机构boss认证审核
    public function organizationAudit(){
        $admin_id = AdminlogModel::getAdminId();
        $boss_id = I('boss_id')?:1;//默认为测试者的id，在nameaudit中的id
        $status = I('status')?:1;//默认为测试者的审核状态
        $user_id = I('user_id')?:698;//默认为测试这的user_id
        $reason = I('reason')?:'';
        $options = $this->options;
        //3个字段转换为数字
        $user_id += 0;
        $status += 0;
        $boss_id += 0;
//        echo $boss_id;echo ',';echo $status;die;

        $res = BossModel::changeStatus($boss_id,$user_id,$status);
        if(!$res){
            $data['message'] = '审核成功';
            AdminlogModel::addAdminLog($admin_id,$boss_id,5);
            if($status == 2){
                YanyiModel::setLiveRoom($user_id,$status,$options);
            }
            BossModel::sendOrganizationMessage($user_id,$status,$options,$reason);
        }else{
            $data['message'] = '审核失败';
        }
//        dumpp($data);
        echo json_encode($data);
    }

    public function organizationIndex(){
        $page = I('page')?:1;
        $rows = I('rows')?:12;
        $start_time =I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $status =  I('status')?:'1,2,3';
//        echo $status;
        $data = BossModel::getOrganizationData($page, $rows, $start_time, $end_time, $status, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}