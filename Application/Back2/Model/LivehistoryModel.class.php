<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 15:59
 */
namespace Back2\Model;

use Think\Model;

class LivehistoryModel extends Model
{
    protected $tableName = 'yk_live_history';
    static public function getData($page, $rows, $start_time, $end_time, $search_key)
    {
        $start = ($page - 1) * $rows;
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time) + 86400;
        $where = [
            "l.starttime between $start_time_stamp and $end_time_stamp",
            "u.user_id like '%{$search_key}%' or u.nickname like '%{$search_key}%' or u.realname like '%{$search_key}%'",
        ];
        $data = M('Live_history l')
            ->join('yk_user u on l.user_id = u.user_id', 'LEFT')
            ->join('yk_room r on l.user_id = r.user_id', 'LEFT')
            ->field('l.*,u.nickname,r.room_name,r.push,r.pull')
            ->where($where)
            ->order('l.starttime desc')
            ->limit($start, $rows)
            ->select();
        foreach ($data as $k => $v) {
            $data[$k]['starttime'] = date('Y-m-d H:i:s', $v['starttime']);
            $data[$k]['endtime'] = date('Y-m-d H:i:s', $v['endtime']);
        }
        $count = M('Live_history l')
            ->join('yk_user u on l.user_id = u.user_id', 'LEFT')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }
}