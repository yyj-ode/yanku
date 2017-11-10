<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class TestController extends Controller{
    public function test(){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(2);
        //$redis->config get databases;
        $key = $redis->keys('*');
        $kk = $redis->mget($key);
        foreach ($kk as $k=>$v){
            $redis->exists($v['room_id'])?$key[$k] = $v['room_id']:'';
        }
        $qq = json_decode($v,true);
        var_dump($qq);die();
        //echo $errorinfo = json_last_error();die();
    }

    public function page(){
        $model = M('yanyiaudit');
        //条件
        $cond['yk_yanyiaudit.status'] = 1;
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['nickname'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 6;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->field('yk_user.user_id,yk_user.nickname,yk_user.realname,yk_yanyiaudit.create_time,yk_yanyiaudit.console_time,yk_yanyiaudit.status')
            ->join('yk_user on yk_user.user_id = yk_yanyiaudit.user_id')
            ->where("yk_yanyiaudit.status = '1'")
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

    public function arr(){
        $a=array();
    }
}