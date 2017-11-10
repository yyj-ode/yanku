<?php

namespace Back2\Model;

use Think\Model;

class VarietyModel extends Model
{
    static public function videoVerify($data1,$uids,$episode_src){
        if(!$data1['title']){
            echo '综艺标题不能为空';
            die;
        }
        if(!$data1['introduce']){
            echo '综艺长介不能为空';
            die;
        }
        if(!$data1['sort_introduce']){
            echo '综艺简介不能为空';
            die;
        }
        if(!$data1['video_img']){
            echo '综艺封面没有选定';
            die;
        }
        foreach($uids as $v){
            if($v == ''){
                echo '还有演员没有选定';
                die;
            }
        }
        $count = count($episode_src);
//        if(!$count){
//            echo '还没有上传视频';
//            die;
//        }
    }

    static function getVarietyListData($page, $rows,$search_key){
        $start = ($page-1)*$rows;
        $where = [
            "push = 1 and title like '%{$search_key}%'"
        ];
        $data['list'] = M('video')
            ->where($where)
            ->order("vid desc")
            ->limit($start,$rows)
            ->select();
        foreach($data['list'] as $k =>$v){
            $data['list'][$k]['video_img'] = FatherModel::$img_host.$v['video_img'];
            $data['list'][$k]['episode_num'] = M('video_episode')
                ->where("vid = {$v['vid']}")
                ->count();
            if($data['list'][$k]['episode_num'] <= 1){
                $data['list'][$k]['type'] = '单集';
            }else{
                $data['list'][$k]['type'] = '多集';
            }
        }
        $count =M('video')
            ->where($where)
            ->count();
        $data['count'] = ceil($count/$rows);
        return $data;
    }
}