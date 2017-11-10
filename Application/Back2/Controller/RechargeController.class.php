<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 10:35
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Think\Controller;
use Back2\Model\RechargeModel;

class RechargeController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time = I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $status = I('status')?:'0,1,2';
        $search_key = I('search_key')?:'';
        $data = RechargeModel::getData($page,$rows,$start_time,$end_time,$status,$search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function exp(){

    }
}