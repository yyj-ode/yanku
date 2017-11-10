<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/4/24 0024
 * Time: 11:02
 */

namespace Version2\Controller;
use Version2\Model\UserModel;
use Version2\Controller;
//echo '<meta http-equiv="Access-Control-Allow-Origin" content="apii.yankushidai.com">';
header('Access-Control-Allow-Origin:*');
header ( "X-FRAME-OPTIONS:DENY");
class MyController extends ResController
{
    private $appId = 'wx9319ce05d8127b23';
    private $appSecret = 'e27db0e684bb7e4dccb6e0b33c06b31c';
//    private $count = array('result'=>1,'message'=>'succcess','data'=>'');
    /*
     * 用户页面---我的信息（固定）
     */
//    public function myIndex(){
//        $my = M('user');
//        $user_id = I('get.user_id');
//        $where['user_id'] = $user_id;
//        if (isset($user_id)){
//        $this->count['data'] = $my->field('user_id,user_img,nickname,signature,level')
//                            ->where($where)
//                            ->select();
//        }else{
//            $this->count['message']='参数不全';
//        }
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 用户页面---我的主页标签（固定）
//     */
//    public function mine(){
//        $user_id = I('get.user_id');
//        //动态三张图
//        //实例化动态模型
//        $dynamic = M('dynamic');
//        $this->count['data']['dynamic'] = $dynamic->field('yk_dynamic_img.dynamic_img')
//                                           ->join('LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id')
//                                           ->where("yk_dynamic.user_id=$user_id")
//                                           ->order('yk_dynamic.dynamic_createtime')
//                                           ->limit(3)
//                                           ->select();
//        //粉丝贡献榜前三名
//        //实例化粉丝贡献模型
//        $deal = M('deal');
//        $this->count['data']['money'] = $deal->field('yk_user.user_id,yk_user.user_img,SUM(yk_gift.price*yk_deal.number) as deal')
//                                      ->join('LEFT JOIN yk_gift ON yk_deal.gift_type=yk_gift.gift_type LEFT JOIN yk_user ON yk_user.user_id=yk_deal.from_user_id')
//                                      ->where("yk_deal.to_user_id=$user_id")
//                                      ->group('yk_deal.from_user_id,yk_deal.to_user_id')
//                                      ->order('deal DESC')
//                                      ->limit(3)
//                                      ->select();
//        //我的粉丝
//        //实例化关注模型
//        $deal = M('attention');
//        $this->count['data']['fans'] = $deal->field('yk_user.user_id,yk_user.user_img')
//                                    ->join('LEFT JOIN yk_user ON yk_user.user_id=yk_attention.user_id')
//                                    ->where("yk_attention.attu_id=$user_id")
//                                    ->order('RAND()')
//                                    ->limit(3)
//                                    ->select();
//        //我的粉丝
//        //实例化关注模型
//        $schedule = M('user_schedule');
//        $this->count['data']['schedule'] = $schedule->field('COUNT(user_id) as all_schedule')
//                                             ->where("user_id=$user_id")
//                                             ->group('user_id')
//                                             ->select();
//        //格式化
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 粉丝守护（贡献）榜
//     */
//    public function fans_contribut(){
//        $user_id = I('get.user_id');
//        $deal = M('deal');
//        $this->count['data'] = $deal->field('yk_user.user_id,yk_user.user_img,yk_user.level,SUM(yk_gift.price*yk_deal.number) as deal')
//                            ->join("LEFT JOIN yk_gift ON yk_deal.gift_type=yk_gift.gift_type LEFT JOIN yk_user ON yk_user.user_id=yk_deal.from_user_id")
//                            ->where("yk_deal.to_user_id=$user_id")
//                            ->group("yk_deal.from_user_id,yk_deal.to_user_id")
//                            ->order("deal DESC")
//                            ->limit(10)
//                            ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 我的等级
//     */
//    public function my_level(){
//        $user_id = I('get.user_id');
//        $deal = M('user');
//        $this->count['data'] = $deal->field('level')
//                            ->where("user_id=$user_id")
//                            ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 粉丝列表
//     */
//    public function fans(){
//        $user_id = I('get.user_id');
//        $fan = M('attention');
//        $this->count['data'] = $fan->field('yk_user.user_img,yk_user.nickname,yk_user.signature')
//                             ->join('LEFT JOIN yk_user ON yk_attention.user_id=yk_user.user_id')
//                             ->where("yk_attention.attu_id=$user_id")
//                             ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 我参加的通告
//     * param1：分页开始条数
//     * param2：参加状态
//     */
//    public function join(){
//        $user_id = I('get.user_id');
//        $start = I('get.start',0);
//        $status = I('get.status',0);
//        $work = M('user_schedule');
//        $this->count['data']=$work->field('yk_user_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.schedule_type')
//                            ->join('LEFT JOIN yk_schedule ON yk_user_schedule.schedule_id=yk_schedule.schedule_id')
//                            ->where("yk_user_schedule.user_id=$user_id AND yk_user_schedule.schedule_status=$status")
//                            ->limit($start,5)
//                            ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 根据参加状态显示
//     * param1：起始条数
//     * param2：通告类型
//     */
//    public function join_schedule_type(){
//        $user_id = I('get.user_id');
//        $start = I('get.start',0);
//        $type = I('get.type',0);
//        $work = M('user_schedule');
//        $this->count = $work->field('yk_user_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.schedule_type')
//                     ->join('LEFT JOIN yk_schedule ON yk_user_schedule.schedule_id=yk_schedule.schedule_id')
//                     ->where("yk_schedule.schedule_type=$type AND yk_user_schedule.user_id=$user_id AND yk_schedule.user_id!=$user_id")
//                     ->limit($start,5)
//                     ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 参加通告验证
//     * param1：4位验证码
//     * param2：通告ID
//     */
//    public function join_audit(){
//        $schedule = I('get.schedule',0);//必填，参加的通告ID
//        $code = I('get.code');
//        $user_id = I('get.user_id');
//        $work = M('user_schedule');
//        $isset = $work->field('schedule_status')
//                      ->where("user_id=$user_id AND schedule_id=$schedule")
//                      ->select();
//        if($isset==2){//参加状态为2，进行判断
//            $work = M('schedule');
//            $valcode = $work->field('valcode')
//                         ->where("schedule_id=$schedule")
//                         ->select();
//            if($valcode == $code){
//                $this->count['message'] = "验证成功！";
//
//
//            }else{
//                $this->count['message'] = "验证失败！";
//
//            }
//        }elseif ($isset==3){//参加状态为3，返回已参加过该活动
//            $this->count['message'] = "您已经参加过该活动";
//        }else{//其他状态则返回未受邀参加该活动
//            $this->count['message'] = "您未受邀请参加过此活动";
//        }
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 动态列表
//     * param1：其实条数
//     */
//    public function dynamic(){
//        $user_id = I('get.user_id');
//        $start = I('get.start',0);
//        $dynamic = M('dynamic');
//        $this->count['data'] = $dynamic->field('yk_user.user_img,yk_user.nickname,yk_user.level,yk_dynamic.dynamic_id,IFNULL(yk_dynamic_img.dynamic_img,"") as dynamic_img,yk_dynamic.content,yk_dynamic.dynamic_createtime,yk_dynamic.localtion,yk_dynamic.view,yk_dynamic.favorite')
//                                       ->join('LEFT JOIN yk_user ON yk_dynamic.user_id=yk_user.user_id LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id')
//                                       ->where("yk_dynamic.user_id = $user_id")
//                                       ->group('yk_dynamic.dynamic_id')
//                                       ->limit($start,5)
//                                       ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 详细动态信息
//     * param1：动态ID
//     */
//    public function dynamic_type(){
//        $dynamic_id = I('get.dynamic_id');
//        $dynamictype = M('dynamic');
//        $this->count['data'] = $dynamictype->field()
//                                        ->join('LEFT JOIN yk_user ON yk_dynamic.user_id=yk_user.user_id LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id LEFT JOIN yk_dynamic_comments ON yk_dynamic.dynamic_id=yk_dynamic_comments.dynamic_id AND yk_user.user_id=yk_dynamic_comments.user_id')
//                                        ->where()
//                                        ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 工作经验
//     * param：user_id
//     */
//    public function work(){
//        $user_id = I('get.user_id');
//        $work = M('work');
//        $this->count['data'] = $work->field('yk_work.work_title,yk_work.start_time,yk_work.end_time,yk_work.work_img,yk_work.introduce')
//                                     ->join("LEFT JOIN yk_user ON yk_user.user_id = yk_work.user_id")
//                                     ->where("yk_user=$user_id")
//                                     ->select();
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 添加工作经验
//     * param1：用户ID
//     * param2：用户标题
//     * param3：开始时间
//     * param4：结束时间
//     * param5：工作介绍
//     */
//    public function work_insert(){
//        $user_id = I('get.user_id');
//        $work = M('work');
//        $data['user_id'] = $user_id;
//        $data['work_title'] = I('get.title');
//        $data['start_time'] = I('get.stime');
//        $data['end_time'] = I('get.etime');
//        $data['introduce'] = I('get.introduce');
//        $this->count['data'] = $work->fetchSql(true)->add($data);
//
//        $User = M("User"); // 实例化User对象
//        $data['name'] = 'ThinkPHP';
//        $data['email'] = 'ThinkPHP@gmail.com';
//        $sql = $User->fetchSql(true)->add($data);
//        echo $sql;
//        // 输出结果类似于
//        // INSERT INTO think_user (name,email) VALUES ('ThinkPHP','ThinkPHP@gmail.com')
//    }
//    /*
//     * 用户页面---我的资料标签（固定）
//     * result1:user_id
//     * result2:nickname
//     * result3:sex
//     * result4:city
//     * result5:brithday
//     * result6:height
//     * result7:weight
//     * result8:chest
//     * result9:waitline
//     * result10:buttocks
//     */
//    public function mine_data(){
//        $user_id = I('get.user_id');
//        $User = M('user');
//        $this->count['data'] = $User->field('user_id,nickname,sex,city,birthday,height,weight,chest,waistline,buttocks,signature')
//                             ->where("user_id=$user_id")
//                             ->select();
//        $this->count['data'] = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 修改个人资料
//     * param1：user_id（必须）
//     * param2：nickname（非必须）
//     * param3：sex（非必须）
//     * param4：city（非必须）
//     * param5：birthday（非必须）
//     * param6：height（非必须）
//     * param7：weight（非必须）
//     * param8：chest（非必须）
//     * param9：waistline（非必须）
//     * param10：buttocks（非必须）
//     * param11：signature（非必须）
//     * result1：修改成功
//     * result0：修改失败
//     */
//    public function change_mine_data(){
//        $user_id = I('post.user_id');
//        $User = M('user');
//        foreach ($_POST as $key=>$value){
//            $data[$key] = $value;
//        }
//        unset($data['user_id']);
//        $this->count['result'] = $User->where("user_id=$user_id")->save($data); // 根据条件更新记录
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 演绎资产
//     * param1：user_id
//     */
//    public function asset(){
//        $user_id = I('get.user_id');
//        $User = M('user_schedule');
//        $count = $User->field('COUNT(*) as enroll')
//                       ->where("user_id=$user_id")
//                                                ->select();
//        $this->count['data'][] = $count[0];
//
//        $count = $User->field('COUNT(*) as joined')
//                      ->where("user_id=$user_id AND schedule_status=3")
//                      ->select();
//        $this->count['data'][] = $count[0];
//
//        $arr= $User->field('count(*)')
//                  ->where("user_id=$user_id and schedule_status>1")
//                  ->select();
//        $allin = (int)$arr[0]['count(*)'];
//        $arr = $User->field('count(*)')
//                    ->where("user_id=$user_id and schedule_status=3")
//                    ->select();
//        $joined = (int)$arr[0]['count(*)'];
//        $credit = $joined/$allin*100;
//        $credit .= '%';
//        $this->count['data'][] = array('credit'=>"$credit");
//
//        $User = M('user');
//        $count = $User->field('acquirement')
//                      ->where("user_id = $user_id")
//                      ->select();
//        $this->count['data'][] = $count[0];
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//    /*
//     * 更改才艺特长
//     * param1：user_id
//     * param2：acquirement
//     */
//    public function change_acquirement(){
//        $user_id = I('post.user_id');
//        $User = M('user');
//        $User->acquirement = I('post.acquirement');
//        $this->count['result'] = $User->where("user_id=$user_id")->save(); // 根据条件更新记录
//        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
//        echo $this->count;
//    }
//
//    public function shi(){
//        require 'vendor/autoload.php';
//        $app_key = c4e4f4e1b5b29c8a3f85811e;
//        $master_secret = c9dc57419e7ed0f48ec37549;
//        $client = new \JPush\Client($app_key, $master_secret);
//        $client->push()
//            ->setPlatform('all')
//            ->addAllAudience(1)
//            ->setNotificationAlert('Hello, 金丰小婊砸')
//            ->send();
//
//    }
    public function schedule(){
        $con['user_id'] = I('post.user_id',310);
        $con['schedule_title'] = I('post.title');
        $con['schedule_type'] = I('post.schedule_type');
        $con['createtime'] = strtotime("now");
        $con['address'] = I('post.address');
        $con['acttime'] = strtotime(I('post.acttime'));
        if($con['createtime'] >= $con['acttime']){
            die;
        }
        $con['valcode'] = rand(1000,9999);
        $city = I('post.city');
        $where['city_name'] = $city;
        $con['sex'] = I('post.sex');
        $con['schedule_content'] = I('post.schedule_content','');
        $con['audit'] = I('post.audit');

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
        $upload->savePath  =     'Schedule/';
        // 上传单个文件
//        $info   =   $upload->uploadOne($_FILES['image']);
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(0);
            die();
        }else{// 上传成功 获取上传文件信息
            foreach ($info as $k=>$v){
                if ($k=='image'){
                    $up = $v['savepath'].$v['savename'];
                    $con['schedule_img'] = $this->uploadOss($up);
                }elseif($k=='content'){
                    $up = $v['savepath'].$v['savename'];
                    $con['schedule_content'] = '<URL>http://img.yankushidai.com/'.$this->uploadOss($up);;

                }
            }
            $city = M('city');
            $schedule = M('schedule');
            $cityfind = $city->field('city_id')->where($where)->find();
            if ($cityfind!=''){
                $con['city'] = $cityfind['city_id'];
                $insert = $schedule->data($con)->add();
            }else{
                $insert = $city->data($where)->add();
                $con['city'] = $insert;
                $insert = $schedule->data($con)->add();
            }
            if ($insert){
                $this->json_rest(1);
            }else{
                $this->json_rest(0);
            }
        }
    }

    public function ceshi(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
//        $key = $redis->set(1,'dfasas');
//        $key = $redis->delete(3);
//        $redis->SREM(3,284);
//        $key = $redis->SINTER(2);
         $key = $redis->keys('*');
//        $key = $redis->SMEMBERS(2);
//        $key = $redis->SMEMBERS('*');
//        $key = $redis->SREM(2,1);
//        $key = $redis->get('1');
//        $ttl = $redis->ttl(1);
        var_dump($key);
//        $this->heartbeat();

    }
    /*
     * 手机跳转页面（通告详情）
     */
    public function schedule_info_share(){
        $Indexinfo = M('schedule');
        $scheduleid = I('post.schedulet_id');
        $data['info'] = $Indexinfo->field("yk_schedule.city,yk_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.schedule_title,yk_user.nickname,yk_user.user_img,yk_user.nameaudit,yk_schedule.schedule_type,yk_schedule.createtime,yk_schedule.acttime,yk_schedule.address,yk_schedule.sex,yk_schedule.schedule_content,yk_schedule.address,yk_user.nameaudit")
            ->join("LEFT JOIN yk_user ON yk_schedule.user_id = yk_user.user_id")
            ->where("yk_schedule.schedule_id=$scheduleid")
            ->find();

        $data['enroll'] = $Indexinfo->field("IFNULL(yk_user.user_img,'1') as user_img,IFNULL(yk_user.nickname,'') as nickname,IFNULL(yk_user.sex,'') as sex,IFNULL(yk_user.user_id,'') as user_id")
            ->join("LEFT JOIN yk_user_schedule ON yk_schedule.schedule_id=yk_user_schedule.schedule_id LEFT JOIN yk_user ON yk_user_schedule.user_id = yk_user.user_id")
            ->where("yk_schedule.schedule_id=$scheduleid")
            ->limit(5)
            ->select();
        if ($data['enroll'][0]['user_img']=='1'){
            $data['enroll'] = array();
        }
        $city = M('city');
        $city_id = $data['info']['city'];
        $city_id = $city->field('city_name')->where("city_id=$city_id")->find();
        $data['info']['city'] = $city_id['city_name'];
        $data['info'] = $this->img_url($data['info'],'schedule_img','Schedule');
        $data['info'] = $this->img_url($data['info'],'user_img','User');
        if (isset($data['enroll'])){
            $data['enroll'] = $this->img_urls($data['enroll'],'user_img','User');
        }
        $this->ajaxReturn($data,'JSON');
    }
    /*
     * 别人的资料分享
     */
    public function others_data_share(){
        $search_id = I('post.search_id');
        $User = M('user');
        $data= $User->field('yk_user.user_img,yk_user.level,yk_user.signature,yk_user.user_type,yk_user.hao_level,yk_user.user_id,yk_user.nickname,yk_user.sex,yk_user.city as user_city,yk_user.birthday,yk_user.height as user_height,yk_user.weight,yk_user.threedimensional,yk_user.signature,group_concat(distinct yk_user_type.type) as tag')
            ->join('LEFT join yk_user_type on yk_user.user_id=yk_user_type.user_id')
            ->where("yk_user.user_id=$search_id")
            ->find();
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->exists($search_id);
        if ($key){
            $data['onlive'] = 1;
        }else{
            $data['onlive'] = 0;
        }
        $city = M('user_city');
        if ($data['threedimensional']==0){
            $data['threedimensional']='0-0-0';
        }
        $city = $city->field('city_name as user_city')->where("city_id=$data[user_city]")->find();
        $data = array_replace($data,$city);
        $fans = M('attention');
        $data['fans'] = $fans->field("yk_user.user_id,yk_user.user_img")
            ->join("LEFT JOIN yk_user ON yk_attention.user_id=yk_user.user_id")
            ->where("yk_attention.attu_id = $search_id")
            ->limit(20)
            ->select();
        if ($data['fans']==''){
            $data['fans']=array();
        }
        $data['fanscount'] = count($data['fans']);
        $usercard = M('usercard');
        $data['usercard'] = $usercard
            ->field('group_concat(DISTINCT yk_usercard_img.usercard_img) as usercard_img,yk_usercard.usercard_id,yk_usercard.usercard_title,yk_usercard.createtime')
            ->join("LEFT JOIN yk_usercard_img ON yk_usercard.usercard_id=yk_usercard_img.usercard_id")
            ->where("yk_usercard.user_id=$search_id")
            ->group("yk_usercard.usercard_id")
            ->limit(1)
            ->select();

        foreach ($data['usercard'] as $k=>&$v){
            $img = explode(',',$v['usercard_img']);
            $v['usercard_img'] = $img;
        }
        if ($data['usercard'] == ''){
            $data['usercard']=array();
        }
        $data['usercard'] = $this->imgs_url($data['usercard'],'usercard_img','User');
        $work = M('work');
        $data['work'] = $work
            ->field('group_concat(DISTINCT yk_work_img.work_img) as work_img,yk_work.work_id,yk_work.work_title,yk_work.start_time,yk_work.introduce')
            ->join("LEFT JOIN yk_work_img ON yk_work.work_id=yk_work_img.work_id")
            ->where("yk_work.user_id=$search_id")
            ->group("yk_work.work_id")
            ->limit(3)
            ->select();

        foreach ($data['work'] as $k=>&$v){
            $img = explode(',',$v['work_img']);
            $v['work_img'] = $img;
        }
        $data['work'] = $this->imgs_url($data['work'],'work_img','User');
        if($data['work']==''){
            $data['work']=array();
        }
        if ($data!=0){
            $data = $this->img_url($data,'user_img','User');
            $data['fans'] = $this->img_urls($data['fans'],'user_img','User');
        }
//        $this->json_rest(1,$data);
        $this->ajaxReturn($data,'JSON');
    }
    /*
     * 直播间分享
     */
    public function live_share(){
        $id = I('post.search_id');
        $my = M('user');
        $where['user_id'] = $id;
        $data = $my->field('user_id,user_img,nickname,sex,level,hao_level')
            ->where($where)
            ->find();
        $data = $this->img_url($data,'user_img','User');
        $deal = M('deal');
        $deal = $deal->field('IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum')
            ->join('LEFT JOIN yk_gift on yk_deal.gift_type=yk_gift.gift_type')
            ->where("to_user_id=$id")
            ->find();
        $data['sum'] = $deal['sum'];
        $key = $this->getliveinfo($id);
        $key = json_decode($key,true);
        if ($key){
            $data['online'] = 1;
        }else{
            $data['online'] = 0;
        }
        $data['chatroom'] = $key['chatroom'];
        $chatroom = $key['chatroom'];
        $data['title'] = $key['title'];
        $data['praise'] = $key['praise'];
        $data['pull'] = str_replace('.flv','/index.m3u8',$key['pull']);
        $data['pull'] = str_replace('http://pull.yankushidai.com','http://yanku321.hlslive.ks-cdn.com',$data['pull']);
        $all = array();
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->get($id);
        $redis->close();
        $key = json_decode($key,true);
        $count = $key['count'];

        $data['count'] = $count;
        if (count($all) < 10) {
            for ($i = 0;$i < 10;$i++){
                $fake[] = rand(302,310);
            }
            $where['user_id'] = array('in', $fake);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->select();

        } elseif (count($all) > 10 && count($all) < 20){
            $where['user_id'] = array('in', $all);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->select();
        } else {
            $where['user_id'] = array('in', $all);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->limit(20)->select();
        }
        $data['view'] = $this->img_urls($data['view'],'user_img','User');
        $this->ajaxReturn($data,'JSON');
    }
    /*
     * 获取直播列表里直播间信息
     */
    public function getliveinfo($id){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->get($id);
        return $key;
    }
    public function ss(){
        if (date("Y-m-d") <= date('Y-m-d', strtotime("+1 week", 1497001272))){
            echo 1;
        }else{
            echo 2;

        }
    }
    public function ceshi1(){

        $user_id=I('post.user_id');
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'Yanyi/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        foreach($info as $k=>$v){
            $data[] = array("user_id"=>(int)$user_id,"yanyi_img"=>$v['savepath'].$v['savename']);
        }
        $audit = M('yanyi');
        $audit->addAll($data);
    }

    /*
         * 直播间分享
         */
    public function live_share_pressure(){
        $user_id = I('post.user_id','');
        $id = I('post.search_id');
        $my = M('user');
        $where['user_id'] = $id;
        $data = $my->field('user_id,user_img,nickname,sex,level,hao_level')
            ->where($where)
            ->find();
        $data = $this->img_url($data,'user_img','User');
        $deal = M('deal');
        $deal = $deal->field('IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum')
            ->join('LEFT JOIN yk_gift on yk_deal.gift_type=yk_gift.gift_type')
            ->where("to_user_id=$id")
            ->find();
        $data['sum'] = $deal['sum'];
        $key = $this->getliveinfo($id);
        $key = json_decode($key,true);
        if ($key){
            $data['online'] = 1;
        }else{
            $data['online'] = 0;
        }
        if (is_int($user_id)){
            $data = D('attention');
            $attu = $data->where("user_id = $user_id AND attu_id=321")->find();
            if ($attu){
                $res['attu'] = 1;
            }else{
                $res['attu'] = 0;
            }
        }elseif(''==$user_id){
            $res['attu'] = 0;
            $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA',
                'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
                'org_name' => '1134170411178481',
                'app_name' => 'yanku');
            $token = new HXController($options);
            $time = getMillisecond();
            $insert = 'yk_'.$time;
            $result = $token->createUser("$insert",'yanku321');
            empty($result)?$this->json_rest(0,array('user_id'=>'-1')):$res['hx_id']=$result['entities'][0]['username'];
        }else{
            $res['attu'] = 0;
            $res['hx_id'] = $user_id;
        }
        $data['count'] = $key['count'];
        $data['chatroom'] = $key['chatroom'];
        $chatroom = $key['chatroom'];
        $data['title'] = $key['title'];
        $data['praise'] = $key['praise'];
        $data['pull'] = str_replace('.flv','/index.m3u8',$key['pull']);
        $data['pull'] = str_replace('hdl','hls',$data['pull']);
        $all = array();
        $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name' => '1134170411178481',
            'app_name' => 'yanku');
        $token = new HXController($options);
        $count = $token->getChatRoomDetail($chatroom);
        $time = getMillisecond();
        $insert = 'yk_'.$time;
        $result = $token->createUser("$insert",'yanku321');
        empty($result)?$this->json_rest(0,array('user_id'=>'-1')):$data['hx_id']=$result['entities'][0]['username'];
        foreach ($count['data'][0]['affiliations'] as $k => $v) {
            foreach ($v as $k => $v) {
                Array_push($all, $v);
            }
        }
        if (count($all) < 10) {
            for ($i = 0;$i < 10;$i++){
                $fake[] = rand(302,310);
            }
            $where['user_id'] = array('in', $fake);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->select();

        } elseif (count($all) > 10 && count($all) < 20){
            $where['user_id'] = array('in', $all);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->select();
        } else {
            $where['user_id'] = array('in', $all);
            $result = M('user');
            $data['view'] = $result->field('user_id,user_img,nickname,hao_level')->where($where)->limit(20)->select();
        }
        $data['view'] = $this->img_urls($data['view'],'user_img','User');
        $this->ajaxReturn($data,'JSON');
    }
    public function tel(){
        $con['name'] = I('post.name');
        $con['mobile'] = I('post.mobile');
        $con['tra_count'] = I('post.tra_count');
        $con['time'] = I('post.time');
        $tra = M('tra');
        $ins=$tra->data($con)->add();
        $ins==''?$this->json_rest(0):$this->json_rest(1);
    }
    public function account(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(15);
        $mobile = $redis->get('mobile');
        $model = new UserModel();
        $con['nickname'] = I('post.nickname');
        $password = 123456;
        $con['sex'] = I('post.sex');
        $con['city'] = 49;
        $con['mobile'] = $mobile;
        $con['password_salt'] = $model->mkSalt();
        $con['password'] = $model->mkPassword($password);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'User/';
        $info = $upload->uploadOne($_FILES['image']);
        if (!$info) {// 上传错误提示错误信息
            $this->json_rest(9);
            die();
        } else {// 上传成功 获取上传文件信息
            $con['user_img'] = $info['savepath'] . $info['savename'];
        }
        $model_user = M('user');
        $ins = $model_user->add($con);
        $mobile += 1;
        $redis->set('mobile',$mobile);
        $ins==''?$this->json_rest(0):$this->json_rest(1);
    }
    public function weichat(){
        require ('JSSDKController.class.php');
        $jssdk = new \JSSDK($this->appId, $this->appSecret);
        $signPackage = $jssdk->GetSignPackage();
        $this->ajaxReturn($signPackage,'JSON');
    }
    private function quiksort($z){
        if (0!=count($z)){
            $m = $z[0];
            $x = array();
            $y = array();
            for ($i=1;$i<count($z);$i++){
                if ($z[$i]>=$m){
                    $y[] = $z[$i];
                }else{
                    $x[] = $z[$i];
                }
            }
            $x = $this->quiksort($x);
            $y = $this->quiksort($y);
            return array_merge($x,array($m),$y);
        }else{
            return $z;
        }
    }
    private function cece(){
        $z = array(1,8,5,7,44,6,32,489,98,72,52,436);
        var_dump($this->quiksort($z));
    }
}
