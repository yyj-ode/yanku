<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 12:53
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\DealModel;
use Think\Controller;

class DealController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time = I('start_time')?:'2017-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $search_key = I('search_key')?:'';
        $data = DealModel::getData($page,$rows,$start_time,$end_time,$search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}