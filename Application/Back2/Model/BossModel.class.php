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

class BossModel extends Model
{
    protected $tableName = 'yk_boss';
    public static function getData($page, $rows, $start_time, $end_time, $status, $search_key){
        $start = $rows * ($page-1);
        //时间字符串转换为时间戳
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_boss.organizationImg = ''",
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%' or yk_user.mobile like '%{$search_key}%'",
            "yk_boss.status in ($status)",
        ];

        $data = M('Boss')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_user.IDcard_number,yk_boss.status,yk_user.mobile,yk_boss.boss_id')
            ->join('yk_user ON yk_boss.user_id = yk_user.user_id')
            ->where($where)
            ->limit($start,$rows)
            ->order("yk_boss.boss_id desc")
            ->select();
        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        foreach($data as $k => $v){
//            $data[$k]['console_time'] = date('Y-m-d H:i:s',$v['console_time']);
//            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['status'] = $change_status[$v['status']];
        }
        $count = M('Boss')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_boss.create_time,yk_boss.console_time,yk_boss.status,yk_user.mobile')
            ->join('yk_user ON yk_boss.user_id = yk_user.user_id')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    public static function getOrganizationData($page, $rows, $start_time, $end_time, $status, $search_key){
        $start = $rows * ($page-1);
        //时间字符串转换为时间戳
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_boss.organizationImg <> ''",
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%' or yk_user.mobile like '%{$search_key}%'",
            "yk_boss.status in ($status)",
        ];

        $data = M('Boss')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_boss.organization,yk_boss.status,yk_user.mobile,yk_boss.boss_id')
            ->join('yk_user ON yk_boss.user_id = yk_user.user_id')
            ->where($where)
            ->limit($start,$rows)
            ->order("yk_boss.boss_id desc")
            ->select();
        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        foreach($data as $k => $v){
//            $data[$k]['console_time'] = date('Y-m-d H:i:s',$v['console_time']);
//            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['status'] = $change_status[$v['status']];
        }
        $count = M('Boss')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_boss.create_time,yk_boss.console_time,yk_boss.status,yk_user.mobile')
            ->join('yk_user ON yk_boss.user_id = yk_user.user_id')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    static public function getDetail($boss_id){
        $boss_id += 0;
        $data = M()->query("
            SELECT b.boss_id,b.schedule_img,b.status,u.nickname,u.realname,u.mobile,u.IDcard_number,u.user_positive,u.sex,u.user_id
            FROM yk_boss b
            LEFT JOIN yk_user u ON b.user_id = u.user_id
            WHERE b.boss_id = $boss_id
        ");
        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        $change_sex = [
            0=>'保密',
            1=>'男',
            2=>'女'
        ];
        foreach($data as $k => $v){
            $data[$k]['user_positive'] = FatherModel::$img_host.$v['user_positive'];
            $data[$k]['schedule_img'] = FatherModel::$img_host.$v['schedule_img'];
            $data[$k]['status'] = $change_status[$v['status']];
            $data[$k]['sex'] = $change_sex[$v['sex']];
        }
        $data = $data[0];
        return $data;

    }

    static public function getOrganizationDetail($boss_id){
        $boss_id += 0;
        $data = M()->query("
            SELECT b.boss_id,b.status,u.nickname,u.realname,u.mobile,u.user_id,
              b.organization,b.organizatioSummary,b.organizationImg,b.organizationAddress
            FROM yk_boss b
            LEFT JOIN yk_user u ON b.user_id = u.user_id
            WHERE b.boss_id = $boss_id
        ");
        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        foreach($data as $k => $v){
            $data[$k]['organizationImg'] = FatherModel::$img_host.$v['organizationImg'];
            $data[$k]['status'] = $change_status[$v['status']];
        }
        $data = $data[0];
//        dumpp($data);
        return $data;

    }

    //修改个人boss认证状态
    static public function changeStatus($boss_id,$user_id,$status){
        $status += 0;
        $boss_id += 0;
        $BossModel = M("Boss"); // 实例化User对象
        // 要修改的数据对象属性赋值
        $data['boss_id'] = $boss_id;
        $data['user_id'] = $user_id;
        $data['status'] = $status;
        $BossModel->save($data);
    }


    //向用户推送消息个人boss认证消息
    static public function sendMessage($user_id,$status,$options,$reason){
        $hx = new HXController($options);
        if(!$reason && $status == 3){
            $reason = '您的个人boss认证因为'.$reason.'的原因认证失败，请重新进行个人boss认证';
            $hx->sendText('admin','users',array("bos$user_id"),$reason);
        }
        if($status == 2){
            $hx->sendText('admin','users',array("bos$user_id"),'恭喜您已经通过个人boss认证，您现在已经可以发布通告了');
        }
    }

    //向用户推送消息机构boss认证消息
    static public function sendOrganizationMessage($user_id,$status,$options,$reason){
        $hx = new HXController($options);
        if(!$reason && $status == 3){
            $reason = '您的机构boss认证因为'.$reason.'的原因认证失败，请重新进行机构boss认证';
            $hx->sendText('admin','users',array("bos$user_id"),$reason);
        }
        if($status == 2){
            $hx->sendText('admin','users',array("bos$user_id"),'恭喜您已经通过机构boss认证，您现在已经可以发布通告了');
        }
    }
}