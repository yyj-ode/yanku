<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class ScheduleController extends Controller{
    //审核通告
    public function shenhe(){
        $model = M('schedule');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['schedule_title'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->field('yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.createtime,yk_schedule.acttime')
            //->where($cond)
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

    //审核详情
    public function shenhexq(){
        $this->show('审核详情');
    }

    //发布数量
    public function alerts()
    {
        $model = M('schedule')
            ->field('schedule_type,COUNT(schedule_id) as tot')
            //IFNULL(SUM(yk_gift.price*yk_deal.number),"0") as sum
            ->group('schedule_type')
            ->select();
        //var_dump($model);
        $this->assign('rows', $model);
        $this->display();
    }

    //发布详情
    public function Bjalerts(){
        $sch_type = I('request.schedule_type', '');

        $model = M('schedule');
        //条件
        $cond['yk_user_schedule.schedule_status'] = 0;
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['schedule_title'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->field('yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.createtime,yk_schedule.acttime,count(yk_user_schedule.user_id) as tot')
            //IFNULL(SUM(yk_gift.price*yk_deal.number),"0")
            ->join('yk_user_schedule on yk_schedule.schedule_id=yk_user_schedule.schedule_id')
            ->where("yk_schedule.schedule_type=$sch_type && yk_user_schedule.schedule_status = 0")
            ->group('yk_schedule.schedule_id')
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

    //报名
    public function registrationnotice()
    {
        //$model = ('user_schedule');
        $info = M('user_schedule')
            ->field('yk_schedule.schedule_type,COUNT(yk_user_schedule.schedule_id) as toto')
            ->join('LEFT JOIN yk_schedule on yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where('yk_user_schedule.schedule_status = 0')
            ->group('yk_schedule.schedule_type')
            ->select();
        //var_dump($info);die();
        $this->assign('reg', $info);
        $this->display();
    }
    //报名详情
    public function BjRegistrationnotice()
    {
        $sch_id = I('request.schedule_type', '');
        //var_dump($sch_id);die();
        $model = M('schedule');
        $info = $model
            ->field('yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.createtime,yk_schedule.acttime,count(yk_schedule.user_id) as tot')
            ->join('yk_user_schedule on yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where("yk_schedule.schedule_type=$sch_id && yk_user_schedule.schedule_status = 0")
            ->group('yk_schedule.schedule_id')
            ->select();
        //var_dump($info);die();
        $this->assign('bjbm',$info);

        $model = M('schedule');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['schedule_title'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('bjbm',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }
    //应约
    public function appointmentnotice()
    {
        $info = M('user_schedule')
            ->field('yk_schedule.schedule_type,COUNT(yk_user_schedule.schedule_id) as toto')
            ->join('LEFT JOIN yk_schedule on yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where('yk_user_schedule.schedule_status = 3')
            ->group('yk_schedule.schedule_type')
            ->select();
        //var_dump($info);die();
        $this->assign('reg', $info);
        $this->display();
    }

    //应约详情
    public function Bjappointmentnotice()
    {
        $sch_id = I('request.schedule_type', '');
        //var_dump($sch_id);die();
        $model = M('schedule');
        $info = $model
            ->field('yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.createtime,yk_schedule.acttime,count(yk_schedule.user_id) as tot')
            ->join('yk_user_schedule on yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where("yk_schedule.schedule_type=$sch_id && yk_user_schedule.schedule_status = 3")
            ->group('yk_schedule.schedule_id')
            ->select();
        $this->assign('bjyy',$info);
        $this->display();
    }

    //负约
    public function fynotice()
    {
        $info = M('user_schedule')
            ->field('yk_schedule.schedule_type,COUNT(yk_user_schedule.schedule_id) as toto')
            ->join('LEFT JOIN yk_schedule on yk_user_schedule.schedule_id=yk_schedule.schedule_id')
            ->where('yk_user_schedule.schedule_status = 2')
            ->group('yk_schedule.schedule_type')
            ->select();
        //var_dump($info);die();
        $this->assign('fuy', $info);
        $this->display();
    }

    //负约详情
    public function Bjfynotice()
    {
        $sch_id = I('request.schedule_type', '');
        $time = time();
        $model=M('schedule');
        $show = $model
            ->field('yk_schedule.schedule_id,yk_schedule.schedule_title,yk_schedule.createtime,yk_schedule.acttime,count(yk_schedule.user_id) as tot')
            ->join('yk_user_schedule on yk_schedule.schedule_id=yk_user_schedule.schedule_id')
            ->where("yk_user_schedule.schedule_status=2 && yk_schedule.acttime+604800 < $time && yk_schedule.schedule_type=$sch_id")
            ->group('yk_schedule.schedule_id')
            ->select();
        //var_dump($show);die();
        $this->assign('fuy',$show);
        $this->display();
    }

    //应约详情内容
    public function details()
    {
        $sch_id=I('request.schedule_id','');
        $model = M('user');
        $show=$model
            ->field('yk_user.user_id,yk_user.user_img,yk_user.nickname')
            ->join('yk_user_schedule on yk_user.user_id = yk_user_schedule.user_id')
            ->where("yk_user_schedule.schedule_id =$sch_id && yk_user_schedule.schedule_status = 3")
            ->select();
        //var_dump($show);die();
        $this->assign('rrr',$show);
        $this->display();
    }

    //报名详情内容
    public function details1()
    {
        $sch_id=I('request.schedule_id','');
        $model = M('user');
        $show=$model
            ->field('yk_user.user_id,yk_user.user_img,yk_user.nickname')
            ->join('yk_user_schedule on yk_user.user_id = yk_user_schedule.user_id')
            ->where("yk_user_schedule.schedule_id =$sch_id && yk_user_schedule.schedule_status = 0")
            ->select();
        //var_dump($show);die();
        $this->assign('rrr',$show);
        $this->display();
    }

    //负约详情内容
    public function details2()
    {
        $sch_id=I('request.schedule_id','');
        $time = time();
        $model = M('user');
        $show=$model
            ->field('yk_user.user_id,yk_user.user_img,yk_user.nickname')
            ->join('yk_user_schedule on yk_user.user_id = yk_user_schedule.user_id')
            ->where("yk_user_schedule.schedule_status=2 && yk_schedule.acttime+604800 < $time && yk_schedule.schedule_type=$sch_id")
            ->select();
        //var_dump($show);die();
        $this->assign('rrr',$show);
        $this->display();
    }

}