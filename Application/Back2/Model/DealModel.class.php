<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 12:53
 */
namespace Back2\Model;

use Think\Model;

class DealModel extends Model
{
    protected $tableName = 'yk_nameaudit';
    static public function getData($page,$rows,$start_time,$end_time,$search_key){
        $start = $rows * ($page-1);
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_deal.pay_time between $start_time_stamp and $end_time_stamp",
            "t.nickname like '%{$search_key}%' or t.user_id like '%{$search_key}%'",
        ];
        $data = M('Deal')
            ->field('yk_deal.*,f.nickname from_user_name,t.user_id to_user_id,t.nickname to_user_name,g.name gift_name,g.price gift_price')
            ->join('yk_user f on yk_deal.from_user_id = f.user_id','LEFT')
            ->join('yk_user t on yk_deal.to_user_id = t.user_id','LEFT')
            ->join('yk_gift g on yk_deal.gift_type = g.gift_type','LEFT')
            ->where($where)
            ->limit($start,$rows)
            ->select();

        foreach($data as $k => $v){
            $data[$k]['total_price'] = $v['gift_price'] * $v['number'];
            $data[$k]['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
        }
        $count =  M('Deal')
            ->field('yk_deal.*,f.nickname from_user_name,t.user_id to_user_id,t.nickname to_user_name,g.name gift_name,g.price gift_price')
            ->join('yk_user f on yk_deal.from_user_id = f.user_id','LEFT')
            ->join('yk_user t on yk_deal.to_user_id = t.user_id','LEFT')
            ->join('yk_gift g on yk_deal.gift_type = g.gift_type','LEFT')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/15);
        return $arr;
    }
}