<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31 0031
 * Time: 14:34
 */
namespace Back2\Model;

use Think\Model;
use Back\Controller\HXController;

class YanyiModel extends Model
{
    protected $tableName = 'yk_yanyiaudit';

    static public function getData($page, $rows, $start_time, $end_time, $status, $search_key){
        $start = $rows * ($page-1);
        //时间字符串转换为时间戳
        $start_time_stamp = strtotime($start_time);
        $end_time_stamp = strtotime($end_time)+86400;
        $where = [
            "yk_yanyiaudit.create_time between $start_time_stamp and $end_time_stamp",
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%' or yk_user.mobile like '%{$search_key}%'",
            "yk_yanyiaudit.status in ($status)",
        ];
        $data = M('Yanyiaudit')
            ->field('yk_yanyiaudit.*,yk_user.realname,yk_user.mobile,yk_user.IDcard_number,yk_user.nickname')
            ->join('yk_user on yk_yanyiaudit.user_id = yk_user.user_id','LEFT')
            ->where($where)
            ->order('yk_yanyiaudit.create_time desc')
            ->limit($start,$rows)
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
        $count = M('Yanyiaudit')
            ->field('yk_yanyiaudit.*,yk_user.realname,yk_user.mobile,yk_user.IDcard_number')
            ->join('yk_user on yk_yanyiaudit.user_id = yk_user.user_id','LEFT')
            ->where($where)
            ->count();

        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;
    }

    static public function getDetail($yanyi_id){
        $yanyi_id += 0;
        $data = M()->query("
            SELECT ya.yanyi_id,u.user_id,u.realname,u.nickname,u.mobile,u.IDcard_number,u.user_type,u.sex,u.threedimensional,u.user_img,
                FROM_UNIXTIME(u.birthday, '%Y-%m-%d') user_birthday,u.signature,u.user_address,u.school,u.height,u.weight,
                u.user_positive,ya.status,group_concat(y.yanyi_img) imgs,GROUP_CONCAT(ut.type) user_type_concat
            FROM yk_yanyiaudit ya
            LEFT JOIN yk_user u ON ya.user_id = u.user_id
            LEFT JOIN yk_yanyi y on ya.yanyi_id = y.yanyi_id
            LEFT JOIN yk_user_type ut on ya.user_id = ut.user_id
            WHERE ya.yanyi_id = {$yanyi_id}
            GROUP BY ya.user_id
            LIMIT 1
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
        $change_user_type = [
            0=>'游客',
            1=>'演员',
            2=>'导演',
        ];
        foreach($data as $k => $v){
            $data[$k]['user_positive'] = FatherModel::$img_host.$v['user_positive'];
            $data[$k]['user_img'] = FatherModel::$img_host.$v['user_img'];
            $data[$k]['status'] = $change_status[$v['status']];
            $data[$k]['sex'] = $change_sex[$v['sex']];
            $data[$k]['user_type'] = $change_user_type[$v['user_type']];
            $data[$k]['imgs'] = explode(',',$data[$k]['imgs']);
            foreach($data[$k]['imgs'] as $k1 => $v1){
                $data[$k]['imgs'][$k1] = FatherModel::$img_host.$data[$k]['imgs'][$k1];
            }
//            echo $data[$k]['user_type_concat'];die;
            $data[$k]['user_type_concat'] = str_replace(
                ['1','2','3','4','5','6','7','8'],
                ['演员', '模特', '主播', '校园艺人', '童星', '主持人', '歌星', '舞者',],
                $data[$k]['user_type_concat']
            );
        }



        $data = $data[0];
        return $data;

    }

    static public function changeStatus($yanyi_id,$status,$reason){
        $YanyiauditModel = M("Yanyiaudit"); // 实例化User对象
        // 要修改的数据对象属性赋值
        $data['yanyi_id'] = $yanyi_id;
        $data['status'] = $status;
        $data['console_time'] = time();
        $data['yanyi_rea'] = $reason;
        $YanyiauditModel->save($data);
    }

    //向用户推送消息
    static public function sendMessage($user_id,$status,$options,$reason){
        $hx = new HXController($options);
        if(!$reason && $status == 3){
            $reason = '您的演绎认证因为'.$reason.'的原因认证失败，请重新进行演绎认证';
            $hx->sendText('admin','users',array("$user_id"),$reason);
        }
        if($status == 2){
            $hx->sendText('admin','users',array("$user_id"),'恭喜您已经通过演绎认证，已经可以报名通告啦！请完善您的个人信息方便剧组查看，并把演绎资料发送至邮箱Andy@yankushidai.com');
        }
    }

    static public function setLiveRoom($user_id,$status,$options){
        $roomModel = M('Room');
        $yanyiauditModel = D('Yanyiaudit');
        $push = 'rtmp://push.yankushidai.com/live/';
        $pull = 'http://pull.yankushidai.com/live/';
        $stream_id = substr(md5(time()),1,16);
        $stream = array(
            "pc_pull"=>"http://hls.yankushidai.com/live/$stream_id/index.m3u8", //PC端拉流地址
            "pc_push"=>"rtmp://push.yankushidai.com/live/$stream_id?vdoid=".time(), //PC端推流地址
            "mobile_push"=>"rtmp://push.yankushidai.com/live/$stream_id?vdoid=".time(), //MOBILE端拉流地址
            "mobile_pull"=>"http://hdl.yankushidai.com/live/$stream_id.flv", //MOBILE端推流地址
        );
        $rname = $yanyiauditModel
            ->field('yk_user.user_id,yk_user.realname')
            ->join('yk_user on yk_yanyiaudit.user_id = yk_user.user_id')
            ->where("yk_user.user_id = $user_id")
            ->find();
        $rname = implode('',$rname);
        $data['user_id'] = $user_id;
        $data['room_id'] = substr(time(),-5);
        $time = time();
        $data['room_name'] = $rname.'的直播间';
        $data['push'] = $stream['mobile_push'];
        $data['pull'] = $stream['mobile_pull'];
        $hx = new HXController($options);
        if($status == 2){
            $hx->setLiveroomsstream($stream);
            $ins = array("superadmin"=>$user_id);
            $result = $hx->setChatroomsuper($ins);
            $roomModel->add($data);
//            $yanyiauditModel->query("update yk_yanyiaudit set status='2',console_time = $time WHERE user_id=$user_id");

        }

    }
}
