<?php
namespace Back\Controller;
use Think\Controller;
class IndexController extends Controller{
    public function index(){
        //session_start();
        //echo "欢迎".$_SESSION['name'];
        //var_dump($_SESSION);die();
        $tot = M('User')->field('count(user_id) as tot')->select();
        $audit = M('nameaudit')->field('count(user_id) as aud_all')->where('status = 1')->select();
        $schedule = M('schedule')->field('count(schedule_id) as sch_all')->select();
        $live = M('yanyiaudit')->field('count(user_id) as live')->where('status=2')->select();
        $user_sch = M('user_schedule')->field('count(user_id) as user_sch')->where('schedule_status=0')->find();
        $usersch = M('user_schedule')->field('count(user_id) as usersch')->where('schedule_status=3')->find();
        $wlive = M('yanyiaudit')->field('count(user_id) as wlive')->where('status=1')->select();
        $fuy = M('user_schedule')->field('COUNT(schedule_id) as toto')->where('schedule_status = 2')->select();
        $totm = M('recharge')->field('sum(amount) as tt')->where('pay_type = 0')->select();
        $tix = M('recharge')->field('sum(amount) as tot')->where('pay_type=1')->select();
        $succ = M('recharge')->field('sum(amount) as tot')->where('pay_type=1 && pay_status=1')->select();
        $wtx = M('recharge')->field('sum(amount) as tot')->where('pay_type=1 && pay_status=0')->select();

        $hotlive = M('user')
            ->field('yk_user.user_img,yk_user.nickname,yk_attention.attu_id,count(yk_attention.user_id) as tt')
            ->join('yk_attention on yk_user.user_id = yk_attention.user_id')
            ->group('yk_attention.attu_id')
            ->order('tt desc')
            ->limit(0,4)
            ->select();

        $hotbm=M('schedule')
            ->field('yk_schedule.schedule_title,yk_schedule.schedule_id,count(yk_user_schedule.schedule_id) AS tt')
            ->join('yk_user_schedule on yk_user_schedule.schedule_id = yk_schedule.schedule_id')
            ->group('yk_user_schedule.schedule_id')
            ->order('tt desc')
            ->limit(0,4)
            ->select();

        $send=M('user')
            ->field('yk_user.user_id,yk_user.nickname,count(yk_schedule.user_id) AS tt')
            ->join('yk_schedule on yk_user.user_id = yk_schedule.user_id')
            ->group('yk_schedule.user_id')
            ->order('tt desc')
            ->limit(0,4)
            ->select();

        $this->assign('total',$tot);
        $this->assign('audit',$audit);
        $this->assign('sche',$schedule);
        $this->assign('live',$live);
        $this->assign('user_sch',$user_sch);
        $this->assign('usersch',$usersch);
        $this->assign('wlive',$wlive);
        $this->assign('hotlive',$hotlive);
        $this->assign('hotbm',$hotbm);
        $this->assign('send',$send);
        $this->assign('fuy', $fuy);
        $this->assign('cz', $totm);
        $this->assign('tx', $tix);
        $this->assign('ktx', $succ);
        $this->assign('wtx', $wtx);
        $this->display();
    }

    //登录
    public function login(){
      if(IS_POST){
          $username = I('post.username');
          $password = I('post.password');
          $model_admin = M('Admin');
          //检索
          $cond['admin_name'] = $username;
          $admin = $model_admin->where($cond)->find();
          if($admin && md5($password) == $admin['password']){
              session_start();
              $_SESSION['name'] = $username;
              $this->redirect('index');
          }else{
              $this->error('用户名或密码错误','login',2);
              //$this->redirect('login');
          }
      }else{
          //展示登录表单
          $this->assign('message',session('message') ? session('message') : '');
          session('message',null);
          $this->display();
      }
    }


    public function logout(){
        session_start();
        //session_destroy(); //清空以创建的所有SESSION
        //session_unset("session_name");//清空指定的session
        unset($_SESSION["name"]);//清空指定的session
        //$this->success('退出成功!','login',2);
        $this->redirect('login');
    }


}
