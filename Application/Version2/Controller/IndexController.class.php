<?php
namespace Version2\Controller;
use Back\Controller\HXController;
use Version2\Controller;
use Version2\Model\UserModel;
use OSS\Core\OssException;
use OSS\OssClient;
class IndexController extends ResController {
    private $url = array();
    private $heart = array();
    private $app_key = 'c4e4f4e1b5b29c8a3f85811e';
    private $master_secret = 'c9dc57419e7ed0f48ec37549';
    //心跳开启标识
    private $sleep_time = 10;//多长时间执行一次
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');
    //*development：测试接口
    public function shi1(){
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $redis->delete(6);
        die();
        $token = substr(md5(date()),13);
        $redis->rPush('key1', 'A');
        $redis->rPush('key1', 'B');
        $redis->rPush('key1', 'C');
        var_dump($redis->lRange('key1', 0, -1)); /* array('A', 'B', 'C') */
        getFirstCharter();
    }
    //*development：测试接口1
    public function shi(){
        $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name'=>'1134170411178481',
            'app_name'=>'yanku');
        $token = new HXController($options);
        $content = array(
              "name"=> "test",
              "description"=>"server create group",
              "maxusers"=>500,
              "owner"=>"yanku0001",
              "members"=>array('86','yanku0002')
        );
        $result = $token->createChatRoom($content);
        $this->json_rest(1,$result['data']);

    }
    public function user_type(){
        $user_id = I('request.user_id');
        $token = I('request.token');
        $this->token_audit($user_id,$token);
    }
    public function reg(){
        $result = $this->createUser(ggg,'test');
        echo $result;
    }
    /*
         * 忘记密码
         * param1：mobile手机号
         * param2：pwd密码
         * 用sha1加密密码和盐值验证
    */
    public function forget_password(){
        $con['mobile'] = I('post.mobile');
        $data['mobile'] = $con['mobile'];
        $password = I('post.pwd');
        $model_user = M('user');
        $login = $model_user->field('user_id')->where($con)->find();
        if(!$login){
            $this->json_rest(25);
            die();
        }
        $model = new UserModel();
        $data['password_salt'] = $model->mkSalt();
        $data['password'] = $model->mkPassword($password);
        $login = $login['user_id'];
        $resule = $model_user->where("user_id=$login")->save($data); // 根据条件更新记录
        if ($resule){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);
        }
    }
    public function change_mobile(){
        $where['user_id'] = I('post.user_id');
        $con['mobile'] = I('post.mobile');
        $model_user = M('user');
        $login = $model_user->field('user_id')->where($con)->find();
        if($login){
            $this->json_rest(24);
            die();
        }
        $res = M('user');
        $res = $res->where($where)->data($con)->save();
        if ($res){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);
        }
    }
    /*
     * 注册
     * param1：mobile手机号
     * param2：pwd密码
     * 用sha1加密密码和盐值验证
     */
    public function register(){
        $con['mobile'] = I('post.mobile');
        $password = I('post.pwd');
        $channel = I('post.channel',0);
        $model_user = M('user');
        $login = $model_user->field('nickname')->where($con)->find();
        if(!empty($login)){
            $this->json_rest(10,array('user_id'=>'-1'));
            die();
        }else{
            $model = new UserModel();
            $con['password_salt'] = $model->mkSalt();
            $con['password'] = $model->mkPassword($password);
            $con['nickname'] = $con['mobile'];
            $con['registertime'] = time();
            $con['lasttime'] = time();
            $con['channel'] = $channel;
            $insert = $model_user->add($con);
            $data['user_id'] = $insert;
            $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA','client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4','org_name'=>'1134170411178481','app_name'=>'yanku');
            $token = new HXController($options);
            $result = $token->createUser("$insert",'yanku321');
            $result = $token->createUser("bos$insert",'yanku321');
            empty($result)?$this->json_rest(0,array('user_id'=>'-1')):$this->json_rest(1,$data);
//            var_dump($result);
        }
    }
    /*
     * 注册详细信息
     */
    public function registerinfo()
    {
        $data = array();
        $user_id = I('post.user_id');
        $type = I('post.type');
        $con['sex'] = I('post.sex');
        $con['nickname'] = I('post.nickname');
        $data['nickname'] = $con['nickname'];
        $con['kubi'] = 50;
        $time = date("Y:m:d");
        $city['city_name'] = I('post.city');
        $con['user_type'] = I('post.type');
        $model_user = D('user');
        $model_city = D('user_city');
        if(2==$type){
            $bins['user_id'] = $user_id;
            $boss = M('boss');
            $ins = $boss->add($bins);
        }
        $reg = $model_user->where($data)->find();
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        //6。确认当天第一次签到
        $redis->select(6);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png',  'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'User/';
        $info = $upload->uploadOne($_FILES['image']);
        if (!$info) {// 上传错误提示错误信息
            $con['user_img'] = 'User/2017-06-09/WechatIMG10.png';
//            $this->json_rest(9);
//            die();
        } else {// 上传成功 获取上传文件信息
            $con['user_img'] = $this->uploadOss($info['savepath'] . $info['savename']);
        }

        if ($reg) {
            $this->json_rest(10,$data);
            die();
        } else {
            $res['day'] = 1;
            $res['time'] = $time;
            $res = json_encode($res,JSON_UNESCAPED_SLASHES);
            $res = $redis->set($user_id,$res);
            $citylist = $model_city->where($city)->find();
            if (empty($citylist)) {
                $insert = $model_city->add($city);
                $con['city'] = $insert;
                $insert = $model_user->where("user_id=$user_id")->save($con);
                empty($insert) ? $this->json_rest(0,$data) : $this->json_rest(1,$data);
            } else {
                $con['city'] = $citylist['city_id'];
                $insert = $model_user->where("user_id=$user_id")->save($con);
                empty($insert) ? $this->json_rest(0,$data) : $this->json_rest(1,$data);
            }
        }
    }

    /*
     * 登陆
     * param1：mobile手机号
     * param2：pwd密码
     * 用sha1加密密码和盐值验证
     */
    public function login()
    {
        $mobile = I('post.mobile');
        $password = I('post.pwd');
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(1);
        if (preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            if ($mobile) {
                $model_user = D('user');
                // 利用用户检索记录
                $cond['mobile'] = $mobile;
                $login = $model_user->where($cond)->find();
                if ($login && sha1($login['password_salt'] . $password) === $login['password']) {
                    $data = $model_user->field('yk_user.user_id,yk_user.nickname,yk_user.kubi,yk_user.user_type,yk_user.user_img,yk_user.sex,yk_user.level,yk_user.signature,yk_room.push,yk_user.mobile,yk_user.hao_level,yk_user.status')
                        ->join('LEFT JOIN yk_room ON yk_user.user_id=yk_room.user_id')
                        ->where($cond)
                        ->find();
                    if (1==$data['status']){
                        $res = array('nickname' => '', 'kubi' => '', 'user_type' => '', 'user_img' => '', 'sex' => '', 'level' => '', 'signature' => '', 'push' => '', 'mobile' => '', 'hao_level' => '', 'token' => '', 'isopen' => '', 'shareURL' => '');
                        $res['user_id'] = $data['user_id'];
                        $this->json_rest(0, $res);
                        die();
                    }
                    $data = $this->img_url($data, 'user_img', 'User');
                    if (0 == $data['sex']) {
                        $res = array('nickname' => '', 'kubi' => '', 'user_type' => '', 'user_img' => '', 'sex' => '', 'level' => '', 'signature' => '', 'push' => '', 'mobile' => '', 'hao_level' => '', 'token' => '', 'isopen' => '', 'shareURL' => '');
                        $res['user_id'] = $data['user_id'];
                        $this->json_rest(18, $res);
                        die();
                    }
                    $thr = M('third');
                    $res = $thr->field('customer_type')->where("user_id=$data[user_id]")->find();
                    if (!$res){
                        $data['customer_type'] = 0;
                    }else{
                        $data['customer_type'] = 1;
                    }
                    $token = substr(md5(time()), 13);
                    $redis->set($data['user_id'], $token);
                    $redis->expireAt($data['user_id'], strtotime(date("Y-m-d", strtotime("+3 month"))));
                    $data['token'] = $redis->get($data['user_id']);
                    //Applepay内购开关
                    $data['isopen'] = 0;//0关闭
                    $data['shareURL'] = 'http://www.yankushidai.com/share/information.html?search_id=' . $data['user_id'];
                    if ($data['push'] == '') {
                        $data['push'] == '';
                    }
                    $this->json_rest(1, $data);
                } else {
                    $this->json_rest(11, array('user_id' => 'o'));
                    die();
                }
            } else {
                $this->json_rest(11, array('user_id' => 'o'));
                die();
            }
        }
    }
        /*
         * 发送城市列表
         * result1：最热城市列表
         * result2：城市列表
         */

    public function city(){
        $user_id = I('request.user_id');
        $token = I('request.token');
        $this->token_audit($user_id,$token);
        $Indexinfo = M('city');
        $data = $Indexinfo->order('convert(city_name using gbk) asc')->select();
        $data = getFirstCharter($data);
        if ($data == ''){
            $data=array();
        }
        $this->json_rest(1,$data);
    }

    /*
     * 发送通告首页
     * result1：banner（通告轮播信息）
     * result2：list（通告列表）
     */
    public function index_tg(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $start = I('post.start',0);
        $city = I('post.city',0);
        $time = time();
        $where = $city==0?'':"AND yk_schedule.city=$city";
        $Indexinfo = M('schedule');
        $data['banner'] = $Indexinfo->field('yk_schedule.schedule_id as banner_id,yk_schedule.schedule_img as img,yk_banner.banner_type')
            ->join('LEFT JOIN yk_banner on yk_banner.banner_id=yk_schedule.schedule_id')
            ->where('banner_type=0')
            ->ORDER('RAND()')
            ->limit(4)
            ->select();
        $data['list'] = $Indexinfo->field("yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.schedule_img,yk_schedule.schedule_type,yk_schedule.acttime,COUNT(yk_user_schedule.schedule_id) as count")
            ->join("LEFT JOIN yk_user ON yk_schedule.user_id=yk_user.user_id LEFT JOIN yk_user_schedule on yk_schedule.schedule_id=yk_user_schedule.schedule_id")
//            ->where("yk_schedule.acttime>=$time AND yk_schedule.status!=0 $where")
            ->where("yk_schedule.status!=0 AND yk_schedule.status!=3 $where")
            ->group('yk_schedule.schedule_id')
            ->order('yk_schedule.createtime DESC')
            ->limit($start,20)
            ->select();
        $data['banner'] = $this->img_urls($data['banner'],'img','Schedule');
        $data['list'] = $this->img_urls($data['list'],'schedule_img','Schedule');
        if ($data['banner']==null){
            $data['banner']=array();
        }elseif ($data['list']==null){
            $data['list']=array();
        }
        $this->json_rest(1,$data);
    }

    /*
     * 根据获取通告类型发送数据
     * param1：通告类型
     * param2：当前城市
     * param3：排序(默认随机排序，可选createtime、acttime)
     * param4：起始数据数(默认0)
     * param5：性别要求
     * param6：是否实名
     */
    public function schedule_type(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $Indexinfo = M('schedule');
        $type = I('post.schedulet_type','ERR');
        if ($type=='ERR'){
            $this->json_rest(5);
            die();
        }
        $city = I('post.city_id',0);
        $order = I('post.order',0);
        $start = I('post.start',0);
        $filter = I('post.filter',0);
        $audit = I('post.audit',0);
        $time = time();
        if ($type == ''){
            $this->json_rest(5,'');
            die();
        }
        switch ($order){
            case 0:
                $order = 'schedule_id DESC';
                break;
            case 1:
                $order = 'createtime DESC';
                break;
            case 2:
                $order = 'acttime';
                break;
            default:
                $this->json_rest(5);
                die();
        }
        $city = $city==0?'':"AND yk_schedule.city=$city";
        $audit = $audit==0?'':"AND yk_schedule.audit=$audit";
        $sex = $filter==0?'':"AND yk_schedule.sex=$filter";
        $data = $Indexinfo->field("yk_schedule.schedule_id,yk_user.nickname,yk_schedule.schedule_img,yk_schedule.schedule_title,yk_schedule.audit,yk_schedule.schedule_type,yk_schedule.acttime,COUNT(yk_user_schedule.schedule_id) as count")
            ->join("LEFT JOIN yk_user ON yk_schedule.user_id=yk_user.user_id LEFT JOIN yk_user_schedule on yk_schedule.schedule_id=yk_user_schedule.schedule_id")
//            ->where("yk_schedule.schedule_type=$type AND yk_schedule.status!=0 AND yk_schedule.status!=3 AND yk_schedule.acttime>=$time $city $audit $sex")
            ->where("yk_schedule.schedule_type=$type AND yk_schedule.status!=0 AND yk_schedule.status!=3 $city $audit $sex")
            ->group('yk_schedule.schedule_id')
            ->order($order)
            ->limit($start,10)
            ->select();
        $data = $this->img_urls($data,'schedule_img','Schedule');
        if ($data==null){
            $data=array();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 通告详细信息
     * param：获取通告ID
     */
    public function schedule_type_info(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $Indexinfo = M('schedule');
        $scheduleid = I('post.schedulet_id');
        $data['info'] = $Indexinfo->field("yk_schedule.status,yk_schedule.city,yk_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.user_id,yk_schedule.schedule_title,yk_user.nickname,yk_user.user_img,yk_schedule.audit,yk_schedule.schedule_type,yk_schedule.createtime,yk_schedule.acttime,yk_schedule.address,yk_schedule.sex,yk_schedule.schedule_content,yk_schedule.address,IFNULL(COUNT(yk_user_schedule.schedule_id),'0') as count")
                                                   ->join("LEFT JOIN yk_user ON yk_schedule.user_id = yk_user.user_id LEFT JOIN yk_user_schedule ON yk_schedule.schedule_id=yk_user_schedule.schedule_id")
                                                   ->where("yk_schedule.schedule_id=$scheduleid AND yk_schedule.status!=4")
                                                   ->find();
        $data['enroll'] = $Indexinfo->field("IFNULL(yk_user.user_img,'1') as user_img,IFNULL(yk_user.nickname,'') as nickname,IFNULL(yk_user.sex,'') as sex,IFNULL(yk_user.user_id,'') as user_id")
                                                     ->join("LEFT JOIN yk_user_schedule ON yk_schedule.schedule_id=yk_user_schedule.schedule_id LEFT JOIN yk_user ON yk_user_schedule.user_id = yk_user.user_id")
                                                     ->where("yk_schedule.schedule_id=$scheduleid")
                                                     ->limit(6)
                                                     ->select();
//        if ($data['enroll']['user_img']==1){
//            $data['info']['count'] = 0;
//        }else{
//            $data['info']['count'] = count($data['enroll']);
//        }
//        var_dump(strtotime(date("Y-m-d")));die();
//        var_dump(strtotime("+1 week", $data['info']['acttime']));die();
        $join = M('user_schedule');
        $join = $join->field('user_id,schedule_status')->where("user_id=$user_id AND schedule_id=$scheduleid")->find();
        isset($join['user_id'])?$data['info']['join']=1:$data['info']['join']=0;
        if ($data['info']['join']==1){
            if ($join['schedule_status']==(string)2 && strtotime(date("Y-m-d")) < strtotime("+1 week", $data['info']['acttime'])){
//                if ($join['schedule_status']==(string)2 && date("Y-m-d") <= date('Y-m-d', strtotime("+1 week", $data['info']['acttime']))){
                    $data['info']['show'] = 1;
            }elseif ($join['schedule_status']==(string)3 ){
                $report = M('schedule_report');
                $report = $report->field('user_id')->where("user_id=$user_id AND schedule_id=$scheduleid")->find();
                empty($report)?$data['info']['show']=2:$data['info']['show']=0;
            }else{
                $data['info']['show'] = 0;
            }
        }else{
            $data['info']['show'] = 0;
        }
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
        }else{
        }
        if ($data['info']['city']==null){
            $data['info']=array();
        }
        if ($data['enroll']==null){
            $data['enroll']=array();
        }
        $data['info']['shareURL'] = 'http://www.yankushidai.com/share/index.html?schedule='.$scheduleid;
        $this->json_rest(1,$data);
    }
    public function report()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $data['report_id'] = $user_id;
        $data['user_id'] = I('post.id');
        $data['report_type'] = I('post.report_type',0);
        $data['reason'] = I('post.reason',0);
        $data['explain'] = I('post.explain',0);
        $report = M('report'); // 实例化User对象
        if ($report->add($data)){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);
        }
    }
    /*
     * 演库首页
     */
    public function index_yk(){
        $user_id = I('request.user_id');
        $token = I('request.token');
        $this->token_audit($user_id,$token);
        $Index = M('user');
        $start = I('post.start',0);
        $banner = M('activity');
        $data['banner'] = $banner->where('activity_push!=1')->select();
//        $data['banner'] = $Index->field('yk_user.user_id as banner_id,yk_banner.banner_type,yk_user.user_img as img')
//                                                    ->join('LEFT JOIN yk_banner on yk_banner.banner_id=yk_user.user_id ')
//                                                    ->where('banner_type=1')
//                                                    ->ORDER('RAND()')
//                                                    ->limit(4)
//                                                    ->select();
        $data['list'] = $Index->field("yk_user.user_id,group_concat(distinct yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit")
                                                 ->join("LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id")
                                                 ->join("LEFT JOIN yk_yanyiaudit ON yk_user.user_id=yk_yanyiaudit.user_id")
                                                 ->where("yk_user_type.type!=0 AND yk_yanyiaudit.status=2 AND yk_user.user_img != 'User/2017-06-09/WechatIMG10.png' AND yk_user.user_img != '1fab8ac694f898.jpeg' AND yk_user.nickname NOT IN ('艺人小哥','伊纯由真','七小叶','程秋盼','lolooo','测试一下')")
                                                 ->group("yk_user.user_id")
                                                 ->order('user_id DESC')
                                                 ->limit($start,20)
                                                 ->select();

        $data['banner'] = $this->img_urls($data['banner'],'activity_img','User');
        $data['list'] = $this->img_urls($data['list'],'user_img','User');
        if ($data['banner']==null){
            $data['banner']=array();
        }elseif ($data['list']==null){
            $data['list']=array();
        }
        $this->json_rest(1,$data);

    }
    /*
     * 根据类型
     */
    public function yk_type(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $typeInfo = M('user');
        $type = I('post.type',0);
        $order = I('post.order',0);
        $sex = I('post.filter',0);
        $start = I('post.start',0);
        $audit = 2;
        0==$sex?$sex='':$sex="AND yk_user.sex=$sex";
        $audit = $audit==1?"AND yk_user.nameaudit=2":"AND yk_user.nameaudit=2 AND yk_yanyiaudit.status=2";
        switch ($order){
            case 0:
                $data['list'] = $typeInfo->query("select yk_user.user_id,group_concat(distinct yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit from yk_user LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id WHERE yk_user.user_id in (SELECT user_id from yk_user_type where type=$type) AND yk_user_type.type!=0 AND yk_yanyiaudit.status=2 $audit $sex AND yk_user.user_img != 'User/2017-06-09/WechatIMG10.png' AND yk_user.user_img != '1fab8ac694f898.jpeg' AND yk_user.nickname NOT IN ('艺人小哥','伊纯由真','七小叶','程秋盼','lolooo','测试一下') GROUP BY yk_user.user_id ORDER BY yk_user.password_salt LIMIT $start,20 ");
//                $data['list'] = $typeInfo->field("yk_user.user_id,group_concat(yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit")
//                    ->join("LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id")
//                    ->where("yk_user_type.type!=0 AND yk_user_type.type=$type $audit $sex")
//                    ->group("yk_user.user_id")
//                    ->order('RAND()')
//                    ->limit($start,20)
//                    ->select();
                break;
            case 1:
                $data['list'] = $typeInfo->query("select yk_user.user_id,group_concat(distinct yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit,COUNT(yk_attention.attu_id) as cont from yk_user LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id LEFT JOIN yk_attention ON yk_user.user_id=yk_attention.attu_id WHERE yk_user.user_id in (SELECT user_id from yk_user_type where type=$type) AND yk_user_type.type!=0 AND yk_yanyiaudit.status=2 $audit $sex AND yk_user.user_img != 'User/2017-06-09/WechatIMG10.png' AND yk_user.user_img != '1fab8ac694f898.jpeg' AND yk_user.nickname NOT IN ('艺人小哥','伊纯由真','七小叶','程秋盼','lolooo','测试一下') GROUP BY yk_user.user_id ORDER BY cont DESC LIMIT $start,20 ");

//                $data['list'] = $typeInfo->field("yk_user.user_id,group_concat(distinct yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit,COUNT(yk_attention.attu_id) as cont")
//                    ->join("LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id LEFT JOIN yk_attention ON yk_user.user_id=yk_attention.attu_id")
//                    ->where("yk_user_type.type!=0 AND yk_user_type.type=$type $audit $sex")
//                    ->group("yk_user.user_id")
//                    ->order('cont DESC')
//                    ->limit($start,20)
//                    ->select();
                break;
            case 2:
                $data['list'] = $typeInfo->query("select yk_user.user_id,group_concat(distinct yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit from yk_user LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id WHERE yk_user.user_id in (SELECT user_id from yk_user_type where type=$type) AND yk_user_type.type!=0 AND yk_yanyiaudit.status=2 $audit $sex AND yk_user.user_img != 'User/2017-06-09/WechatIMG10.png' AND yk_user.user_img != '1fab8ac694f898.jpeg' AND yk_user.nickname NOT IN ('艺人小哥','伊纯由真','七小叶','程秋盼','lolooo','测试一下') GROUP BY yk_user.user_id ORDER BY yk_user.lasttime DESC LIMIT $start,20 ");
//                $data['list'] = $typeInfo->field("yk_user.user_id,group_concat(yk_user_type.type) as type,yk_user.nickname,yk_user.user_img,yk_user.nameaudit")
//                    ->join("LEFT JOIN yk_user_type ON yk_user.user_id=yk_user_type.user_id LEFT JOIN yk_yanyiaudit on yk_user.user_id=yk_yanyiaudit.user_id")
//                    ->where("yk_user_type.type!=0 AND yk_user_type.type=$type $audit $sex")
//                    ->group("yk_user.user_id")
//                    ->order('yk_user.lasttime DESC')
//                    ->limit($start,20)
//                    ->select();
                break;
            default:
                $this->json_rest(5);
                die();
        }
        $data['list'] = $this->img_urls($data['list'],'user_img','User');
        if ($data['list']==''){
            $data['list']=array();
        }
        $this->json_rest(1,$data);
    }
    public function yk_type_order_by_fans(){
        $user_id = I('request.user_id');
        $token = I('request.token');
        $this->token_audit($user_id,$token);
        $typeInfo = D('Yanku');
        $type = I('post.type',0);
        $sex = I('post.sex','');
        $start = I('post.start','');

        $sex = empty($sex)?'':"AND u.sex=$sex";
        $nameaudit = I('get.nameaudit','');
        $start = I('get.start',0);
        $city = I('get.city_id');
        $data = $typeInfo->field('user_id,nickname,user_img,type_cont,type,COUNT(a.attu_id) as cont,nameaudit')
//                         ->where("type = $type")
                         ->group('user_id')
//                         ->order('cont DESC')
//                         ->limit($start,20)
                         ->select();
        $data = $this->img_urls($data,'user_img','User');
        $this->json_rest(1,$data);
    }
    /*
     * 用户页面---用户信息（固定）
     */
    public function user_info(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $my = M('user');
        $search_id = I('post.id');
        0==(int)$search_id?$id = substr($search_id,3,strlen($search_id)):$id=$search_id;
        $where['user_id'] = $id;
        if (isset($user_id)){
            $data = $my->field('user_id,user_img,nickname,signature,level,hao_level,sex')
                ->where($where)
                ->find();
            $where['attu_id'] = $id;
            $where['user_id'] = $user_id;
            $attention = M('attention');
            $attention = $attention->where($where)->select();
            if ($data['user_img'] != ''){
                $data = $this->img_url($data,'user_img','User');
            }else{
                $this->json_rest(0);
                die();
            }
            $user_id==$id?$data['attu']=1:(empty($attention)?$data['attu']=0:$data['attu']=1);
        }else{
            $this->json_rest(5);
            die();
        }
        if(0==(int)$search_id){
            $data['nickname']=$data['nickname']."(BOSS)";
            $data['user_id'] = 'bos'.$data['user_id'];
        }
        $this->json_rest(1,$data);

    }
    /*
     * 用户页面---我的主页标签（固定）
     */
    public function mine(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        //动态三张图
        //实例化动态模型
//        $dynamic = M('dynamic');
//        $this->count['data']['dynamic'] = $dynamic->field('yk_dynamic_img.dynamic_img')
//            ->join('LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id')
//            ->where("yk_dynamic.user_id=$user_id")
//            ->order('yk_dynamic.dynamic_createtime')
//            ->limit(3)
//            ->select();
        //粉丝贡献榜前三名
        //实例化粉丝贡献模型
        $deal = M('deal');
        $data['money'] = $deal->field('yk_user.user_id,yk_user.user_img,SUM(yk_gift.price*yk_deal.number) as deal')
            ->join('LEFT JOIN yk_gift ON yk_deal.gift_type=yk_gift.gift_type LEFT JOIN yk_user ON yk_user.user_id=yk_deal.from_user_id')
            ->where("yk_deal.to_user_id=$search_id")
            ->group('yk_deal.from_user_id,yk_deal.to_user_id')
            ->order('deal DESC')
            ->limit(3)
            ->select();
        //我的粉丝
        //实例化关注模型
        $deal = M('attention');
        $data['fans'] = $deal->field('yk_user.user_id,yk_user.user_img')
            ->join('LEFT JOIN yk_user ON yk_user.user_id=yk_attention.user_id')
            ->where("yk_attention.attu_id=$search_id")
            ->order('RAND()')
            ->limit(3)
            ->select();
        //我的粉丝
//        //实例化模型
//        $schedule = M('user_schedule');
//        $data['schedule'] = $schedule->field('COUNT(user_id) as all_schedule')
//            ->where("user_id=$search_id")
//            ->group('user_id')
//            ->select();
        //格式化
        $data['money'] = $this->img_urls($data['money'],'user_img','User');
        $data['fans'] = $this->img_urls($data['fans'],'user_img','User');

        $this->json_rest(1,$data);
    }
    /*
     * 粉丝守护（贡献）榜
     */
    public function fans_contribut(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $contribut_type = I('post.contribut_type');
        switch ($contribut_type){
            case 0:
                $time = '';
                break;
            case 1:
                $time = "AND  YEARWEEK(FROM_UNIXTIME(yk_deal.pay_time,'%Y-%m-%d')) = YEARWEEK(now())";
                break;
            default:
                $time = '';
                break;

        }
        $deal = M('deal');
        $data = $deal->field('yk_user.user_id,yk_user.user_img,yk_user.nickname,yk_user.level,yk_user.user_type,yk_user.hao_level,SUM(yk_gift.price*yk_deal.number) as deal')
            ->join("LEFT JOIN yk_gift ON yk_deal.gift_type=yk_gift.gift_type LEFT JOIN yk_user ON yk_user.user_id=yk_deal.from_user_id")
            ->where("yk_deal.to_user_id=$search_id $time")
            ->group("yk_deal.from_user_id,yk_deal.to_user_id")
            ->order("deal DESC")
            ->limit(10)
            ->select();
        if ($data == ''){
            $this->json_rest(12,$data);
            die();
        }else{
            $data = $this->img_urls($data,'user_img','User');
        }
        if ($data==null){
            $data=array();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 我的等级
     */
    public function my_level(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $deal = M('user');
        $this->count['data'] = $deal->field('level')
            ->where("user_id=$user_id")
            ->select();
        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
        echo $this->count;
    }
    /*
     * 粉丝列表
     */
    public function fans(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $start = I('post.start',0);
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $fan = M('attention');
        $data = $fan->field('yk_attention.user_id,yk_user.user_type,yk_user.level,yk_user.user_img,yk_user.nickname,yk_user.signature,yk_user.hao_level')
            ->join('LEFT JOIN yk_user ON yk_attention.user_id=yk_user.user_id')
            ->where("yk_attention.attu_id=$search_id")
            ->limit($start,15)
            ->select();
        $data = $this->img_urls($data,'user_img','User');
        if ($data==null){
            $data=array();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 关注列表
     */
    public function attention(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $start = I('start',0);
        $fan = M('attention');
        $data = $fan->field('yk_user.user_id,yk_user.level,yk_user.user_img,yk_user.nickname,yk_user.signature,yk_user.user_type')
            ->join('LEFT JOIN yk_user ON yk_attention.attu_id=yk_user.user_id')
            ->where("yk_attention.user_id=$user_id AND yk_attention.attu_id>$start")
            ->order("yk_attention.addtime desc")
            ->limit($start,20)
            ->select();
        $data = $this->img_urls($data,'user_img','User');
        if ($data==null){
            $data=array();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 我参加的通告
     * param1：分页开始条数
     * param2：参加状态
     */
    public function join(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $start = I('post.start',0);
        $status = I('post.status',0);
        $status = $status==0?'':"AND yk_user_schedule.schedule_status=$status";
        $work = M('user_schedule');
        $data=$work->field('yk_user_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.schedule_type')
            ->join('LEFT JOIN yk_schedule ON yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where("yk_user_schedule.user_id=$user_id $status")
            ->limit($start,5)
            ->select();

        $data = $this->img_urls($data,'schedule_img','Schedule');
        if ($data==null){
            $data=array();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 根据参加状态显示
     * param1：起始条数
     * param2：通告类型
     */
    public function join_schedule_type(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $status = I('post.type',0);
        $start = I('post.start',0);
        $where['yk_user_schedule.user_id'] = array('EQ',$user_id);
        $where['yk_schedule.user_id']= array('NEQ',$user_id);
        switch ($status){
            case 0:
                break;
            case 1:
                $where['yk_user_schedule.schedule_status'] = array('EQ','0');
                break;
            case 2:
                $where['yk_user_schedule.schedule_status'] = array('IN','2,3');
                break;
            default:
                break;
        }
        $work = M('user_schedule');
        $data = $work->field('ifnull(yk_user_schedule.schedule_id,"") as schedule_id,yk_schedule.acttime,yk_schedule.schedule_title,yk_schedule.schedule_img,yk_schedule.schedule_type,yk_user.nickname')
            ->join('LEFT JOIN yk_schedule ON yk_user_schedule.schedule_id=yk_schedule.schedule_id LEFT JOIN yk_user ON yk_schedule.user_id=yk_user.user_id')
            ->where($where)
            ->limit($start,5)
            ->select();
        if ($data[0]['schedule_id']==""){
            $data = array(0=>array("schedule_id"=>'',"acttime"=>"","schedule_title"=>"","schedule_img"=>"","schedule_type"=>"","nickname"=>""));
            $this->json_rest(13,$data);

        }else{
            $data = $this->img_urls($data,'schedule_img','Schedule');
            if ($data==null){
                $data=array();
            }
            $this->json_rest(1,$data);
        }
    }

    /*
     * 参加通告验证
     * param1：4位验证码
     * param2：通告ID
     */
    public function join_audit(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $schedule = I('post.schedule',0);//必填，参加的通告ID
        $code = I('post.code');
        $work = M('user_schedule');
        $isset = $work->field('schedule_status')
            ->where("user_id=$user_id AND schedule_id=$schedule")
            ->find();
        if($isset['schedule_status']==2) {//参加状态为2，进行判断
            $worker = M('schedule');
            $data = $worker->field('acttime,valcode')
                ->where("schedule_id=$schedule")
                ->find();
            if (date("Y-m-d") <= date('Y-m-d', strtotime("+1 week", $data['acttime']))) {
                if ($data['valcode'] == $code) {
                    $con['schedule_status'] = 3;
                    $in = $work->where("user_id=$user_id AND schedule_id=$schedule")->save($con);
                    if ($in){
                        $this->json_rest(1);
                        die();
                    }else{
                        $this->count['result'] = 0;
                        $this->count['message'] = "验证失败";
                    }
                } else {
                    $this->count['result'] = 0;
                    $this->count['message'] = "验证失败";
                }
            } else {
                $this->count['result'] = 0;
                $this->count['message'] = "已超过验证时间";
            }
        }elseif ($isset==3){//参加状态为3，返回已参加过该活动
            $this->count['result'] = 0;
            $this->count['message'] = "您已经参加过该活动";
        }else{//其他状态则返回未受邀参加该活动
            $this->count['result'] = 0;
            $this->count['message'] = "您未受邀请参加过此活动";
        }
        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
        echo $this->count;
    }
    /*
     * 动态列表
     * param1：其实条数
     */
    public function dynamic(){
        $user_id = I('get.user_id');
        $start = I('get.start',0);
        $dynamic = M('dynamic');
        $this->count['data'] = $dynamic->field('yk_user.user_img,yk_user.nickname,yk_user.level,yk_dynamic.dynamic_id,IFNULL(yk_dynamic_img.dynamic_img,"") as dynamic_img,yk_dynamic.content,yk_dynamic.dynamic_createtime,yk_dynamic.localtion,yk_dynamic.view,yk_dynamic.favorite')
            ->join('LEFT JOIN yk_user ON yk_dynamic.user_id=yk_user.user_id LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id')
            ->where("yk_dynamic.user_id = $user_id")
            ->group('yk_dynamic.dynamic_id')
            ->limit($start,5)
            ->select();
        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
        echo $this->count;
    }
    /*
     * 详细动态信息
     * param1：动态ID
     */
    public function dynamic_type(){
        $dynamic_id = I('post.dynamic_id');
        $dynamictype = M('dynamic');
        $this->count['data'] = $dynamictype->field()
            ->join('LEFT JOIN yk_user ON yk_dynamic.user_id=yk_user.user_id LEFT JOIN yk_dynamic_img ON yk_dynamic.dynamic_id=yk_dynamic_img.dynamic_id LEFT JOIN yk_dynamic_comments ON yk_dynamic.dynamic_id=yk_dynamic_comments.dynamic_id AND yk_user.user_id=yk_dynamic_comments.user_id')
            ->where()
            ->select();
        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
        echo $this->count;
    }
    /*
     * 资料卡
     * param：user_id
     */
    public function usercard(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $usercard = M('usercard');
        $data = $usercard
            ->field('group_concat(DISTINCT yk_usercard_img.usercard_img) as usercard_img,yk_usercard.usercard_id,yk_usercard.usercard_title,yk_usercard.createtime')
            ->join("LEFT JOIN yk_usercard_img ON yk_usercard.usercard_id=yk_usercard_img.usercard_id")
            ->where("yk_usercard.user_id=$search_id")
            ->group("yk_usercard.usercard_id")
            ->select();

        foreach ($data as $k=>&$v){
            $img = explode(',',$v['usercard_img']);
            $v['usercard_img'] = $img;
        }
        if ($data == ''){
            $data=array();
            $this->json_rest(19,$data);
            die();
        }
        $data = $this->imgs_url($data,'usercard_img','User');
        $this->json_rest(1,$data);
    }
    /*
     * 添加资料卡
     * param1：用户ID
     * param2：用户标题
     * param3：开始时间

     */
    public function usercard_insert(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $usercard = M('usercard');
        $data['user_id'] = $user_id;
        $data['usercard_title'] = I('post.card_title');
        $data['createtime'] = strtotime("now");
        $insert = $usercard->add($data);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'UserCard/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(0);
            die();
        }else{// 上传成功
            foreach($info as $k=>$v){
                $oss = $this->uploadOss($v['savepath'].$v['savename']);
                $con[] = array('usercard_id'=>$insert,'usercard_img'=>$oss);
            }
        }
        $usercard_img = M("usercard_img"); // 实例化User对象
        $data = $usercard_img->addAll($con);
        if ($data){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);

        }
    }
    /*
     * 工作经验
     * param：user_id
     */
    public function work(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $work = M('work');
        $data = $work
            ->field('group_concat(DISTINCT yk_work_img.work_img) as work_img,yk_work.work_id,yk_work.work_title,yk_work.start_time,yk_work.introduce')
            ->join("LEFT JOIN yk_work_img ON yk_work.work_id=yk_work_img.work_id")
            ->where("yk_work.user_id=$search_id")
            ->group("yk_work.work_id")
            ->select();
        foreach ($data as $k=>&$v){
            $img = explode(',',$v['work_img']);
            $v['work_img'] = $img;
        }
        $data = $this->imgs_url($data,'work_img','User');
        if($data==''){
            $data=array(0=>array('work_img'=>array('1'),'work_id'=>'','work_title'=>'','start_time'=>'','introduce'=>''));
            $this->json_rest(19,$data);
            die();
        }
        $this->json_rest(1,$data);
    }
    /*
     * 添加工作经验
     * param1：用户ID
     * param2：用户标题
     * param3：开始时间
     * param4：结束时间
     * param5：工作介绍
     */
    public function work_insert(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $work = M('work');
        $data['user_id'] = $user_id;
        $data['work_title'] = I('post.title');
        $data['start_time'] = I('post.stime');
        $data['introduce'] = I('post.introduce');
        $insert = $work->add($data);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'Work/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(9);
            die();
        }else{// 上传成功
            foreach($info as $k=>$v){
                $oss = $this->uploadOss($v['savepath'].$v['savename']);
                $con[] = array('work_id'=>$insert,'work_img'=>$oss);
            }
        }
        $work_img = M("work_img"); // 实例化User对象
        $data = $work_img->addAll($con);
        if ($data){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);

        }
    }
    /*
     * 删除资料卡、工作经验
     */
    public function delete_card(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $delete_type = I('post.delete_type');
        $card_id = I('post.id');
        switch ($delete_type){
            case 0:
                $delete_type = 'work';
                break;
            case 1:
                $delete_type = 'usercard';
                break;
            default:
                $this->json_rest(5);
                die();
        }
        $con['user_id'] = $user_id;
        $con[$delete_type."_id"] = $card_id;
        $del[$delete_type."_id"] = $card_id;
        $type = M($delete_type);
        $type_img = M($delete_type.'_img');
        $data = $type_img->where($del)->select();

        $type->startTrans();//开启事务
        $result = $type->where($con)->delete();
        $result1 = $type_img->where($del)->delete();

        if ($result&&$result1){
            $type->commit();
            $type_img->commit();
            foreach ($data as $k=>&$v){
                $v[$delete_type.'_img'] = 'Upload/'.$v[$delete_type.'_img'];
                unlink($v[$delete_type.'_img']);
            }
            $this->json_rest(1);
        }else{
            $type->rollback();
            $type_img->rollback();
            $this->json_rest(0);
        }
    }
    /*
     * 更改资料卡工作经验
     */
    public function change_card(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $change_type = I('post.change_type');
        $card_id = I('post.id');
        $title = I('post.title');
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
//        $where['user_id'] = $user_id;
        switch ($change_type){
            case 0:
                //修改工作经验
                $change_type = 'work';
                $where[$change_type.'_id'] = $card_id;
                $in['introduce'] = I('post.introduce');
                $in['start_time'] = I('post.time');
                $in['work_title'] = $title;
                $upload->savePath = 'Work/'; // 设置附件上传（子）目录
                break;
            case 1:
                //修改资料卡
                $change_type = 'usercard';
                $where[$change_type.'_id'] = $card_id;
                $in['usercard_title'] = $title;
                $in['createtime'] = strtotime("now");
                $upload->savePath = 'UserCard/'; // 设置附件上传（子）目录
                break;
            default:
                $this->json_rest(5);
                die();
        }
        $type = M($change_type);
        $type_img = M($change_type.'_img');
        $type_img1 = M($change_type.'_img');
        $data = $type_img->where($where)->select();
        // 上传文件
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(9);
            die();
        }else{// 上传成功
            foreach($info as $k=>$v){
                $oss = $this->uploadOss($v['savepath'] . $v['savename']);
                $con[] = array($change_type.'_id'=>$card_id,$change_type.'_img'=>$oss);
            }
            $result = $type->where($where)->save($in);
            $type->startTrans();//开启事务
            $result1 = $type_img->where($where)->delete();
            $result2 = $type_img1->addAll($con);
            if ($result1&&$result2){
                $type_img->commit();
                $type_img1->commit();
                foreach ($data as $k=>&$v){
                    $v['work_img'] = 'Upload/'.$v['work_img'];
                    unlink($v['work_img']);
                }
                $this->json_rest(1);
            }else{
                $type_img->rollback();
                $type_img1->rollback();
                $this->json_rest(0);
            }
        }
    }
    /*
     * 他的资料标签
     * result1:user_id
     * result2:nickname
     * result3:sex
     * result4:city
     * result5:brithday
     * result6:user_height
     * result7:weight
     * result8:threedimensional
     * result9：signature
     */
    public function others_data(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
//        $type = I('post.type',0);
        0==(int)$search_id?$id = substr($search_id,3,strlen($search_id)):$id=$search_id;
        $where['user_id'] = $user_id;
        $where['attu_id'] = $search_id;
//        $where['u_type'] = $type;
        $User = M('user');
        $data= $User->field('yk_user.user_img,yk_user.level,yk_user.acquirement,yk_user.user_type,yk_user.hao_level,yk_user.user_id,yk_user.nickname,yk_user.sex,yk_user.city as user_city,yk_user.birthday,yk_user.height as user_height,yk_user.weight,yk_user.threedimensional,yk_user.signature,ifnull(group_concat(distinct yk_user_type.type),"") as tag')
            ->join('LEFT join yk_user_type on yk_user.user_id=yk_user_type.user_id')
            ->where("yk_user.user_id=$search_id")
            ->find();
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
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
        $data['fans'] = $fans->field("yk_user.user_type,yk_user.user_id,yk_user.user_img")
                              ->join("LEFT JOIN yk_user ON yk_attention.user_id=yk_user.user_id")
                              ->where("yk_attention.attu_id = $search_id")
                              ->order('RAND()')
                              ->limit(10)
                              ->select();
        if ($data['fans']==''){
            $data['fans']=array();
        }
        if ($user_id!=$search_id){
            $fans1 = M('attention');
            $attu = $fans1->field('addtime')->where($where)->find();
            if ($attu==''){
                $data['attu'] = 0;
            }else{
                $data['attu'] = 1;
            }
        }
        $fan = $fans->field("count('user_id') as con")->where("attu_id=$search_id")->find();
        $data['fanscount'] = $fan['con'];
        if ($data!=0){
            $data = $this->img_url($data,'user_img','User');
            $data['fans'] = $this->img_urls($data['fans'],'user_img','User');
        }
        $data['shareURL']='http://www.yankushidai.com/share/information.html?search_id='.$search_id;
        $this->json_rest(1,$data);
    }
    /*
     * 他的主页body（安卓用）
     */
    public function others_data_basic(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $User = M('user');
        $data= $User->field('yk_user.acquirement,yk_user.nickname,yk_user.sex,yk_user.city as user_city,yk_user.birthday,yk_user.height as user_height,yk_user.weight,yk_user.threedimensional,yk_user.signature,ifnull(group_concat(distinct yk_user_type.type),"") as tag')
            ->join('LEFT join yk_user_type on yk_user.user_id=yk_user_type.user_id')
            ->where("yk_user.user_id=$search_id")
            ->find();
        $city = M('user_city');
        if ($data['threedimensional']==0){
            $data['threedimensional']='0-0-0';
        }
        $city = $city->field('city_name as user_city')->where("city_id=$data[user_city]")->find();
        $data = array_replace($data,$city);
        $this->json_rest(1,$data);
    }
    /*
     * 用户页面---我的资料标签（固定）
     * result1:user_id
     * result2:nickname
     * result3:sex
     * result4:city
     * result5:brithday
     * result6:user_height
     * result7:weight
     * result8:threedimensional
     * result9：signature
     */
    public function mine_data(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $User = M('user');
        $data= $User->field('yk_user.user_img,yk_user.level,yk_user.hao_level,yk_user.acquirement,yk_user.user_id,yk_user.nickname,yk_user.sex,yk_user.city as user_city,yk_user.kubi,yk_user.birthday,yk_user.height as user_height,yk_user.weight,yk_user.threedimensional,yk_user.signature,ifnull(group_concat(distinct yk_user_type.type),"") as tag')
            ->join('LEFT join yk_user_type on yk_user.user_id=yk_user_type.user_id')
            ->where("yk_user.user_id=$user_id")
            ->find();
        $up['lasttime'] = time();
        if(2130706433!=ip2long($_SERVER['HTTP_X_REAL_IP'])||2147483647!=ip2long($_SERVER['HTTP_X_REAL_IP'])){
		    $up['lastip'] = ip2long($_SERVER['HTTP_X_REAL_IP']);
	    }else{
  	     	 $up['lastip'] = ip2long($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
	$User->where("user_id=$user_id")->save($up);
        $city = M('user_city');
        if ($data['threedimensional']==0){
            $data['threedimensional']='0-0-0';
        }
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(6);
        $res = $redis->get($user_id);
        $res = json_decode($res,true);
        if ($res['time']==date("Y:m:d")){
            $data['showsignin'] = 0;
        }else{
            $data['showsignin'] = 1;
        }
        $room = M('room');
        $res = $room->where("user_id=$user_id")->find();
        $data['push'] = $res['push'];
        $city = $city->field('city_name as user_city')->where("city_id=$data[user_city]")->find();
        $data = array_replace($data,$city);
        if ($data!=0){
            $data = $this->img_url($data,'user_img','User');
        }elseif($data == ''){
            $this->json_rest(13);
            die();
        }
        $attenmodel = M('attention');
        $attu = $attenmodel->field('count(attu_id) as attention_num')->where("user_id=$user_id")->find();
        $fans = $attenmodel->field('count(user_id) as fans_num')->where("attu_id=$user_id")->find();
        null==$fans['fans_num']?$data['fans_num'] = '0':$data['fans_num'] = $fans['fans_num'];
        null==$attu['attention_num']?$data['attention_num'] = '0':$data['attention_num'] = $attu['attention_num'];
        $data['isopen'] = 0;
        $data['shareURL']='http://www.yankushidai.com/share/information.html?search_id='.$user_id;
        $data['videoshareURL'] = 'http://www.yankushidai.com/share/video.html?search_id='.$user_id;
        $data['day'] = 0;
        $this->json_rest(1,$data);
    }
    /*
     * 修改个人资料
     * param1：user_id（必须）
     * param2：nickname（非必须）
     * param3：sex（非必须）
     * param4：city（非必须）
     * param5：birthday（非必须）
     * param6：height（非必须）
     * param7：weight（非必须）
     * param8：threedimensional（非必须）
     * param9：signature（非必须）
     * result10：修改成功
     * result11：修改失败
     */
    public function change_mine_data(){
        $user_id = I('request.user_id');
        $token = I('request.token');
        $this->token_audit($user_id,$token);
        $nickname = I('post.nickname');
        $con['nickname'] = $nickname;
        $con['sex'] = I('post.sex');
        $con['birthday'] = I('post.birthday');
        $con['height'] = I('post.height');
        $con['weight'] = I('post.weight');
        $con['threedimensional'] = I('post.threedimensional');
        $con['signature'] = I('post.signature');
        $con['acquirement'] = I('post.acquirement');
//        $data['type'] = I('post.tag');
//        $data = explode(",",$data['type']);
        $city['city_name'] = I('post.user_city');
//        foreach ($data as $k=>$v){
//           $list[]=array('user_id'=>$user_id,'type'=>$v);
//        }
        $User = M('user');
//        $type = M('user_type');
        $model_city = M('user_city');
        $citys = $model_city->where($city)->find();
        if ($citys){
            $con['city'] = $citys['city_id'];
        }else{
            $con['city'] = $model_city->add($city);
        }
        $User->startTrans();//开启事务
        if(!$User->where("user_id=$user_id")->save($con)){// 根据条件更新记录
            $User->rollback();
//            $this->error('修改失败');
        }
//        if (!$type->where("user_id=$user_id")->delete()){
//            $type->rollback();
//        }
//        if(!$type->where("user_id=$user_id")->addAll($list)){
//            $type->rollback();
////            $this->error('删除失败');
//        }
        $User->commit();
//        $type->commit();
        $this->json_rest(1);
    }
    /*
     * 演绎资产
     * param1：user_id
     */
    public function asset(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $search_id = I('post.search_id');
        $res = $this->audit_other($search_id);
        if ($res['yanyi']!=2){
            $res = array('enroll'=>'0','creadit'=>'0%','joined'=>0,'zichan'=>'0');
            $this->json_rest(19,$res);
            die();
        }
        $User = M('user_schedule');
        $enroll = $User->field('COUNT(*) as enroll')
            ->where("user_id=$search_id")
            ->select();

        $joined = $User->field('COUNT(*) as joined')
                       ->where("user_id=$search_id AND schedule_status=3")
                       ->find();
        $arr= $User->field('count(*)')
            ->where("user_id=$search_id and schedule_status>1")
            ->select();
        $allin = (int)$arr[0]['count(*)'];
        $arr = $User->field('count(*)')
            ->where("user_id=$search_id and schedule_status=3")
            ->select();
        $joined = (int)$arr[0]['count(*)'];
        $credit = $joined/$allin*100;
        $credit .= '%';
        $credit = array('credit'=>"$credit");


        $data = array_merge($enroll[0],$credit);
        $data['joined'] = $joined;
        $data['zichan'] = '0%';
        $this->json_rest(1,$data);
    }
    /*
     * 更改才艺特长
     * param1：user_id
     * param2：acquirement
     */
    public function change_acquirement(){
        $user_id = I('post.user_id');
        $User = M('user');
        $User->acquirement = I('post.acquirement');
        $this->count['result'] = $User->where("user_id=$user_id")->save(); // 根据条件更新记录
        $this->count = json_encode($this->count,JSON_UNESCAPED_UNICODE);
        echo $this->count;
    }
    /*
     * 开启直播
     */
    public function turnon_live(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $title = I('post.title');
        $city = I('post.city');
        $nickname = I('post.nickname');
        $channel_type = I('post.channel_type');
        $room = M('room');
        $count = $room->field('yk_room.room_id,pull,IFNULL(yk_room.praise,"0") as praise,IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum')
            ->join('LEFT JOIN yk_deal ON yk_room.user_id=yk_deal.to_user_id LEFT JOIN yk_gift ON yk_gift.gift_type=yk_deal.gift_type')
            ->where("yk_room.user_id=$user_id")
            ->find();
        $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name'=>'1134170411178481',
            'app_name'=>'yanku');
        $token = new HXController($options);
        $content = array(
            "title"=> "$title",
            "desc"=>"server create group",
            "max_users"=>200000,
            "anchor"=>$user_id
        );
        $result = $token->liverooms($content);
//        if (strlen($result)>=15 || $result==null){
//            $this->json_rest(16);
//            die();
//        }
        $count['chatroom'] = $result['data']['chatroom_id'];
        $count['count'] = 0;
        /**测试预留**/
        $count['actionimg'] = 'http://rest.yankushidai.com/Upload/go.png';
        $count['actionurl'] = 'http://www.yankushidai.com/travelofchina/index.html';
        /**测试预留**/
        $this->json_rest(1,$count);

        $count['city'] = $city;
        $count['title'] = $title;
        $my = M('user');
        $data = $my->field('user_id,user_img,nickname,sex,level,hao_level')
            ->where("user_id = $user_id")
            ->find();
        $data['channel_type'] = $channel_type;
        $count = array_merge($count,$data);
        $count = $this->img_url($count,'user_img','User');
        $data = json_encode($count,JSON_UNESCAPED_SLASHES);
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $res = $redis->set($user_id,$data);
//        if (!$redis->get($user_id)){
//            sleep(2);
//            $redis->set($user_id,$data);
//        }
        $redis->select(3);
        $redis->sAdd($channel_type,$user_id);
        $data = M('attention');
        $data = $data->field('user_id')->where('attu_id=1')->select();
        $rand = rand(0,3);
        $msg = $this->onlive_msg($rand,$nickname);
        $audince = array();
        foreach ($data as $k=>$v){
            $audince[]=$v['user_id'];
        }
        if (empty($audince)){
            die();
        }
        require 'vendor/autoload.php';
        $client = new \JPush\Client($this->app_key, $this->master_secret);
        $client->push()
            ->setPlatform(array('ios', 'android'))
            ->addAlias($audince)
            ->setNotificationAlert('开播通知')
            ->iosNotification(array(
                'title' => '开播通知',
                'body' => $msg,
                'sound' => 'sound.caf',
                'badge' => '+1',
                // 'content-available' => true,
                // 'mutable-content' => true,
                'category' => 'jiguang',
                'extras' => array(
                    'type' => '3',
                    'key' => (string)$user_id
                ),
            ))
            ->androidNotification($msg,array(
                'title' => '开播通知',
                // 'build_id' => 2,
                'extras' => array(
                    'type' => '3',
                    'key' => (string)$user_id,
                ),
            ))
            ->send();

    }
    /*
     * 直播列表
     */
    public function live_list(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $channel_type = I('post.channel_type',0);
        $banner = M('activity');
        $list['banner'] = $banner->select();
        $list['banner'] = $this->img_urls($list['banner'],'activity_img','Activity');
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        switch ($channel_type){
            case 0:
                $redis->select(2);
                $key = $redis->keys('*');
                break;
            case 1:
                $redis->select(2);
                $model = M('attention');
                $con = $model->field('attu_id')
                    ->where("user_id=$user_id")
                    ->select();
                foreach ($con as $k=>$v){
                    $redis->exists($v[attu_id])?$key[$k] = $v[attu_id]:'';
                }
                break;
            case 2:
                $redis->select(3);
                $key = $redis->sGetMembers(2);
//                $redis->select(2);
//                $key = $redis->getMultiple($key);
                break;
            case 3:
                $redis->select(3);
                $key = $redis->sGetMembers(3);
                break;
            case 4:
                $redis->select(3);
                $key = $redis->sGetMembers(4);
                break;
            case 5:
                $redis->select(3);
                $key = $redis->sGetMembers(5);
                break;
            default :
                $this->json_rest(5);
                die();
        }
        if (empty($key)){
            $list['data'] = array();
            $this->json_rest(1,$list);
            die();
        }
//        $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
//            'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
//            'org_name'=>'1134170411178481',
//            'app_name'=>'yanku');
//        $token = new HXController($options);
        $redis->select(2);
        $key = $redis->mget($key);
        if ($key[0]==null){
            $this->json_rest(1,array());
            die();
        }
        foreach ($key as $k=>&$v){
            $v = json_decode($v,TRUE);
        }
        $list['data'] = $key;
//        foreach ($key as $k=>$v){
////            MGET
//            $con[$v] = json_decode($redis->get($v),TRUE);
//            $count[$v] = $token->getChatRoomDetail($con[$v]['chatroom']);
//            $count = isset($count[$v]['data']['data']['affiliations_count'])?$count[$key]['data']['data']['affiliations_count']:0;
//            $con[$v]['count'] = $count;
//            $list['data'][] = $con[$v];
//        }
        $this->json_rest(1,$list);
    }
    /*
     * 关闭直播
     */
    public function turnoff_live()
    {
        $user_id = I('post.user_id');
        $con['user_id'] = $user_id;
        $praise = I('post.praise');
        $con['praise'] = $praise;
        $con['kubi'] = I('post.kubi');
        $con['view'] = I('post.view');
        $con['starttime'] = I('post.starttime');
        $con['endtime'] = I('post.endtime');
        $channel_type = I('post.channel_type');
        $room = M('room');
        $data['praise'] = $praise;
        $room->where("user_id=$user_id")->save($data);
        $history = M('live_history');
        $count = $history->add($con);
        if ($count) {
            $redis = new \Redis();
            $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
            $redis->select(2);
            $result = json_decode($redis->get($user_id), true);
            $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA',
                'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
                'org_name' => '1134170411178481',
                'app_name' => 'yanku');
            $token = new HXController($options);
            $result = $token->deleteChatRoom($result['chatroom']);
            if ($result){
                $redis->delete($user_id);
                $redis->select(3);
                $redis->SREM($channel_type,$user_id);
                $this->json_rest(1);
            }else{
                $this->json_rest(0);
            }

        }
    }
    /*
     * 关注接口
     */
    public function attention_user(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $attu_id = I('post.attu_id');
        if ($user_id!=$attu_id){
            $con['user_id'] = $user_id;
            $con['attu_id'] = $attu_id;
            $con['addtime'] = time();
            $rand = rand(0,3);
            $msg = $this->attu_msg($rand);
            $count = M('attention');
            $count = $count->data($con)->add();
            if ($count!=''){
                require 'vendor/autoload.php';
                $client = new \JPush\Client($this->app_key, $this->master_secret);
                try {
                    $client->push()
                        ->setPlatform('all')
                        ->addAlias((string)$attu_id)
                        ->options(array('apns_production'=>true))
                        ->setNotificationAlert('关注通知')
                        ->iosNotification(array(
                            'title' => '关注通知',
                            'body' => $msg,
                            'sound' => 'sound.caf',
                            'badge' => '+1',
                            // 'content-available' => true,
                            // 'mutable-content' => true,
                            'category' => 'jiguang',
                            'extras' => array(
                                'type' => '2',
                                'key' => '',
                            ),
                        ))
                        ->androidNotification(array(
                            'title' => '关注通知',
                            'body' => $msg,
                            // 'build_id' => 2,
                            'extras' => array(
                                'type' => '2',
                                'key' => '',
                            ),
                        ))
                        ->send();
                    $this->json_rest(1);
                } catch (\JPush\Exceptions\APIConnectionException $e) {
                    // try something here
    //                print $e;
                    $this->json_rest(20);
                } catch (\JPush\Exceptions\APIRequestException $e) {
                    // try something here
    //                print $e;
                    $this->json_rest(20);
                }

            }else{
                $this->json_rest(0);
            }
        }else{
            $this->json_rest(0);
        }
    }
    /*
     * 取消关注接口
     */
    public function unattention(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $attu_id = I('post.attu_id');
        $count = M('attention');
        $count = $count->where("user_id=$user_id AND attu_id = $attu_id")->delete();
        if ($count){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);
        }
    }
    /*
     * 观看直播
     */
    public function inlive(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $id = I('post.id');
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
        $data['actionimg'] = $key['actionimg'];
        $data['actionurl'] = $key['actionurl'];
        null==$key['isRtcing']?$data['isRtcing'] = 0:$data['isRtcing'] = $key['isRtcing'];
        null==$key['rtcAnchorID']?$data['rtcAnchorID'] = 0:$data['rtcAnchorID'] = $key['rtcAnchorID'];
        if ($key){
            $data['online'] = 1;
        }else{
            $data['online'] = 0;
        }
        $att = M('attention');
        $att1 = $att->where("user_id=$user_id AND attu_id=$id")->select();
        if ($att1 || $user_id == $id){
            $data['attention'] = 1;
        }else{
            $data['attention'] = 0;
        }
        $chatroom = $key['chatroom'];
        $data['title'] = $key['title'];
        $all = array();
        $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name' => '1134170411178481',
            'app_name' => 'yanku');
        $token = new HXController($options);
        $count = $token->getChatRoomDetail($chatroom);
        foreach ($count['data'][0]['affiliations'] as $k => $v) {
            foreach ($v as $k => $v) {
                Array_push($all, $v);
            }
        }
        if (count($all) < 10) {
            for ($i = 0;$i < 10;$i++){
                $fake[] = rand(833,87);
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
        $data['shareURL'] = 'http://www.yankushidai.com/share/video.html?search_id='.$id;
        $data['view'] = $this->img_urls($data['view'],'user_img','User');
        $this->json_rest(1, $data);
    }
        public function nameaudit(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $con['realname'] = I('post.realname');
            $con['IDcard_number'] = I('post.IDcard_number');
            $console['create_time'] = time();
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = 'Audit/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
//                $this->error($upload->getError());
                $this->json_rest(0);
                die();
            }else{// 上传成功 获取上传文件信息
                foreach($info as $k=>$v){
                    if ($k=='user_positive'){
                        $con['user_positive'] = $v['savepath'].$v['savename'];
                        $oss = $this->uploadOss($con['user_positive']);
		    }elseif ($k == 'user_back'){
                        $con['user_back'] = $v['savepath'].$v['savename'];
                        $oss = $this->uploadOss($con['user_back']);
	            }
                }
            }
            $console['user_id'] = $user_id;
            $console['status'] = 1;
            $console['create_time'] = time();
            $user = M('user');
            $audit = M('nameaudit');
            $user->startTrans();//开启事务
            $result = $user->where("user_id=$user_id")->save($con);// 根据条件更新记录
            $result1 = $audit->add($console);
            if ($result&&$result1){
                $user->commit();
                $audit->commit();
            }else{
                // 根据条件更新记录
                $user->rollback();
                $audit->rollback();
            }
            $this->json_rest(1);
        }
        /*
         * 实名认证查询
         */
        public function audit_in(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $nameaudit = M('nameaudit');
            $con = $nameaudit->field('IFNULL(status,0) as status')->where("user_id=$user_id")->order('status ASC')->find();
            if (null == $con['status']){
                $data['nameaudit'] = 0;
            }else{
                $data['nameaudit'] = $con['status'];
            }
            $yanyi = M('yanyiaudit');
            $con = $yanyi->field('IFNULL(status,0) as status')->where("user_id=$user_id")->order('status')->find();
            $data['yanyi'] = $con['status'];
            if (!$data['yanyi'] ){
                $data['yanyi'] = 0;
            }
            $res = M('boss')->field("status")->where("user_id=$user_id")->find();
            NULL == $res['status']?$data['boss'] = 0:$data['boss'] = $res['status'];
            $this->json_rest(1,$data);
        }
    /*
     * 他的实名认证查询
     */
    public function audit_other($user_id){
        $nameaudit = M('nameaudit');
        $con = $nameaudit->field('IFNULL(status,0) as status')->where("user_id=$user_id")->find();
        $data['nameaudit'] = $con['status'];
        if ($data['nameaudit']==2 ){
            $yanyi = M('yanyiaudit');
            $con = $yanyi->field('IFNULL(status,0) as status')->where("user_id=$user_id")->find();
            $data['yanyi'] = $con['status'];
            if (!$data['yanyi'] ){
                $data['yanyi'] = 0;
            }
        }elseif ($data['nameaudit'] == 1){
            $data['yanyi'] = 0;

        }else{
            $data['nameaudit'] = 0;
            $data['yanyi'] = 0;

        }
        return $data;
    }
        public function newyanyi(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
//            $data = array();
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = 'Yanyi/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->json_rest(0);
                die();
            }else{// 上传成功
                $ins['AliPay'] = I('post.AliPay');
                foreach($info as $k=>$v){
                    $oss = $this->uploadOss($info['savepath'] . $info['savename']);
                    $data[] = array("user_id"=>(int)$user_id,"yanyi_img"=>$oss);
                }
//                var_dump($info);
//                echo "<br>1<br>";
//                var_dump($data);
//                die();
            }
            $user = M('user');
            $audit = M('yanyi');
            $yanyi = M('yanyiaudit');
            $con['user_id'] = $user_id;
            $con['create_time'] = strtotime("now");
            $type = I('post.type');
            $con1['type'] = explode(',',$type);
            $model_type = M('user_type');
            $model_type1 = M('user_type');
            foreach ($con1['type'] as $key=>$v){
                $v==0?'':$dataList[] = array('user_id'=>$user_id,'type'=>$v);
            }
            $user->startTrans();//开启事务
            if(!$user->where("user_id=$user_id")->save($ins)){// 根据条件更新记录
                $user->rollback();
            }
            if(!$audit->addAll($data)){// 根据条件更新记录
                $audit->rollback();
            }
            if(!$model_type1->where("user_id=$user_id")->delete()){// 根据条件更新记录
                $model_type1->rollback();
            }
            if(!$model_type->addAll($dataList)){// 根据条件更新记录
                $model_type->rollback();
            }
            if(!$yanyi->add($con)){// 根据条件更新记录
                $yanyi->rollback();
            }
            $user->commit();
            $model_type1->commit();
            $model_type->commit();
            $audit->commit();
            $yanyi->commit();
            $this->json_rest(1);
        }
        public function suggest(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $con['suggestion'] = I('post.suggestion');
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Upload/'; // 设置附件上传根目录
            $upload->savePath = 'Suggest/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->json_rest(9);
                die();
            }else{// 上传成功
                foreach($info as $k=>$v){
                    $con[$k] = $v['savepath'].$v['savename'];
                }
            }
            $con['user_id'] = $user_id;
            $insert = M('suggest');
            $insert = $insert->data($con)->add();
            if ($insert){
                $this->json_rest(1);
            }else{
                $this->json_rest(0);
            }
        }
        /*
         * 参加通告
         */
        public function signupschedule(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $schedule_id = I('post.schedulet_id');
            $count = M('user_schedule');
            $rank = $count->field("count($schedule_id) as count")->where("schedule_id=$schedule_id")->find();
            $con['ranking'] = $rank['count'];
            $con['signtime'] = strtotime("now");
            $con['user_id'] = $user_id;
            $con['schedule_id'] = $schedule_id;
            $insert = M('user_schedule');
            $insert = $insert->data($con)->add();

            if ($insert){
                $this->json_rest(1);
            }else{
                $this->json_rest(0);
            }
        }
        /*
         * ***********直播心跳检测***********
         * ***********禁止随便开启***********
         */
        public function heartbeat1111(){
            $redis = new \Redis();
            $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
            $redis->select(2);
            $key = $redis->keys('*');
            if($key != ''){
                $redis->select(4);
                foreach ($key as $k=>$v){
                    $redis->select(4);
                    if (!$redis->exists($v)){
                        $redis->select(2);
                        $key = $redis->get($v);
                        $key = json_decode($key,true);
//                        $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA',
//                            'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
//                            'org_name' => '1134170411178481',
//                            'app_name' => 'yanku');
//                        $token = new HXController($options);
//                        $result = $token->deleteChatRoom($key['chatroom']);
                        $redis->select(3);
                        $redis->SREM((int)$key['channel_type'],(int)$v);
                        $key2 = $redis->select(2);
                        $key = $redis->delete($v);
                    }
                }
            }else{
                die();
            }
        }
        /***

         正式的，临时关闭

         ****/
        public function heartbeat(){
            $redis = new \Redis();
            $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
            $redis->select(2);
            $key1 = $redis->keys('*');
            $redis->select(4);
            $key2 = $redis->keys('*');
            $difference =  array_diff($key1,$key2);
            if ($difference!=''){
                $redis->select(2);
                $val = $redis->mget($key1);
                foreach ($val as $k=> $v){
                    $value = json_decode($v,true);
                    $channel_type = $value['channel_type'];
                    $data[$channel_type] = $value['user_id'];
                    $del[] = $k;
                }
                $redis->del($difference);
                $redis->select(3);
                foreach ($data as $k=>$v){
                    $redis->sRem($k,$v);
                }
            }
        }
        /*
         * 直播间心跳请求
         */
    public function heartrequest(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $view = I('post.view');
        $con['channel_type'] = I('post.channel_type');
        $praise= I('post.praise',1);
        $con['praise'] = $praise;
        $kubi = I('post.kubi');
        $con['kubi'] = $kubi;
        $con['view'] = $view;
//        $con['view'] = I('post.view');
        $con['starttime'] = I('post.starttime');
        $con['endtime'] = I('post.endtime');
        $con = json_encode($con,JSON_UNESCAPED_SLASHES);
        //开启心跳检测
//            if ($this->onlive==0){
//                $this->onlive=1;
//                $this->heartbeat();
//            }
        $time = strtotime("now");
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->get($user_id);
        if ($key){
            $key = json_decode($key,true);
            $key['count'] = $view;
            $key['praise'] = $praise;
            $key['kubi'] = $kubi;
            $key = json_encode($key,JSON_UNESCAPED_SLASHES);
            $redis->set($user_id,$key);
        }else{
            $this->json_rest(1);
            die();
        }
        $redis->select(4);
        $key = $redis->set($user_id,$con);
        $redis->EXPIRE($user_id, 15);
        $this->json_rest(1);

    }
        /*
         * 修改头像
         */
        public function change_userimg(){
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
            $upload->savePath  =     'User/';
            // 上传单个文件
            $info   =   $upload->uploadOne($_FILES['image']);
            if(!$info) {// 上传错误提示错误信息
                $this->json_rest(0);
                die();
            }else{// 上传成功 获取上传文件信息
                $con['user_img'] = $this->uploadOss($info['savepath'].$info['savename']);
                $user = M('user');
                $url = $user->field('user_img')->where("user_id=$user_id")->find();
                unlink('Upload/'.$url['user_img']);
                $insert = $user->where("user_id=$user_id")->save($con);
                $con = $this->img_url($con,'user_img');
                if ($insert){
                    $this->json_rest(1,$con);
                }else{
                    $this->json_rest(0);
                }
            }

        }
    /*
     *  直播间送礼
     */
    public function deal(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $to_user_id = I('post.to_user_id');
        $gift_type = I('post.gift_type');
        $con['to_user_id'] = $to_user_id;
        $con['from_user_id'] = $user_id;
        $con['gift_type'] = $gift_type;
        $con['number'] = I('post.number',1);
        $con['pay_time'] = strtotime("now");
        $deal = M('deal');
        $toUser = M('user');
        $fromUser = M('user');
        $price = M('gift');
        $where['gift_type'] = $gift_type;
        $price = $price->field('price')->where($where)->find();
        $price = $price['price'];
        $deal->startTrans();//开启事务
        $result = $deal->data($con)->add();
        $result1 = $fromUser->where("user_id=$user_id")->setDec('kubi',$price);
        $result2 = $toUser->where("user_id=$to_user_id")->setInc('kubi',$price);
        if ($result && $result2 && $result1){
            $deal->commit();
            $fromUser->commit();
            $toUser->commit();
            $this->json_rest(1);
        }else{
            $deal->rollback();
            $fromUser->rollback();
            $toUser->rollback();
            $this->json_rest(14);
        }
    }
    /*
     * 获取直播列表里直播间信息
     */
    public function getliveinfo($id){
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->get($id);
        $redis->close();
        return $key;
    }
    /*
     * 他的页面和webapp进入直播间功用借口
     */
    public function in_live(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $id = I('post.id');
        $key = $this->getliveinfo($id);
        $key = json_decode($key,true);
        if($key){
            $this->json_rest(1,$key);
        }else{
            $this->json_rest(15,array('user_id'=>'0'));
        }
    }
    public function schedule_result()
    {
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $insert = M('schedule_report');
        $status = M('user_schedule');
        $schedule_id = I('post.schedule_id');
        $status = $status->field('schedule_status')->where("user_id=$user_id AND schedule_id=$schedule_id")->find();
        if ($status['schedule_status']==3){
            $ins['user_id'] = $user_id;
            $ins['schedule_id'] = $schedule_id;
            $ins['role'] = I('post.role');
            $ins['salary'] = I('post.salary');
            $ins['salary_type'] = I('post.salary_type');
            $ins['report'] = I('post.report');
            $ins['status'] = I('post.status');
            $insert = $insert->add($ins);
            if($insert){
                $this->json_rest(1);
            }else{
                $this->json_rest(0);
            }
        }else{
            $this->json_rest(0);
        }
    }

    public function ceshi(){
        $user_id = I('post.user_id');
        $user = M('user');
        $res = array('day'=>2);
        $result = exp_rule($res['day']);
        echo $user->where("user_id=$user_id")->setInc('kubi',$result['kubi']);
        die();
        $day = 30;
        var_dump(exp_rule($day));die();
        $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name'=>'1134170411178481',
            'app_name'=>'yanku');
        $token = new HXController($options);
        $stream = array(
            "pc_pull"=>"http://pullhls.yankushidai.com/live/LIVEEJ15BD7819818/index.m3u8", //PC端拉流地址
            "pc_push"=>"rtmp://pushrsa.yankushidai.com/live/8dae218f972f7b37?vdoid=1497343350", //PC端推流地址
            "mobile_push"=>"rtmp://pushrsa.yankushidai.com/live/8dae218f972f7b37?vdoid=1497343350", //MOBILE端拉流地址
            "mobile_pull"=>"http://pullhdl.yankushidai.com/live/8dae218f972f7b37.flv", //MOBILE端推流地址
        );
        var_dump($token->setLiveroomsstream($stream));
        echo "----------------------------<br>";
        $ins = array("superadmin"=>321);
        var_dump($token->setChatroomsuper($ins));
        echo "----------------------------<br>";
        $content = array(
            "title"=> "South Africa Live",
            "desc"=>"server create group",
            "anchor"=>321
        );
        var_dump($token->liverooms($content));
        die();
//        $re = M('work');
//        $in['introduce'] = 'w3www';
//        $in['start_time'] = 1499011200;
//        $in['work_title'] = 'ww3w';
//        $where['work_id'] = 111;
//        $where['user_id'] = 308;
//        echo $re->where($where)->save($in);
//        die();
//        if (date("Y:m:d")<date("Y:m:d",strtotime("+1 day"))) {
//         echo 1;
//        }
//        die();
//        $user_id = I('post.user_id');
//        $redis = new \Redis();
//        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
//        //6。确认当天第一次签到
//        $redis->select(6);
//        echo  $redis->get($user_id);
//die();
        //        $res = $redis->sAdd(1,$user_id);

//        $day = I('post.day',1);
//        echo $day+1;
//        echo strtotime(date('Ymd')) + 86400;
//        echo strtotime(date('Y-m-d',time()));
//        echo date("Y-m-d H:i:s");
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->keys('*');
        var_dump($key);
        $this->heartbeat();

    }
    public function Sign_in(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $day = I('post.day',1);
        $time = date("Y:m:d");
//        var_dump($rule);die;
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        //6。确认当天第一次签到
        $redis->select(6);
        $res = $redis->get($user_id);
        $res = json_decode($res,true);
        if ($res['time']!=date("Y:m:d",strtotime("-1 day"))||$res=='') {
            $res['day'] = 1;
            $data['kubi'] = 20;
            $data['experience'] = 20;
            $data['day'] = 1;
            $res['time'] = $time;
            $res = json_encode($res,JSON_UNESCAPED_SLASHES);
            $res = $redis->set($user_id,$res);
            $user = M('user');
            $sql = $user->field("kubi,experience")->where("user_id=$user_id")->find();
            $sql['kubi'] = $sql['kubi']+$data['kubi'];
            $sql['experience'] = $sql['experience']+$data['experience '];
            $reslt = $user->where("user_id=$user_id")->save($sql);
            $sql['day'] = $res['day'];
            if ($reslt){
                $this->json_rest(1,$data);
            }else{
                $this->json_rest(0);
            }
        }elseif ($res['time']==date("Y:m:d")){
            $this->json_rest(0);
        }else{
            $res['day'] += 1;
            $result = exp_rule($res['day']);
            $res['time'] = $time;
            $result['day'] = $res['day'];
            $res = json_encode($res,JSON_UNESCAPED_SLASHES);
            $user = M('user');
            $sql = $user->field("kubi,experience")->where("user_id=$user_id")->find();
            $sql['kubi'] = $sql['kubi']+$result['kubi'];
            $sql['experience'] = $sql['experience']+$result['experience'];
            $reslt = $user->where("user_id=$user_id")->save($sql);
            $sql['kubi'] = $sql['kubi']-$result['kubi'];
            if ($reslt){
                $res = $redis->set($user_id,$res);
                $this->json_rest(1,$sql);
            }else{
                $this->json_rest(0);
            }
//            $user1 = M('user');
//            $user->startTrans();
//            $up1 = $user->where("user_id=$user_id")->setInc('kubi',$result['kubi']);
//            $up2 = $user1->where("user_id=$user_id")->setInc('experience',$result['exp']);
//            if ($up1&&$up2){
//                $user->commit;
//                $user1->commit;
//                $res = $redis->set($user_id,$res);
//                $result['day'] = $day+1;
//                $this->json_rest(1,$result);
//            }else{
//                $user->rollback();
//                $user1->rollback();
//                $this->json_rest(0);
//            }
        }
        die();
//            if ($res!=date("Y:m:d",strtotime("-1 day"))) {
//            $redis->set($user_id,$time);
//            $data['day']=1;
//            $redis->sMove($day,1,$user_id);
//        }else{
//            //7.每天登陆集合
//            $redis->select(7);
//            $res = $redis->sIsMember($day,$user_id);
//            $redis->sMove($day,$day+1,$user_id);
//            $redis->expire($day+1, 10);
//            $data['day']=$day+1;
//        }
//        //$dietime = strtotime(date("Y-m-d",strtotime("+1 day")));
////        $dietime = strtotime(date('Ymd')) + 86400;
////        $redis->expireAt($user_id,$dietime); //当天24h
//        $this->json_rest(1,$data);
    }
    public function turnon_sa(){
        $redis = new \Redis();
        $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        if ($redis->exists(321)){
            $key = $redis->get(321);
            $key = json_decode($key,true);
            $this->json_rest(1,$key);
        }else{
            $user_id = I('post.user_id');
            $token = I('post.token');
            $this->token_audit($user_id,$token);
            $title = I('post.title');
            $city = I('post.city');
            $nickname = I('post.nickname');
            $channel_type = I('post.channel_type');
            $room = M('room');
            $count = $room->field('yk_room.room_id,pull,IFNULL(yk_room.praise,"0") as praise,IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum')
                ->join('LEFT JOIN yk_deal ON yk_room.user_id=yk_deal.to_user_id LEFT JOIN yk_gift ON yk_gift.gift_type=yk_deal.gift_type')
                ->where("yk_room.user_id=$user_id")
                ->find();
            $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
                'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
                'org_name'=>'1134170411178481',
                'app_name'=>'yanku');
            $token = new HXController($options);
            $content = array(
                "title"=> "$title",
                "desc"=>"server create group",
                "max_users"=>200000,
                "anchor"=>$user_id
            );
            $result = $token->liverooms($content);
    //        if (strlen($result)>=15 || $result==null){
    //            $this->json_rest(16);
    //            die();
    //        }
            $count['chatroom'] = '22885796216833';
//            $count['chatroom'] = $result['data']['chatroom_id'];
            $count['count'] = 0;
            /**测试预留**/
            $count['actionimg'] = 'http://rest.yankushidai.com/Upload/go.png';
            $count['actionurl'] = 'http://www.yankushidai.com/travelofchina/index.html';
            /**测试预留**/
            $this->json_rest(1,$count);

            $count['city'] = $city;
            $count['title'] = $title;
            $my = M('user');
            $data = $my->field('user_id,user_img,nickname,sex,level,hao_level')
                ->where("user_id = $user_id")
                ->find();
            $data['channel_type'] = $channel_type;
            $count = array_merge($count,$data);
            $count = $this->img_url($count,'user_img','User');
            $data = json_encode($count,JSON_UNESCAPED_SLASHES);
            $redis = new \Redis();
            $redis -> pconnect("localhost",6379); //localhost也可以填你服务器的ip
            $redis->select(2);
            $res = $redis->set($user_id,$data);
    //        if (!$redis->get($user_id)){
    //            sleep(2);
    //            $redis->set($user_id,$data);
    //        }
            $redis->select(3);
            $redis->sAdd($channel_type,$user_id);
            $data = M('attention');
            $data = $data->field('user_id')->where('attu_id=1')->select();
            $rand = rand(0,3);
            $msg = $this->onlive_msg($rand,$nickname);
            $audince = array();
            foreach ($data as $k=>$v){
                $audince[]=$v['user_id'];
            }
            if (empty($audince)){
                die();
            }
            require 'vendor/autoload.php';
            $client = new \JPush\Client($this->app_key, $this->master_secret);
            $client->push()
                ->setPlatform(array('ios', 'android'))
                ->addAlias($audince)
                ->setNotificationAlert('开播通知')
                ->iosNotification(array(
                    'title' => '开播通知',
                    'body' => $msg,
                    'sound' => 'sound.caf',
                    'badge' => '+1',
                    // 'content-available' => true,
                    // 'mutable-content' => true,
                    'category' => 'jiguang',
                    'extras' => array(
                        'type' => '3',
                        'key' => (string)$user_id
                    ),
                ))
                ->androidNotification($msg,array(
                    'title' => '开播通知',
                    // 'build_id' => 2,
                    'extras' => array(
                        'type' => '3',
                        'key' => (string)$user_id,
                    ),
                ))
                ->send();
        }
    }
    public function yanyi(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $ins['AliPay'] = I('post.AliPay',0);
        $ins['realname'] = I('post.realname');
        $type = I('post.type');
        $user = M('user');
        $alipay = $user->where("user_id=$user_id")->save($ins);
        $con['user_id'] = $user_id;
        $con['create_time'] = strtotime("now");
        $yanyi = M('yanyiaudit');
        $yanyi_id = $yanyi->add($con);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = 'Yanyi/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->json_rest(0);
            die();
        }else{// 上传成功
            foreach($info as $k=>$v){
                $oss = $this->uploadOss($v['savepath'] . $v['savename']);
                $data[] = array("yanyi_id"=>(int)$yanyi_id,"user_id"=>(int)$user_id,"yanyi_img"=>$oss);
            }
            $model_type = M('user_type');
            $model_type->where("user_id=$user_id")->delete();
        }
        $audit = M('yanyi');
        $con1['type'] = explode(',',$type);
        foreach ($con1['type'] as $key=>$v){
            $v==0?'':$dataList[] = array('user_id'=>$user_id,'type'=>$v);
        }
        $type = $model_type->addAll($dataList);
        $audit = $audit->addAll($data);
        if ($type&&$audit){
            $this->json_rest(1);
        }else{
            $this->json_rest(0);

        }
    }
    public function chatroom(){
        $m = '《旅空行者》--南非印象';
        echo json_encode($m,JSON_UNESCAPED_SLASHES);
        die();
        $u= M('user');
        $m = $u->where("user_id=321")->setInc('experience',10);
        var_dump($m);
        $m = M('room');
        $m = $m->field('user_id')->select();
        $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
            'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
            'org_name'=>'1134170411178481',
            'app_name'=>'yanku');
        $hx = new HXController($this->options);
        $stream_id = substr(md5(time()),1,16);
