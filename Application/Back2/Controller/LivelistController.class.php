<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Think\Controller;

class LivelistController extends Controller
{
    public function index(){
        $page = I('page')?:1;
        $rows = I('rows')?:15;
        $page += 0;
        $rows+=0;
        $start = ($page-1)*3;
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        //判断是否有数据，没有返回提醒
        $key = $redis->keys('*');
//        echo '<br>';print_r($key);die;
        $kk = $redis->mget($key);

        $count = count($kk);

        //每页取$rows条数据
        $data = [];
        $data['count'] = ceil($count/$rows);
        for($i = 0;$i<$rows;$i++){
            if($kk[$start+$i]){
                $data['list'][] = $kk[$start+$i];
            }
        }
        if($data['list']){
            foreach ($data['list'] as $k=>&$v){
                $redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
                $v = json_decode($v,true);
            }
            echo json_encode($data);
        }else{
            echo json_encode(array('status'=>'暂无直播'));
        }

    }

    //返回正在直播的数量
    public function num(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        //$redis->config get databases;
        $key = $redis->keys('*');
        $kk = $redis->mget($key);
        foreach ($kk as $k=>&$v){
            $redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
            $v = json_decode($v,true);
        }
        return count($kk);
    }

    //返回直播监控的数据
    public function monitor(){
        $page = I('page')?:1;
        $start = ($page-1)*10;
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        //$redis->config get databases;
        $key = $redis->keys('*');
        $kk = $redis->mget($key);
        $count = count($kk);
        foreach ($kk as $k=>&$v){
            $redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
            $v = json_decode($v,true);
            unset($kk[$k]['praise']);
            unset($kk[$k]['sum']);
            unset($kk[$k]['chatroom']);
            unset($kk[$k]['count']);
            unset($kk[$k]['city']);
            unset($kk[$k]['sex']);
            unset($kk[$k]['level']);
            unset($kk[$k]['hao_level']);
            unset($kk[$k]['channel_type']);
            unset($kk[$k]['kubi']);
        }
        //每页取3条数据
        $data = [];
        for($i = 0;$i<10;$i++){
            if($kk[$start+$i]){
                $data['list'][] = $kk[$start+$i];
            }
        }
        $data['count'] = ceil($count/10);
        //var_dump($kk[0]['room_id']);die();
//        echo '<pre>';print_r($data);die;
        echo json_encode($data);
    }
}
