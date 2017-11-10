<?php

namespace Back2\Model;

use Think\Model;

class ChannelModel extends Model
{
    static public function getData($page,$rows,$start_time,$end_time){
        $page+=0;
        $rows+=0;
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $data = M()->query("
          SELECT channel,count(user_id) num
          FROM yk_user
          WHERE (registertime BETWEEN {$start_time_stamp} AND {$end_time_stamp})
          GROUP BY channel
          LIMIT {$page},{$rows}
        ");
        return $data;

    }
}