<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 2017/8/1 0001
     * Time: 14:15
     */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
    use Think\Controller;
    use Back2\Model\ReportModel;

class ReportController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:12;
        $search_key = I('search_key')?:'';
        $view =  I('status')?:'0,1';
        $data = ReportModel::getData($page, $rows, $view, $search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}