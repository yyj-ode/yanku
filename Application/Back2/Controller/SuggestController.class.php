<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');


use Back2\Model\SuggestModel;
use Think\Controller;

class SuggestController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:12;
        $data = SuggestModel::getData($page,$rows);
        echo json_encode($data);
    }
}