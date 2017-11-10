<?php
namespace Back2\Model;
use Think\Model;
use Back\Controller\HXController;


class UserModel extends Model
{
    protected $tableName = 'yk_user';
    static public function getData($page, $rows, $start_time, $end_time, $search_key,$status){
        $start = ($page - 1) * $rows;
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;

        $where = [
            "u.registertime between $start_time_stamp and $end_time_stamp",
            "u.user_id like '%{$search_key}%' or u.nickname like '%{$search_key}%' or u.realname like '%{$search_key}%' or u.mobile like '%{$search_key}%'",
            "u.status IN ($status)"
        ];
        $data = M()
            ->query("
                select u.user_id,u.realname,u.nickname,u.user_img,u.kubi,u.registertime,u.lasttime,u.lastip,xxx.total_price,u.status
                from yk_user u
                left join
                (select d.*,sum(g.price) total_price
                from yk_deal d
                left join yk_gift g on d.gift_type = g.gift_type
                group by d.from_user_id) xxx
                on u.user_id = xxx.from_user_id
                where (u.registertime between {$start_time_stamp} and {$end_time_stamp})
                and (u.user_id like '%{$search_key}%' or u.nickname like '%{$search_key}%' or u.realname like '%{$search_key}%' or u.mobile like '%{$search_key}%')
                and u.status IN ({$status})
                order by user_id desc
                limit {$start},{$rows}
            ");
        $change_status = [
            0=>'正常',
            1=>'拉黑',
        ];
        foreach($data as $k => $v){
            $data[$k]['registertime'] = date('Y-m-d H:i:s',$v['registertime']);
            $data[$k]['lasttime'] = date('Y-m-d H:i:s',$v['lasttime']);
            $data[$k]['user_img'] = FatherModel::$img_host.$data[$k]['user_img'];
            $data[$k]['status'] = $change_status[$v['status']];
            if(!$v['total_price']){
                $data[$k]['total_price'] = 0;
            }
        }
        $count =M('User u')
            ->where($where)
            ->count();
//        echo $count;die;
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    //身份认证详情
    static public function getDetail($id){
        $id += 0 ;  //强转为数字
        $data = M()->query("
            SELECT n.id,u.user_id,u.realname,u.nickname,u.mobile,u.IDcard_number,u.user_positive,n.status,u.user_back
            FROM yk_nameaudit n
            LEFT JOIN yk_user u ON n.user_id = u.user_id
            WHERE n.id = {$id}
            LIMIT 1
        ");

        $change_status = [
            1=>'审核中',
            2=>'成功',
            3=>'失败'
        ];
        foreach($data as $k => $v){
            $data[$k]['user_positive'] = FatherModel::$img_host.$v['user_positive'];
            $data[$k]['user_back'] = FatherModel::$img_host.$v['user_back'];
            $data[$k]['status'] = $change_status[$v['status']];
        }

        $data = $data[0];
        return $data;
    }

//    身份认证审核
    static public function changeStatus($user_id,$status){
        if($status == 2){ //在提交过来的表单中，2表示通过认证
            $nameaudit = 2; //在yk_user表中，1表示通过实名认证
        }else{
            $nameaudit = 0; //在yk_user表中，0表示通过实名认证
        }
        $UserModel = M("User"); // 实例化User对象
        $UserModel->query("update yk_user set nameaudit=$nameaudit WHERE user_id=$user_id");
    }

    //查看该用户的所有报名记录
    static public function enrollList($page,$rows,$user_id){
        $user_id += 0;
        $rows += 0;
        $start = ($page - 1) * $rows;
        $data = M()->query("
            SELECT s.schedule_id,s.schedule_title,us.schedule_status
            FROM yk_user_schedule us 
            LEFT JOIN yk_schedule s ON us.schedule_id = s.schedule_id
            WHERE us.user_id = {$user_id}
            LIMIT {$start},{$rows}
        ");
        $schedule_status = [
            0=>'未受理',
            1=>'拒绝',
            2=>'受邀参加',
            3=>'已参加'
        ];
        foreach($data as $k => $v){
            $data[$k]['schedule_status'] = $schedule_status[$v['schedule_status']];
        }
        $count = M('User_schedule us')->where("us.user_id = $user_id")->count();
        $arr['list'] = $data;
        if(!$arr['list']){
            $arr['exist'] = 0;
        }else{
            $arr['exist'] = 1;
        }
        $arr['count'] = ceil($count/$rows);
        return $arr;

    }
    //查看该用户的所有面试
    static public function interviewList($page,$rows,$user_id){
        $user_id += 0;
        $rows += 0;
        $start = ($page - 1) * $rows;
        $data = M()->query("
            SELECT i.inter_id,s.schedule_title,u.nickname,FROM_UNIXTIME(i.interviewtime,\"%Y-%m%-%d %H:%i\") interviewtime,i.address,i.status,u.user_img,i.mark
            FROM yk_interview i
            LEFT JOIN yk_user u ON i.user_id = u.user_id
            LEFT JOIN yk_schedule s ON i.schedule_id = s.schedule_id
            WHERE i.to_user_id = $user_id
            LIMIT $start,$rows
        ");
        $status = [
            0=>'未受理',
            1=>'已拒绝',
            2=>'已接受',
            3=>'已结束'
        ];
        foreach($data as $k => $v){
            $data[$k]['user_img'] = FatherModel::$img_host.$data[$k]['user_img'];
            $data[$k]['status'] =$status[$v['status']];
        }
        $count = M('interview')->where("to_user_id = $user_id")->count();
        $arr['list'] = $data;
        if(!$arr['list']){
            $arr['exist'] = 0;
        }else{
            $arr['exist'] = 1;
        }
        $arr['count'] = ceil($count/$rows);
        return $arr;

    }


    static public function getUserImg($user_id){
        $data = M('User')->field('user_img')->where("user_id = $user_id")->limit(1)->select();
        $img = $data[0]['user_img'];
        $img =  FatherModel::$img_host.$img;
        return $img;
    }

    //审核用户头像
    static public function imgAudit($user_id,$status){
//        echo $user_id;
//        echo '<br>';
//        echo $status;die;
        if($status == 2){
            $img = 'User/2017-06-09/593a5f8415bd3.jpg';
            $UserModel = new UserModel();
            $UserModel->query("update yk_user set user_img = '{$img}' WHERE user_id=$user_id");
        }

    }

    //向XX用户推送消息
    static public function sendMessage($user_id,$status,$options){
        $hx = new HXController($options);
        if($status == 2){
            $content = '您的头像不符合国家法律规定，系统为您重新设置了默认头像';
            $hx->sendText('admin','users',array("$user_id"),$content);
        }
    }

    //向全体用户推送消息
    static public function sendMessageToAllUsers($options,$content,$group){
        $hx = new HXController($options);
        //手动查找需要发送的用户群体
        if($group == 1){//全部用户
            $user_ids = M('User')->field('user_id')->where("user_id != 1")->select();
//            $user_ids = M('User')->field('user_id')->where("user_id in (698,116950,116216)")->select();
//            echo 1;die;
        }elseif($group == 2){//全部艺人(通过演绎认证的用户)
            $user_ids = M('yanyiaudit')->field('DISTINCT(user_id)')->where("status = 2")->select();
        }elseif($group == 3){//全部boss(通过boss用户的认证)
            $user_ids = M('boss')->field('DISTINCT(user_id)')->where("status = 2")->select();
        }else{
            echo json_encode(array('status'=>'发送失败，没有选定用户群组'));die;
        }

        $arr = [];
        foreach($user_ids as $k => $v){
            $arr[] = $v['user_id'];
        }

        //测试用
//        $arr = $user_ids;

//        dumpp($arr);die;
        $hx->sendText('admin','users',$arr,$content);
    }


}
