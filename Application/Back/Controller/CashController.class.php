<?php
namespace Back\Controller;
use Think\Controller;
use Think\Page;
class CashController extends Controller {

    //充值
    public function rechargerecord(){
        $model=M('recharge');
        //总金额
        $totm = $model
            ->field('sum(amount) as tt')
            ->where('yk_recharge.pay_type = 0')
            ->select();

        $this->assign('tot',$totm);

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
            ->field('yk_user.nickname,yk_recharge.id,yk_recharge.user_id,yk_recharge.amount,(yk_recharge.amount*66) as tot,yk_recharge.order_number,yk_recharge.pay_status,yk_recharge.create_time')
            ->join('yk_user on yk_recharge.user_id=yk_user.user_id')
            ->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('rechar',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //提现
    public function showrecords(){
        $model=M('recharge');
        //总
        $all = $model
            ->field('sum(amount) as tot')
            ->where('pay_type=1')
            ->select();
        //成功
        $succ = $model
            ->field('sum(amount) as tot')
            ->where('pay_type=1 && pay_status=1')
            ->select();
        //待提
        $wait = $model
            ->field('sum(amount) as tot')
            ->where('pay_type=1 && pay_status=0')
            ->select();

        $this->assign('all',$all);
        $this->assign('succ',$succ);
        $this->assign('www',$wait);

        //条件
        $cond['yk_recharge.pay_type'] = 1;
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
            ->field('yk_user.nickname,yk_recharge.id,yk_recharge.user_id,yk_recharge.amount,(yk_recharge.amount*66) as tot,yk_recharge.order_number,yk_recharge.pay_status,yk_recharge.create_time')
            ->join('yk_user on yk_recharge.user_id=yk_user.user_id')
            ->where('yk_recharge.pay_type = 1')
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('tix',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //提现详情
    public function showrecordsXq(){
        $this->display();
    }

    //消费
    public function expensecalendar(){
        //select yk_deal.from_user_id,u1.nickname,yk_deal.to_user_id,u2.nickname,yk_deal.pay_time,yk_deal.number,yk_gift.price,yk_deal.pay_time,yk_gift.name from yk_deal left join yk_gift on yk_deal.gift_type=yk_gift.gift_type left join yk_user as u1 on yk_deal.from_user_id=u1.user_id left join yk_user as u2 on yk_deal.to_user_id=u2.user_id
        $model=M('deal');

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
            ->field('yk_deal.from_user_id,u1.nickname,yk_deal.to_user_id,u2.nickname,yk_deal.pay_time,yk_deal.number,yk_gift.price,yk_deal.pay_time,yk_gift.name')
            ->join('left join yk_gift on yk_deal.gift_type=yk_gift.gift_type left join yk_user as u1 on yk_deal.from_user_id=u1.user_id left join yk_user as u2 on yk_deal.to_user_id=u2.user_id')
            //->where($cond)
            ->limit($page->firstRow . ','.$limit)
            ->select();
        $this->assign('deal',$rows);

        $page->setConfig('theme','<div class="col-sm-6 text-left" style="width: auto"><ul class="pagination" style="float:left"> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </ul></div>');
        $page->setConfig('prev','<');
        $page->setConfig('next','>');
        $page->setConfig('first','|<');
        $page->setConfig('last','>|');
        $page_html = $page->show();
        $this->assign('page_html',$page_html);
        $this->display();
    }

    //充值规则
    public function topuprules(){
        $this->display();
    }

    //规则编辑
    public function theeditor(){
        $this->display();
    }
}