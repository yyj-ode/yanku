<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class AdminController extends Controller {
        private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');
    //本站用户
    public function user(){
        $userdb = M('user');
        $all = $userdb
            ->field('yk_user.user_id,yk_user.realname,yk_user.nickname,yk_user.user_img,yk_user.kubi,IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum,yk_user.registertime,yk_user.lasttime,yk_user.lastip')
            ->join('yk_deal on yk_user.user_id=yk_deal.from_user_id left join yk_gift on yk_deal.gift_type=yk_gift.gift_type')
            ->group('yk_user.user_id')
            ->select();
        //select yk_user.user_id,yk_user.realname,yk_user.nickname,yk_user.user_img,yk_user.kubi,IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum,yk_user.registertime,yk_user.lasttime,yk_user.lastip from yk_user left join yk_deal on yk_user.user_id=yk_deal.from_user_id left join yk_gift on yk_deal.gift_type=yk_gift.gift_type GROUP BY yk_user.user_id
        //var_dump($all);die();
        $this->assign('rows',$all);

        $model = M('User');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['nickname'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 20;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('rows',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //实名认证
    public function identity(){
        $model = M('nameaudit');
//        $audi = $model
//            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_user.mobile,yk_user.IDcard_number,yk_user.user_positive,yk_nameaudit.status,yk_nameaudit.create_time,yk_nameaudit.console_time')
//            ->join('left join yk_user on yk_nameaudit.user_id=yk_user.user_id ')
//            ->where("yk_nameaudit.status = '1'")
//            ->select();
//        $this->assign('rowss',$audi);

        $model = M('nameaudit');
        //$model = M('Nameaudit');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['nickname'] = ['like','%'.$tit.'%'];
        }
//        $where['nickname'] = I('get.nickname');
        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
//        $rows = $model
//            ->where($cond)
//            ->limit($page->firstRow . ','.$limit)
//            ->select();
        $rows = $model
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_user.mobile,yk_user.IDcard_number,yk_user.user_positive,yk_nameaudit.status,yk_nameaudit.create_time,yk_nameaudit.console_time')
            ->join('left join yk_user on yk_nameaudit.user_id=yk_user.user_id ')
            ->order('yk_nameaudit.status')
            ->limit($page->firstRow . ','.$limit)
            ->select();
//        var_dump($rows);die();
        $this->assign('rowss',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //删除用户
    public function del(){
        $operate = 'delete';
        //判断是否传递selected
        $selected = I('post.ids',[]);
        //var_dump($selected);die();
        if(empty($selected)){
            //无主键值，不需要完成操作
            $operate = '';
        }
        //有主键值，判断当前操作类型
        switch ($operate) {
            case 'delete':
                //in条件删除
                M('User')->where(['user_id'=>['in',$selected]])->delete();
                break;
        }
        $this->redirect('user');
    }
    //身份认证编辑
    public function identityeditor(){
        $user_id = I('request.user_id','');
        //var_dump($user_id);die();
        $show =M('User')
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_user.mobile,yk_user.IDcard_number,yk_user.user_positive')
            ->where("user_id = $user_id")
            ->select();
        //var_dump($show);die();
        $this->assign('authshim',$show);
        $this->display();
    }

    //身份认证判断
    public function auditshenfen(){
        $user_id = I('request.user_id','');
        $time=time();
        $model = M('nameaudit');
        $user = M('user');
        $value =$_POST['inlineRadioOptions'];
        $content_i = $_POST['content'];

        switch ($value){
            case 1:
                $info = $user->query("update yk_user set nameaudit='0' WHERE user_id=$user_id");
                $info = $model->query("update yk_nameaudit set status='3',console_time=$time WHERE user_id=$user_id");
                //$info = $model->query("update yk_nameaudit set console_time=$time WHERE user_id=$user_id");
                $hx = new HXController($this->options);
                $content = '您的实名认证因为'.$content_i.'的原因认证失败，请重新进行实名认证';
                $hx = $hx->sendText('admin','users',array("$user_id"),$content);
                $this->success('实名认证失败...','identity',2);
                break;
            case 2:
                $info = $user->query("update yk_user set nameaudit='1' WHERE user_id=$user_id");
                $info = $model->query("update yk_nameaudit set status='1',console_time=$time WHERE user_id=$user_id");
                $this->success('实名认证审核中，尽快完成审核...','identity',2);
                break;
            case 3:
                $info = $user->query("update yk_user set nameaudit='2' WHERE user_id=$user_id");
                $info = $model->query("update yk_nameaudit set status='2',console_time=$time WHERE user_id=$user_id");
                $hx = new HXController($this->options);
                $content = '恭喜您已经通过实名认证，请尽快进行演绎认证！';
                $hx = $hx->sendText('admin','users',array($user_id),$content);
                $this->success('实名认证成功...','identity',2);
                break;
        }
    }

    //演绎认证
    public function deductive(){
//        $info = M('yanyiaudit')
//            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_yanyiaudit.create_time,yk_yanyiaudit.console_time,yk_yanyiaudit.status')
//            ->join('yk_user on yk_user.user_id = yk_yanyiaudit.user_id')
////            ->ORDER('yk_yanyiaudit.status desc')
////            ->limit(0,6)
//            ->where('yk_yanyiaudit.status=1')
//            ->select();
////        var_dump($info);die();
//        $this->assign('rows',$info);

        $model = M('yanyiaudit');
        //$model = M('Nameaudit');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['nickname'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 7;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_yanyiaudit.create_time,yk_yanyiaudit.console_time,yk_yanyiaudit.status')
            ->join('yk_user on yk_user.user_id = yk_yanyiaudit.user_id')
//            ->ORDER('yk_yanyiaudit.status desc')
//            ->limit(0,6)
            ->where('yk_yanyiaudit.status=1')
            ->select();
        $this->assign('rows',$rows);
//        var_dump($info);die();

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //演绎认证编辑
    public function dodeductive(){
        $user_id = I('request.user_id','');
//        $show1 =M('yanyi')
//            ->field('group_concat(DISTINCT yk_yanyi.yanyi_img) as yyimg,yk_user.user_id,yk_user.nickname,yk_user.realname')
//            ->join('yk_user on yk_user.user_id = yk_yanyi.user_id')
//            ->where("yk_yanyi.user_id = $user_id")
//            ->select();
//        foreach ($show as $k=>&$v){
//            $yanyiimg = explode(',',$v['yyimg']);
//            $v['yyimg'] = $yanyiimg;
//        }

        $show1 =M('yanyi')
            ->field('yanyi_img')
            ->where("user_id = $user_id")
            ->select();
        $show =M('user')
            ->field('user_id,nickname,realname')
            ->where("user_id = $user_id")
            ->select();
        //var_dump($show);die();


        $this->assign('yanyiaudit',$show);
        $this->assign('yanyimg',$show1);
        $this->display();
    }

    //创建直播间
    public function creatroom(){
        $user_id = I('request.user_id','');
        $model=D('room');
        $update = M('yanyiaudit');
        $push = 'rtmp://push.yankushidai.com/live/';
        $pull = 'http://pull.yankushidai.com/live/';
        $stream_id = substr(md5(time()),1,16);
        $stream = array(
            "pc_pull"=>"http://pullhls.yankushidai.com/live/$stream_id/index.m3u8", //PC端拉流地址
            "pc_push"=>"rtmp://pushr.yankushidai.com/live/$stream_id?vdoid=".time(), //PC端推流地址
            "mobile_push"=>"rtmp://pushr.yankushidai.com/live/$stream_id?vdoid=".time(), //MOBILE端拉流地址
            "mobile_pull"=>"http://pullhdl.yankushidai.com/live/$stream_id.flv", //MOBILE端推流地址
        );
        $rname = $update
            ->field('yk_user.user_id,yk_user.realname')
            ->join('yk_user on yk_yanyiaudit.user_id = yk_user.user_id')
            ->where("yk_user.user_id = $user_id")
            ->find();
        $rname=implode('',$rname);
        $data['user_id'] = $user_id;
        $data['room_id'] = substr(time(),-5);
        $time=time();
        $data['room_name'] = $rname.'的直播间';
        $data['push'] = $stream['mobile_push'];
        $data['pull'] = $stream['mobile_pull'];

        //var_dump($data);die();
        $ss =$_POST['inlineRadioOptions'];
        $content_i = $_POST['content'];
        $hx = new HXController($this->options);
        switch ($ss){
            case 1:
                $info =$update->query("update yk_yanyiaudit set status='3',console_time=$time WHERE user_id=$user_id");
                $content = '您的演绎认证因为'.$content_i.'的原因认证失败，请重新进行演绎认证';
                $hx = $hx->sendText('admin','users',array("$user_id"),$content);
                $this->success('用户审核失败..','deductive',3);
                break;
            case 2:
                $info =$update->query("update yk_yanyiaudit set status='1',console_time=$time WHERE user_id=$user_id");
                $this->success('演绎验证待完成，请及时完成审核...','deductive',3);
                break;
            case 3:
                //$if =$update->field('status')->where("user_id = $user_id")->find();
                $if1 =$model->field('room_name')->where("user_id = $user_id")->find();
                //var_dump($if);die();
                if(!empty($if1)){
                    echo "该用户已进行过演绎认证，勿重复操作！";
                    header("Refresh:3;url=deductive");
                    die();
                }
                $hx->setLiveroomsstream($stream);
                $ins = array("superadmin"=>$user_id);
                $result = $hx->setChatroomsuper($ins);
                $info = $model->add($data);
                $info1 =$update->query("update yk_yanyiaudit set status='2',console_time=$time WHERE user_id=$user_id");
                $content = '恭喜您已经通过演绎认证哦，已经可以进行直播或者报名参加海量通告了！';
                $hx = $hx->sendText('admin','users',array($user_id),$content);
                $this->success('审核通过，跳转中...','deductive',3);
                break;
        }
    }


}