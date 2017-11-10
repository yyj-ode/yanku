<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Back2\Model\ActivityModel;
use Back2\Model\AdminlogModel;
use Back2\Model\FatherModel;
use Think\Controller;

class ActorController extends Controller
{
    public function getActorList(){
        //所有艺人列表
        $data = M('yanyiaudit y')
            ->field("DISTINCT(y.user_id),u.nickname")
            ->join('yk_user u ON y.user_id = u.user_id','LEFT')
            ->where("y.status=2 AND u.nickname is not NULL")
            ->select();
//        dumpp($data);
//        foreach($data as $k => $v){
//            $data[$k]['info'] = $v['user_id'].'-'.$v['nickname'];
//        }
        //已经创建过报名单的艺人列表
        $data_exist = M('vote')->field('user_id')->select();
        $exist = array_column($data_exist,'user_id');
//        dumpp($data);
        $result = [];
        foreach($data as $k => $v){
            if(in_array($v['user_id'],$exist)){
                unset($data[$k]);
            }else{
                $result[] = $v;
            }
        }
//        dumpp($data);
        echo json_encode($result);
    }



}