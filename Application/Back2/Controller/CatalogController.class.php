<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Think\Controller;
use Back2\Model\CatalogModel;

class CatalogController extends Controller
{
    public function index(){
        //————————————————————————————————————————————

        //layui调试阶段，$user默认设为admin,之后设为字符串'吃瓜群众''
        $user = I('user')?:'吃瓜群众';
//        session_start();
//        $user = session('user')?:'admin';
//        echo $user;die;


        //————————————————————————————————————————————


        $role = M()->query("select role_id from yk_admin where admin_name = '{$user}'");
        $role = $role[0]['role_id'];
        $data = CatalogModel::getData($role);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}