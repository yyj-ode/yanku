<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 14:42
 */
namespace Back2\Model;

use Think\Model;

class GiftModel extends Model
{
    protected $tableName = 'yk_nameaudit';
    static public function getData($page,$rows){
        $start = $rows * ($page-1);
        $data = M('Gift')
            ->limit($start,$rows)
            ->select();
        $change = [
            1 => '打赏类',
            2 => '众筹类'
        ];
        foreach ($data as $k => $v){
            $data[$k]['type'] = $change[$v['type']];
        }
        $count = M('Gift')
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }
}