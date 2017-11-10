<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class GiftController extends Controller {
    //礼物列表
    public function giftlist(){
        //$model = M('Gift');
        $model = M('Gift')
            ->field('gift_type,batch,name,price')
            ->select();
        $this->assign('rrr',$model);

        $model = M('Gift');
        //条件
        $cond = [];
        //搜索
        $tit = I('get.title','');
        if($tit !==''){
            $cond['name'] = ['like','%'.$tit.'%'];
        }

        //分页部分
        $limit = 5;
        $total = $model->where($cond)->count();
        $page = new Page($total,$limit);
        $rows = $model
            ->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('rrr',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left"><ul class="pagination"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div><div class="col-sm-6 text-right">%HEADER%</div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
       $this->display();
    }

    //编辑礼物列表
    public function dogiftlist(){
        $gift_id = I('request.gift_type', '');
        //var_dump($gift_id);die();
        $model =M('gift');
        $info = $model
            ->field('gift_type,price,batch,name')
            ->where("gift_type=$gift_id")
            ->select();
        //var_dump($info);die();
        $this->assign('gift',$info);
        $this->display();
    }

    //删除
    public function del(){
        $this->show('ok');
    }

    //添加礼物
    public function addgift(){
        $this->display();
    }

    //添加
    public function add(){
        $giftid = I('post.id');
        $data['gift_type'] = I('post.id');
        $data['type'] = I('post.type');
        $data['price'] = I('post.kubi');
        $data['batch'] = I('post.batch');
        $data['name'] = I('post.name');
        //var_dump($data);die();

        $model = M('gift');
        $info = $model->add($data);
        $this->success('添加成功！','giftlist',1);
    }

    //修改
    public function update(){
        $giftid = I('post.gifttype');
        //var_dump($giftid);die();
        $data['batch'] = I('post.type');
        $data['name'] = I('post.name');
        $data['price'] = I('post.kubi');
        $model = M('gift');

        $model->where("gift_type=$giftid")->save($data);
        $this->success('修改成功！','giftlist',2);
    }
}