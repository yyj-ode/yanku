<?php
namespace Back\Controller;
use Think\Controller;
use Think\Page;
class LiveController extends Controller {

    //直播列表
    public function zblist(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        //$redis->config get databases;
        $key = $redis->keys('*');
        $kk = $redis->mget($key);
        foreach ($kk as $k=>&$v){
            $redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
            $v = json_decode($v,true);
        }
        //var_dump($kk[0]['room_id']);die();
        $this->assign('rows',$kk);
        $this->display();

    }

    //删除直播间
    public function dellist(){
        $this->display();
    }

    //直播监控
    public function video(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        $key = $redis->keys('*');
        $kk = $redis->mget($key);
        foreach ($kk as $k=>&$v){
            //$redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
            $v = json_decode($v,true);
        }
        //var_dump($kk[0]['room_id']);die();
        $this->assign('rows',$kk);
        $this->display();
    }

    //系统消息
    public function message(){
        $this->display();
    }

    //举报
    public function usersreport(){
        $model = M('report');

        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['report_id'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->field('yk_report.id,yk_report.report_id,yk_report.user_id,yk_report.reason,yk_report.explain')
            //->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('show',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //直播记录
    public function record(){
        $model = M('live_history');
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
            ->field('yk_live_history.user_id,yk_user.nickname,yk_live_history.starttime,yk_live_history.endtime,yk_live_history.kubi,yk_live_history.view,yk_live_history.praise')
            ->join('yk_user on yk_live_history.user_id = yk_user.user_id')
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('history',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }
}