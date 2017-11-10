<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Back2\Model\ActivityModel;
use Back2\Model\AdminlogModel;
use Think\Controller;
use Back2\Model\BannerModel;

class BannerController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $search_key = I('search_key')?:'';
        $data = BannerModel::getData($page,$rows,$search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }

    public function del(){
        $banner_id = I('banner_id');
        $admin_id = AdminlogModel::getAdminId();
        if(!$banner_id){
            echo json_encode(array('status'=>'失败，没有banner_id'));die;
        }
        $bannerModel = M('Banner');

        $res = $bannerModel->where("banner_id = $banner_id")->delete();
        if($res){
//            echo '轮播图下架成功';die;
            $page = I('page')?:1;
            $rows = I('rows')?:15;
            $search_key = I('search_key')?:'';
            $data = BannerModel::getData($page,$rows,$search_key);
            $data['status'] = '成功';
            AdminlogModel::addAdminLog($admin_id,$banner_id,9);
            echo json_encode($data);
        }else{
//            echo '轮播图下架失败';die;
            echo json_encode(array('status'=>'失败'));
        }
    }

    public function add(){
        $schedule_id = I('schedule_id');
        $admin_id = AdminlogModel::getAdminId();
        $bannerModel = M('Banner');

        $count = $bannerModel->where('banner_type = 0')->count();
        if($count >= 4){
//            echo '轮播图数量达到上限';die;
            echo json_encode(array('status'=>'失败','reason'=>'轮播图数量达到上限'));die;
        }

        $data = [
            'banner_type' => 0,
            'type_id' => 0,
            'banner_id' => $schedule_id
        ];
        $exist = count($bannerModel->query("select * from yk_banner where banner_id = '{$schedule_id}'"));
        if(!$exist){    //没有改轮播图，则可添加
            $res = $bannerModel->add($data);
            if($res){
//                echo '轮播图添加成功';die;
                AdminlogModel::addAdminLog($admin_id,$schedule_id,0);
                echo json_encode(array('status'=>'成功','reason'=>'无'));
//                echo json_encode(array('status'=>'轮播图添加成功'));
            }else{
//                echo '轮播图添加失败';die;
                echo json_encode(array('status'=>'失败','reason'=>'轮播图添加失败'));
//                echo json_encode(array('status'=>'轮播图添加失败'));
            }
        }else{
//            echo '该轮播图已存在';die;
            echo json_encode(array('status'=>'失败','reason'=>'该轮播图已存在'));
//            echo json_encode(array('status'=>'该轮播图已存在'));
        }

    }

    public function getScheduleData(){
        $page = I('page')?:1;
        $rows =  I('rows')?:12;
        $search_key = I('search_key')?:'';
        $data = BannerModel::getScheduleData($page,$rows,$search_key);
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}
?>