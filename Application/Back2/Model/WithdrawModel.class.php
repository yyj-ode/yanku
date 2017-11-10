<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 12:35
 */
namespace Back2\Model;

use Think\Model;

class WithdrawModel extends Model
{
    protected $tableName = 'yk_recharge';
    static public function getData($page,$rows,$start_time,$end_time,$status,$search_key){
        $start = $rows * ($page-1);
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_recharge.create_time between $start_time_stamp and $end_time_stamp",
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%'",
            "yk_recharge.pay_status in ($status)",
            "yk_recharge.pay_type = 1"
        ];
        $data = M('Recharge')
            ->join('yk_user on yk_recharge.user_id = yk_user.user_id','LEFT')
            ->field('yk_recharge.*,yk_user.nickname')
            ->where($where)
            ->limit($start,$rows)
            ->select();
//        echo '<pre>';print_r($data);die;
        $change_status = [
            0=>'等待',
            1=>'成功',
            2=>'失败'
        ];
        foreach($data as $k => $v){
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['pay_status'] = $change_status[$v['pay_status']];
            $data[$k]['kubi'] = $data[$k]['amount'] * 66;
        }
        $count = M('Recharge')
            ->join('yk_user on yk_recharge.user_id = yk_user.user_id','LEFT')
            ->field('yk_recharge.*,yk_user.nickname')
            ->where($where)
            ->count();
//        echo '<pre>';print_r($data);die;
        $arr['list'] = $data;
        $arr['count'] = ceil($count/15);
        return $arr;
    }
}