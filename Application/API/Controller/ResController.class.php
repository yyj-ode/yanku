<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/5/5 0005
 * Time: 10:41
 */

namespace API\Controller;
use Think\Controller;

class ResController extends Controller{
    protected $count = array('result'=>'','message'=>'','version'=>1,'data'=>'');
    public function json_rest($code,$data){
        $this->count['result'] = $code;
        $this->count['message'] = $this->status($code);
        if ($data == '') {
            if (isset($this->count['data'])){

            }elseif ($data==''){
                $data = array();
            }else{
                unset($this->count['data']);
            }
        }else{
            $this->count['data'] = $data;
        }
        $this->count = json_encode($this->count,JSON_UNESCAPED_SLASHES);
        echo $this->count;
    }

    public function status($code){
        static $_status = array(
            0 => 'False',//失败
            1 => 'OK',//成功
            2 => 'Created',//创建成功
            3 => '不存在',//不存在
            4 => 'No Content', //无内容
            5 => 'Param Incomplete', //参数不全
            6 => '请重新登录', //token错误
            7 => 'Consume False', //消费失败
            8 => 'Balance Not Enough', //余额不足
            9 => '文件上传失败', //文件上传失败
            10 => '已存在', //已存在
            11 => 'Param False', //参数错误
            12 => '粉丝暂无贡献',
            13 => '无数据',
            14 => '交易失败',
            15 => '该主播已下播',
            16 => '开播失败',
            17 => '空',
            18 => '注册信息不全',
            19 => '未认证',
        );
        return $_status[$code];
    }
    public function token_audit($user_id,$token){
        $redis = new \Redis();
        $redis -> connect("localhost",6379); //localhost也可以填你服务器的ip
        $redis->select(1);
        if (empty($user_id)||empty($token)){
            $this->json_rest(6);
            die();
        }else{
            if ($token!=$redis->get($user_id)){
                $this->json_rest(6);
                die();
            }else{
                return 1;
            }
        }
    }

    public function img_urls($data,$filter,$url){
        foreach ($data as $key=>&$v) {
            $v[$filter] = 'http://' . $_SERVER ['HTTP_HOST'] . '/Upload/' . $v[$filter];
        }
        return $data;
    }
    public function img_url($data,$filter,$url){
        $data[$filter] = 'http://' . $_SERVER ['HTTP_HOST'] . '/Upload/'  . $data[$filter];
        return $data;
    }
    public function imgs_url($data,$filter,$url){
        foreach ($data as $key=>&$value) {
            foreach ($value[$filter] as $k=>&$v){
                $v = 'http://' . $_SERVER ['HTTP_HOST'] . '/Upload/'. $v;
            }
        }
        return $data;
    }
    public function attu_msg($code){
        static $_status = array(
            0 => '悄悄的告诉你有个小婊砸关注了你啦',
            1 => 'Ta偷偷的关注了你,快回来看看吧',
            2 => '又有一个人被你的美貌与才华吸引了，点我看看吧',
            3 => '嘿！快来看看又是谁被你的魅力折服'
        );
        return $_status[$code];
    }
    public function onlive_msg($code,$nickname){
        $_status = array(
            0 => "你关注的".$nickname."已经开播啦",
            1 => $nickname."开播啦啦啦啦",
            2 => $nickname."开播啦啦啦啦",
            3 => $nickname."开播啦啦啦啦"
        );
        return $_status[$code];
    }
}


