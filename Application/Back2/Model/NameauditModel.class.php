<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 13:49
 */

namespace Back2\Model;


use Think\Model;
use Back\Controller\HXController;

class NameauditModel extends Model
{
    protected $tableName = 'yk_nameaudit';
    public static function getData($page, $rows, $start_time, $end_time, $status, $search_key){
        $start = $rows * ($page-1);
        //时间字符串转换为时间戳
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_nameaudit.create_time between $start_time_stamp and $end_time_stamp",
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%' or yk_user.mobile like '%{$search_key}%'",
            "yk_nameaudit.status in ($status)",
        ];

        $data = M('Nameaudit')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_nameaudit.create_time,yk_nameaudit.console_time,yk_nameaudit.status,yk_user.mobile,yk_nameaudit.id')
            ->join('yk_user ON yk_nameaudit.user_id = yk_user.user_id')
            ->where($where)
            ->limit($start,$rows)
            ->order("yk_nameaudit.create_time desc")
            ->select();
        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        foreach($data as $k => $v){
            $data[$k]['console_time'] = date('Y-m-d H:i:s',$v['console_time']);
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['status'] = $change_status[$v['status']];
        }
        $count = M('Nameaudit')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_nameaudit.create_time,yk_nameaudit.console_time,yk_nameaudit.status,yk_user.mobile')
            ->join('yk_user ON yk_nameaudit.user_id = yk_user.user_id')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    static public function changeStatus($id,$user_id,$status){
        $status += 0;
        $id += 0;
        $NameauditModel = M("Nameaudit"); // 实例化User对象
        // 要修改的数据对象属性赋值
        $data['id'] = $id;
        $data['status'] = $status;
        $data['console_time'] = time();
        $NameauditModel->save($data);
    }


    //向用户推送消息
    static public function sendMessage($user_id,$status,$options,$reason){
        $hx = new HXController($options);
        if(!$reason && $status == 3){
            $reason = '您的实名认证因为'.$reason.'的原因认证失败，请重新进行实名认证';
            $hx->sendText('admin','users',array("$user_id"),$reason);
        }
        if($status == 2){
            $hx->sendText('admin','users',array("$user_id"),'恭喜您已经通过实名认证，您现在已经可以进行直播了');
        }
    }
}