//        $stream = array(
//            "pc_pull"=>"http://pullhls.yankushidai.com/live/$stream_id/index.m3u8", //PC端拉流地址
//            "pc_push"=>"rtmp://pushr.yankushidai.com/live/$stream_id?vdoid=".time(), //PC端推流地址
//            "mobile_push"=>"rtmp://pushr.yankushidai.com/live/$stream_id?vdoid=".time(), //MOBILE端拉流地址
//            "mobile_pull"=>"http://pullhdl.yankushidai.com/live/$stream_id.flv" //MOBILE端推流地址
//        );
//        var_dump($hx->setLiveroomsstream($stream));
//        die();
        foreach ($m as $k=>$v){
            $ins = array("superadmin"=>"$v[user_id]");
//            $result[] = $v;
            $result[] = $hx->setChatroomsuper($ins);
        }
        echo '<--------------------------->';
        var_dump($result);
    }
    private function push(){
        require 'vendor/autoload.php';
        $client = new \JPush\Client($this->app_key, $this->master_secret);
        $msg = '万圣前夜，黑暗降临，百鬼夜行，恐怖升级！演库&HIGHTOWN BAR联手打造万圣大型主题趴，邀您参与直播互动，与我们一起“鬼混”吧！';
        var_dump($client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->options(array('apns_production'=>true))
            ->setNotificationAlert('直播通知')
            ->iosNotification(array(
                'title' => '直播通知',
                'body' => $msg,
                'sound' => 'sound.caf',
                'badge' => '+1',
                // 'content-available' => true,
                // 'mutable-content' => true,
                'category' => 'jiguang',
                'extras' => array(
                    'type' => '3',
                    'key' => (string)'363'
                ),
            ))
            ->androidNotification($msg,array(
                'title' => '直播通知',
                // 'build_id' => 2,
                'extras' => array(
                    'type' => '3',
                    'key' => (string)'363',
                ),
            ))
            ->send()
        );
    }
    function login_third(){
        $ins['login_type'] = I('post.login_type');
        $ins['uid'] = I('post.uid');
        $model_user = M('user');
        $data = $model_user->field('yk_user.user_id,yk_user.nickname,yk_user.kubi,yk_user.user_type,yk_user.user_img,yk_user.sex,yk_user.level,yk_user.signature,yk_room.push,yk_user.mobile,yk_user.hao_level,yk_user.status')
            ->join('LEFT JOIN yk_room ON yk_user.user_id=yk_room.user_id LEFT JOIN yk_third ON yk_user.user_id=yk_third.user_id')
            ->where($ins)
            ->find();
        if (null==$data||1==$data['status']){
            $res = array('user_id'=>'','nickname' => '', 'kubi' => '', 'user_type' => '', 'user_img' => '', 'sex' => '', 'level' => '', 'signature' => '', 'push' => '', 'mobile' => '', 'hao_level' => '', 'token' => '', 'isopen' => '', 'shareURL' => '');
            $this->json_rest(0, $res);
            die();
        }elseif (0 == $data['sex']) {
            $res = array('nickname' => '', 'kubi' => '', 'user_type' => '', 'user_img' => '', 'sex' => '', 'level' => '', 'signature' => '', 'push' => '', 'mobile' => '', 'hao_level' => '', 'token' => '', 'isopen' => '', 'shareURL' => '');
            $res['user_id'] = $data['user_id'];
            $this->json_rest(18, $res);
            die();
        }
        $thr = M('third');
        $res = $thr->field('customer_type')->where("user_id=$data[user_id]")->find();
        if (!$res){
            $data['customer_type'] = 0;
        }else{
            $data['customer_type'] = 1;
        }
        $token = substr(md5(time()), 13);
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(1);
        $redis->set($data['user_id'], $token);
        $redis->expireAt($data['user_id'], strtotime(date("Y-m-d", strtotime("+3 month"))));
        $data['token'] = $redis->get($data['user_id']);
        //Applepay内购开关
        $data['isopen'] = 0;
        $data['shareURL'] = 'http://www.yankushidai.com/share/information.html?search_id=' . $data['user_id'];
        if ($data['push'] == '') {
            $data['push'] == '';
        }
        $data = $this->img_url($data, 'user_img', 'User');
        $this->json_rest(1, $data);
    }
    function register_third()
    {
        $ins['login_type'] = I('post.login_type');
        $ins['uid'] = I('post.uid');
        $thr = M('third');
        $res = $thr->where($ins)->find();
        $ins['customer_type'] = 1;
        if ($res){
            $this->json_rest(10, array('user_id' => $res['user_id']));
            die();
        }
        $redis = new \Redis();
        $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
        $redis->select(10);
        $max = $redis->get('max_mobile');
        $redis->set('max_mobile', $max + 1);
        $con['mobile'] = $max;
        $con['password_salt'] = 0000;
        $con['password'] = 000000;
        $con['nickname'] = $max;
        $con['registertime'] = time();
        $con['lasttime'] = time();
        $con['channel'] = 0;
        $model_user = M('user');
        $login = $model_user->field('nickname')->where($con)->find();
        if (!empty($login)) {
            $this->json_rest(10, array('user_id' => '-1'));
            die();
        } else {
            $insert = $model_user->add($con);
            if (!$insert){
                $this->json_rest(26);
                die();
            }
            $data['user_id'] = $insert;
            $ins['user_id'] = $insert;
//            $thr = M('third');
            $ins = $thr->add($ins);
            $options = array('client_id' => 'YXA6ryLaYB6qEee9ag-MCEtXOA', 'client_secret' => 'YXA6P275ejRYIcHpCHj_eqtonJUmJb4', 'org_name' => '1134170411178481', 'app_name' => 'yanku');
            $token = new HXController($options);
            $result1 = $token->createUser("$insert", 'yanku321');
            $result = $token->createUser("bos$insert", 'yanku321');
            empty($result1) ? $this->json_rest(0, array('user_id' => '-1')) : $this->json_rest(1, $data);
        }
    }
    function bingding_mobile(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $ins['login_type'] = I('post.login_type');
        $ins['uid'] = I('post.uid');
        $mobile = I('post.mobile');
        $usr = M('user');
        $res = $usr->field('user_id')->where("mobile=$mobile")->find();
        if ($res['user_id']==''){
            $this->json_rest(0);
            die();
        }
        $ins['user_id'] = $res['user_id'];
        $thr = M('third');
        $ins = $thr->add($ins);
        1==$ins?$this->json_rest(0):$this->json_rest(1);
    }
    function mic_union(){
        $user_id = I('post.user_id');
        $token = I('post.token');
        $this->token_audit($user_id,$token);
        $arr = array('user_id'=>'','nickname'=>'','user_img'=>'');
        $ser['room_id'] = I('post.room');
        $rom = M('room');
        $res = $rom->field('user_id')->where($ser)->find();
        if($res['user_id']==$user_id){
            $this->json_rest(23,$arr);
            die();
        }elseif($res['user_id']){
            $data = $this->getliveinfo($res['user_id']);
            $data = json_decode($data,true);
            if ($data||$data['isRtcing']==0){
                $res['user_img'] = $data['user_img'];
                $res['nickname'] = $data['nickname'];
                $this->json_rest(1,$res);
            }elseif ($data['isRtcing']==1){
                $this->json_rest(27,$arr);
                die();
            }else{
                $this->json_rest(21,$arr);
            }
        }else{
            $this->json_rest(22,$arr);
            die();
        }
    }
    function info(){
        echo 1;
    }
}

