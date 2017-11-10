<?php

namespace Back2\Model;

use Think\Model;

class SuggestModel extends Model
{
   static public function getData($page,$rows){
       $rows = 8 ;
       $start = ($page - 1) * $rows;
       $where = "s.createtime != 0";
       $data['list'] = M('suggest s')
           ->field('s.suggestion,s.createtime,u.nickname,s.user_id,u.mobile')
           ->join('yk_user u ON s.user_id = u.user_id','LEFT')
           ->where($where)
           ->order('s.createtime desc')
           ->limit($start,$rows)
           ->select();
       foreach($data['list'] as $k => $v){
           $data['list'][$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']) ;
       }
       $count = M('suggest s')->where($where)->count();
       $data['count'] = ceil($count/$rows);
       return $data;
   }
}
