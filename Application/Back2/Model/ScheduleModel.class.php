<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/2 0002
 * Time: 12:59
 */
namespace Back2\Model;

use Think\Model;
use Back\Controller\HXController;
class ScheduleModel extends Model
{
    protected $tableName = 'yk_schedule';
    //返回通告审核页面数据

//    验证规则
    protected $_validate = array(
        array('user_id','require','必须有用户id！'), //默认情况下用正则进行验证
        array('schedule_title','','require','必须有通告标题！'), // 在新增的时候验证name字段是否唯一
        array('schedule_type','','require','必须选择通告类型！'), // 在新增的时候验证name字段是否唯一
        array('valcode','','require','必须有验证码！'), // 在新增的时候验证name字段是否唯一
        array('address','','require','必须有活动地址！'), // 在新增的时候验证name字段是否唯一
        array('schedule_content','','require','必须有通告内容！'), // 在新增的时候验证name字段是否唯一
    );

    static public function audit($page,$rows,$start_time,$end_time,$status,$search_key){
        $start = $rows * ($page-1);
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "s.schedule_title like '%{$search_key}%'",
            "s.status in ($status)",
            "s.createtime between $start_time_stamp and $end_time_stamp",
        ];
        $data = M('Schedule s')
            ->field('s.schedule_id,u.nickname,s.schedule_title,s.createtime,s.acttime,s.status')
            ->join('yk_user u on s.user_id = u.user_id','LEFT')
            ->where($where)
            ->order("s.createtime desc")
            ->limit($start,$rows)
            ->select();
        $change_status = [
            0=>'审核中',
            1=>'招募中',
            2=>'已截止',
            3=>'未通过'
        ];
        foreach($data as $k => $v){
            $data[$k]['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
            $data[$k]['acttime'] = date('Y-m-d H:i:s', $v['acttime']);
            $data[$k]['status'] = $change_status[$data[$k]['status']];
        }
        $count =  M('Schedule s')
            ->join('yk_user u on s.user_id = u.user_id','LEFT')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    //返回报名统计页面数据
    static public function statistics($page,$rows,$start_time,$end_time,$status,$search_key)
    {
        $start = $rows * ($page-1);
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "s.status = $status",
        ];
        $data = M()->query("
            SELECT s.schedule_id, u.nickname, s.schedule_title, s.createtime, s.acttime, us1.enroll_num, us2.invite_num, us3.attend_num
            FROM yk_schedule s
            LEFT JOIN yk_user u ON s.user_id = u.user_id
            LEFT JOIN(
                SELECT schedule_id,count(*) enroll_num FROM yk_user_schedule GROUP BY schedule_id
            ) us1 ON s.schedule_id = us1.schedule_id
            LEFT JOIN(
                SELECT schedule_id,count(*) invite_num FROM yk_user_schedule WHERE schedule_status in(2,3) GROUP BY schedule_id 
            ) us2 ON s.schedule_id = us2.schedule_id
            LEFT JOIN(
                SELECT schedule_id,count(*) attend_num FROM yk_user_schedule WHERE schedule_status = 3 GROUP BY schedule_id 
            ) us3 ON s.schedule_id = us3.schedule_id
            WHERE s.schedule_title LIKE '%{$search_key}%'
            ORDER BY s.createtime DESC
            limit $start,$rows
        ");
        foreach($data as $k => $v){
            $data[$k]['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
            $data[$k]['acttime'] = date('Y-m-d H:i:s', $v['acttime']);
            if(!$v['enroll_num']){
                $data[$k]['enroll_num'] = 0;
            }
            if(!$v['invite_num']){
                $data[$k]['invite_num'] = 0;
            }
            if(!$v['attend_num']){
                $data[$k]['attend_num'] = 0;
            }
        }
        $count =  M('Schedule s')
            ->where("s.schedule_title LIKE '%{$search_key}%'")
            ->count();

        $total_enroll = M('User_schedule')->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        $arr['total_schedule'] = $count;
        $arr['total_enroll'] = $total_enroll;
        return $arr;
    }

//    查看负约人名单
    static public function notAttendList($schedule_id,$page,$rows){
        $schedule_id += 0;
        $page += 0;
        $rows += 0;
        $start = ($page-1) * $rows;
        $data['list'] = M('User_schedule us')
            ->field('u.user_id,u.realname,u.nickname,u.user_img,u.mobile')
            ->join('yk_user u ON us.user_id = u.user_id','LEFT')
            ->where("us.schedule_id = $schedule_id and us.schedule_status = 2")
            ->limit($start,$rows)
            ->select();
        $count = M('User_schedule us')
            ->field('u.user_id,u.realname,u.nickname,u.user_img,u.mobile')
            ->join('yk_user u ON us.user_id = u.user_id','LEFT')
            ->where("us.schedule_id = $schedule_id and us.schedule_status = 2")
            ->count();
        $data['count'] = ceil($count/$rows);
        if(!$data['list']){
            $data['exist'] = 0;
            return $data;
        }else{
            $data['exist'] = 1;
            foreach($data as $k => $v){
                $data['list'][$k]['user_img'] = FatherModel::$img_host.$data[$k]['user_img'];
            }
            return $data;
        }
    }

    //    查看全部报名用户列表
    static public function enrollList($page,$rows,$schedule_id){
        $schedule_id += 0;
        $rows += 0;
        $start = $rows * ($page-1);
        $data = M('')->query("
            SELECT u.user_id,u.nickname,u.realname,u.user_img,u.mobile,enroll.enroll_num,
              neglect.neglect,invite.invite_num,break.break_num,attend.attend_num
            FROM yk_user_schedule us 
            LEFT JOIN yk_user u ON us.user_id = u.user_id
            LEFT JOIN(
                SELECT user_id,count(*) enroll_num 
                FROM yk_user_schedule 
                GROUP BY user_id
            ) enroll ON us.user_id = enroll.user_id
            LEFT JOIN(
                SELECT user_id,count(*) neglect 
                FROM yk_user_schedule 
              WHERE schedule_status = 0
                GROUP BY user_id
            ) neglect ON us.user_id = neglect.user_id
            LEFT JOIN(
                SELECT user_id,count(*) invite_num 
                FROM yk_user_schedule 
                WHERE schedule_status IN (2,3)
                GROUP BY user_id
            ) invite ON us.user_id = invite.user_id
            LEFT JOIN(
                SELECT user_id,count(*) break_num 
                FROM yk_user_schedule 
                WHERE schedule_status = 2
                GROUP BY user_id
            ) break ON us.user_id = break.user_id
            LEFT JOIN(
                SELECT user_id,count(*) attend_num 
                FROM yk_user_schedule 
                WHERE schedule_status = 3
                GROUP BY user_id
            ) attend ON us.user_id = attend.user_id
            WHERE us.schedule_id = {$schedule_id}
            LIMIT {$start},{$rows}
        ");
        foreach($data as $k => $v){
            $data[$k]['user_img'] = FatherModel::$img_host.$data[$k]['user_img'];
            if(!isset($data[$k]['invite_num'])){
                $data[$k]['invite_num'] = 0;
            }
            if(!isset($data[$k]['break_num'])){
                $data[$k]['break_num'] = 0;
            }
            if(!isset($data[$k]['neglect'])){
                $data[$k]['neglect'] = 0;
            }
            if(!isset($data[$k]['attend_num'])){
                $data[$k]['attend_num'] = 0;
            }
        }
        $where = [
            'schedule_id' => $schedule_id
        ];
        $count = M('User_schedule')->where($where)->count();
        $arr['list'] = $data;
        if(!$arr['list']){
            $arr['exist'] = 0;
        }{
            $arr['exist'] = 1;
        }
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    //查看该通告下的所有面试（列表）
    static public function interviewList($page,$rows,$schedule_id)
    {
        $schedule_id += 0;
        $rows += 0;
        $start = $rows * ($page-1);
        $data = M('')->query("
            SELECT i.inter_id,s.schedule_title,u.nickname,FROM_UNIXTIME(i.interviewtime,\"%Y-%m%-%d %H:%i\") interviewtime,i.address,i.status,u.user_img,u.user_id
            FROM yk_interview i
            LEFT JOIN yk_schedule s ON i.schedule_id = s.schedule_id
            LEFT JOIN yk_user u ON i.to_user_id = u.user_id
            WHERE i.schedule_id = $schedule_id
            LIMIT $start,$rows
        ");
        $status = [1=>'已拒绝',2=>'已接受',3=>'已结束',];
        foreach($data as $k => $v){
            $data[$k]['user_img'] = FatherModel::$img_host.$data[$k]['user_img'];
            $data[$k]['status'] =$status[$v['status']];
        }
        $where = [
            'schedule_id' => $schedule_id
        ];
        $count = M('Interview')->where($where)->count();
        $arr['list'] = $data;
        if(!$arr['list']){
            $arr['exist'] = 0;
        }{
        $arr['exist'] = 1;
    }
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }


    static public function scheduleDetail($schedule_id){
        $data = M()->query("
            SELECT s.schedule_id,s.schedule_img,s.schedule_title,u.nickname,c.city_name,s.status,u.user_id,
                FROM_UNIXTIME(s.acttime, '%Y-%m-%d') act_time,s.sex,s.schedule_type,s.schedule_content
            FROM yk_schedule s
            LEFT JOIN yk_user u ON s.user_id = u.user_id
            LEFT JOIN yk_city c ON s.city = c.city_id
            WHERE s.schedule_id = $schedule_id
        ");
        $sex = [
            0 => '不限',
            1 => '男',
            2 => '女',
        ];
        $schedule_type = [
            0 => '影视通告',
            1 => '模特招募',
            2 => '群众演员',
            3 => '主播',
            4 =>  '主持',
            5 => '歌手',
        ];
        $change_status = [
            0=>'审核中',
            1=>'招募中',
            2=>'已截止',
            3=>'未通过'
        ];
        foreach($data as $k => $v){
            $data[$k]['sex'] = $sex[$v['sex']];
            $data[$k]['schedule_type'] = $schedule_type[$v['schedule_type']];
            $data[$k]['status'] = $change_status[$v['status']];
            $data[$k]['schedule_img'] = FatherModel::$img_host.$data[$k]['schedule_img'];
        }
        $data = $data[0];
        return $data;
    }

    static public function scheduleEdit($schedule_id){
        $data = M()->query("
            SELECT s.schedule_id,s.schedule_img,s.schedule_title,u.nickname,c.city_name,s.status,u.user_id,
                FROM_UNIXTIME(s.acttime, '%Y-%m-%d') act_time,s.sex,s.schedule_type,s.schedule_content
            FROM yk_schedule s
            LEFT JOIN yk_user u ON s.user_id = u.user_id
            LEFT JOIN yk_city c ON s.city = c.city_id
            WHERE s.schedule_id = $schedule_id
        ");
        foreach($data as $k => $v){
            $data[$k]['schedule_img'] = FatherModel::$img_host.$data[$k]['schedule_img'];
        }
        $data = $data[0];
        return $data;
    }

    static public function changeStatus($schedule_id,$status){
        $status += 0;
        $schedule_id += 0;
        $ScheduleModel = M("Schedule");
        $data['schedule_id'] = $schedule_id;
        $data['status'] = $status;
        //下面这行代码，不知道为啥就是过不去，status字段改不了？audit就可以？永远卡在这里~!!??
        $ScheduleModel->save($data);
    }

    //向用户推送消息
    static public function sendMessage($user_id,$status,$options,$reason){
        $hx = new HXController($options);
        if($status == 3){
            $reason = '您的通告审核因为'.$reason.'的原因失败，请重新编辑通告';
            $hx->sendText('admin','users',array("bos$user_id"),$reason);
        }
        if($status == 1){
            $hx->sendText('admin','users',array("bos$user_id"),'恭喜您，您的通告已通过审核！');
        }
    }

}
