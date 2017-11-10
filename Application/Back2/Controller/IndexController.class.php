<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');
use Think\Controller;
use Back2\Model\FatherModel;
use Back2\Model\UserModel;


class IndexController extends Controller
{
    public function index(){
        $data = [];
//        注册数量：
        $data['register_num'] = M('User')->where('registertime >= 0')->count();

//        艺人数量
        $data['artist_num'] = M('yanyiaudit ya')->where('status=2')->count();
//        演员数量：
        $data['actor_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=1 AND ya.`status`=2')->count();
//        模特数量：
        $data['model_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=2 AND ya.`status`=2')->count();
//        主播数量：
        $data['anchor_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=3 AND ya.`status`=2')->count();
//        校园艺人数量：
        $data['campusartist_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=4 AND ya.`status`=2')->count();
//        童星数量：
        $data['childstar_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=5 AND ya.`status`=2')->count();
//        主持人数量：
        $data['compere_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=6 AND ya.`status`=2')->count();
//        歌星数量：
        $data['singer_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=7 AND ya.`status`=2')->count();
//        舞者数量：
        $data['dancer_num'] = M('User_type ut')->join('yk_yanyiaudit ya ON ut.user_id = ya.user_id','LEFT')->where('ut.type=8 AND ya.`status`=2')->count();

//        发通告数量：
        $data['schedule_num'] = M('Schedule')->count();

//        待审核通告数量：
        $data['schedule_waiting'] = M('Schedule')->where("status = 0")->count();

//        个人boss数量：
        $data['boss_personal'] = M('Boss')->where("status = 2 AND organizationImg = ''")->count();

//        机构boss数量：
        $data['boss_organization'] = M('Boss')->where("status = 2 AND organization != '北京演库网络科技有限公司' AND organizationImg != ''")->count();

//        正在直播的数量----------------------------------------------------------
        $LivelistController = new LivelistController();
        $data['video_num'] = $LivelistController->num();

//        报名数量：
        $data['enroll_num'] = M('User_schedule')->count();

//        应约数量：
        $data['invited_num'] = M('User_schedule')->where('schedule_status = 3')->count();

//        负约数量：
        $data['break_num'] = M('User_schedule')->where('schedule_status = 2')->count();

//        待实名审核数量：
        $data['identity_wating_num'] = M('Nameaudit')->where('status = 1')->count();

//        充值总金额：
        $data['recharge_totalprice'] = M('Recharge')->where('pay_status = 1 and pay_type = 0')->sum('amount');

//        待演绎审核数量：
        $data['yanyi_wating_num'] = M('Yanyiaudit')->where('status = 1')->count();

//        充值来源（微信，支付宝，苹果）：
        $data['recharge_channel'] = '???';

//        提现数据（提现金额，成功金额,待处理提现的主播数量,待处理金额）：
        $data['withdraw_total_price'] = M('Recharge')->where('(pay_status = 1 and pay_type = 1) or (pay_status = 0 and pay_type = 1)')->sum('amount');
        $data['withdraw_success_price'] = M('Recharge')->where('pay_status = 1 and pay_type = 1')->sum('amount');
        $withdraw_wating_anchor_num = M()->query("SELECT * FROM yk_recharge WHERE pay_status = 0 and pay_type = 1 and amount != 0 GROUP BY user_id ");
        $data['withdraw_wating_anchor_num'] = count($withdraw_wating_anchor_num);
        $data['withdraw_wating_price'] = M('Recharge')->where('pay_status = 0 and pay_type = 1')->sum('amount');

//        热门主播头像、昵称和关注数：
        $data['hot_anchor'] = M()->query("
            SELECT attn.attu_id,`user`.nickname,count(*) num,`user`.user_img
            FROM yk_attention attn
            LEFT JOIN yk_user user
            ON attn.attu_id = `user`.user_id
            GROUP BY attu_id 
            ORDER BY num DESC 
            LIMIT 4");

        foreach($data['hot_anchor'] as $k => $v){
            $data['hot_anchor'][$k]['user_img'] = FatherModel::$img_host.$v['user_img'];
        }

//        热门报名和报名数：
        $data['hot_enroll'] = M()->query("
           SELECT s.schedule_id,s.schedule_title,count(*) num
            FROM yk_user_schedule us
            LEFT JOIN yk_schedule s
            ON us.schedule_id = s.schedule_id
            GROUP BY us.schedule_id
            ORDER BY num DESC
            LIMIT 4");

//        通告：
        $data['schedule'] = M()->query("
            SELECT `user`.nickname,sc.schedule_title,FROM_UNIXTIME(sc.createtime, '%Y-%m-%d') create_time
            FROM yk_schedule sc
            LEFT JOIN yk_user user
            ON sc.user_id = `user`.user_id
            ORDER BY create_time DESC
            limit 9");

//        echo '<pre>';print_r($data) ;die;
        echo json_encode($data);
    }
}