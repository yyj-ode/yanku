<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 14:41
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Think\Controller;
use Back2\Model\GiftModel;

class GiftController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $data = GiftModel::getData($page,$rows);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}