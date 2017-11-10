<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14 0014
 * Time: 12:42
 */
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Back2\Model\CatalogModel;
use Back2\Model\CitybackModel;
use Back2\Model\FatherModel;
use Think\Controller;
class CitybackController extends Controller
{
    public function add(){
        $model = M('cityback');
        $data['province_name'] = '1';
        $data['city_name'] = '2';
        for($i=0;$i<7;$i++){
            $res = $model->data($data)->add();
            if($res){
                echo '成功';
            }else{
                echo '失败';
            }
        }

//        $data = $model->select();
//        dumpp($data);
    }

    public function getCity(){
        $province = I('province')?:'北京市';
        if(!$province){
            echo json_encode(0);
        }
        $data = CitybackModel::getCityData($province);
        echo json_encode($data);
    }
}