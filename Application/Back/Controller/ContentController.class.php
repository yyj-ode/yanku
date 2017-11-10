<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class ContentController extends Controller{
    //用户反馈
    public function feedback(){
        $model = M('User');
        //条件
        $cond = [];

        //分页部分
        $limit = 5;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);

        $rows = $model
            ->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('rows',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left"><ul class="pagination"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div><div class="col-sm-6 text-right">%HEADER%</div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');

        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //页面管理
    public  function page(){

    }
}