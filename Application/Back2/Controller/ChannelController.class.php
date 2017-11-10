<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Back2\Model\CatalogModel;
use Back2\Model\ChannelModel;
use Think\Controller;

class ChannelController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $start_time =I('start_time')?:'1970-01-01';
        $end_time = I('end_time')?:date('Y-m-d',time());
        $data = ChannelModel::getData($page,$rows,$start_time,$end_time);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}