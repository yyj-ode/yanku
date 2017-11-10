<?php

namespace Back2\Model;

use Think\Model;

class ActivityModel extends Model
{
    protected $tableName = 'yk_activity';
    static public function getData(){
        $data['list'] = M('Activity')->select();
        foreach($data['list'] as $k => $v){
            $data['list'][$k]['activity_img'] = FatherModel::$img_host. $data['list'][$k]['activity_img'];
        }
        return $data;
    }


}