<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/5/24 0024
 * Time: 16:31
 */

namespace API\Controller;


use Think\Controller;

class PingController extends ResController
{
   public function shi(){
       require_once('vendor/autoload.php');
       \Pingpp\Pingpp::setApiKey('sk_live_Lezvn5SC848CSy9eP04KSuL8');
       echo \Pingpp\Charge::create(array(
           'order_no'  => '2017052507339469',
           'amount'    => '100',
           'app'       => array('id' => 'app_1Cy5WT8mfXf1arvf'),
           'channel'   => 'alipay',
           'currency'  => 'cny',
           'client_ip' => '182.48.105.76',
           'subject'   => '库币支付',
           'body'      => '库币支付啊啊啊啊啊啊啊'
       ));
   }
   public function pay(){
        $user_id = I('post.user_id');
//        $token = I('post.token');
//        $this->token_audit($user_id,$token);
       $amount = I('post.amount');
       $channel = I('post.channel');
        require_once('vendor/autoload.php');
        \Pingpp\Pingpp::setApiKey('sk_live_Lezvn5SC848CSy9eP04KSuL8');

        $client_ip = $_SERVER['REMOTE_ADDR'];
        $order_no = substr(md5(time()), 0, 12);
        $data = \Pingpp\Charge::create(array(
            'order_no'  => $order_no,
            'amount'    => $amount,
            'app'       => array('id' => 'app_1Cy5WT8mfXf1arvf'),
            'channel'   => $channel,
            'currency'  => 'cny',
            'client_ip' => $client_ip,
            'subject'   => '库币支付',
            'body'      => '库币支付啊啊啊啊啊啊啊'
        ));
        $rec = M('recharge');
        $insert['user_id'] = $user_id;
        $insert['order_number'] = $order_no;
        $insert['create_time'] = strtotime("now");
        $insert['pay_type'] = 0;
        $insert = $rec->data($insert)->add();
        if ($insert){
            $data = json_decode($data,true);
            $this->json_rest(1,$data);
        }
   }
   public function paysuccess(){
       $json = file_get_contents("php://input");
       $json = json_decode($json,true);
       if ($json['type']=='charge.succeeded'){
           $data = $json['data']['object'];
           if ($data['paid']==true){
               $kubi = $data['amount']/100*66;
               $order_number = $data['order_no'];
               $rec = M('recharge');
               $where['order_number'] = $order_number;
               $res = $rec->field('user_id,id')->where($where)->find();
               $user_id = $res['user_id'];
               $id = $res['id'];
               $User = M('user');
               $con['pay_status'] = 1;
               $User->startTrans();//开启事务
               $result = $User->where("user_id=$user_id")->setInc('kubi',$kubi);
               $result1 = $rec->where("id=$id")->save($con);
               if($result && $result1){// 根据条件更新记录
                   $User->commit();
                   $rec->commit();
               }else{
                   $User->rollback();
                   $rec->rollback();
               }
           }

       }
   }
    public function show(){
        $user_id = I('post.user_id');
        $amount = I('post.amount');
        $channel = I('post.channel');
        require_once('vendor/autoload.php');
        \Pingpp\Pingpp::setApiKey('sk_live_Lezvn5SC848CSy9eP04KSuL8');

        $client_ip = $_SERVER['REMOTE_ADDR'];
        $order_no = substr(md5(time()), 0, 12);
        $data = \Pingpp\Charge::create(array(
            'order_no'  => $order_no,
            'amount'    => $amount,
            'app'       => array('id' => 'app_1Cy5WT8mfXf1arvf'),
            'channel'   => $channel,
            'currency'  => 'cny',
            'client_ip' => $client_ip,
            'subject'   => '库币支付',
            'body'      => '库币支付啊啊啊啊啊啊啊'
        ));
        $rec = M('recharge');
        $insert['user_id'] = $user_id;
        $insert['order_number'] = $order_no;
        $insert['create_time'] = strtotime("now");
        $insert['pay_type'] = 0;
        $insert = $rec->data($insert)->add();
        if ($insert){
            $data = json_decode($data,true);
            $this->json_rest(1,$data);
        }
   $this->json_rest(1,$data);
   }
   public function applepay(){
       $user_id = I('post.user_id');
       $token = I('post.token');
       $this->token_audit($user_id,$token);
       $amount = I('post.amount');
       $amount *= 66;
       $rec = M('recharge');
       $User = M('user');
       $con['user_id'] = $user_id;
       $con['create_time'] = strtotime("now");
       $con['order_number'] = substr(md5(time()), 0, 12);
       $con['pay_status'] = 0;
       $con['pay_type'] = 0;
       $data['kubi'] = $amount;
       $User->startTrans();//开启事务
       $result = $User->where("user_id=$user_id")->setInc('kubi',$amount);
       $result1 = $rec->add($con);
       if($result && $result1){// 根据条件更新记录
           $User->commit();
           $rec->commit();
           $this->json_rest(1,$data);
       }else{
           $User->rollback();
           $rec->rollback();
           $this->json_rest(0);
       }
   }
}