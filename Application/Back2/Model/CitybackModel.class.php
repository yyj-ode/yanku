<?php

namespace Back2\Model;

use Think\Model;

class CitybackModel extends Model
{
    protected $tableName = 'yk_cityback';
    static public function getCityData($province_name){
        $data = M('cityback')->where("province_name = '{$province_name}'")->select();
        return $data;
    }
}