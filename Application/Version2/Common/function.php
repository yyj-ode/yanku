<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/5/14 0014
 * Time: 14:53
 * 城市首字母排序
 */
//城市首字母排序
function getFirstCharter($data){
    $city = array();
    foreach ($data as $key=>&$v){
        if(empty($v['city_name'])){return '';}
        $str = $v['city_name'];
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) $v['initial'] = 'A';
        if($asc>=-20283&&$asc<=-19776) $v['initial'] = 'B';
        if($asc>=-19775&&$asc<=-19219) $v['initial'] = 'C';
        if($asc>=-19218&&$asc<=-18711) $v['initial'] = 'D';
        if($asc>=-18710&&$asc<=-18527) $v['initial'] = 'E';
        if($asc>=-18526&&$asc<=-18240) $v['initial'] = 'F';
        if($asc>=-18239&&$asc<=-17923) $v['initial'] = 'G';
        if($asc>=-17922&&$asc<=-17418) $v['initial'] = 'H';
        if($asc>=-17417&&$asc<=-16475) $v['initial'] = 'J';
        if($asc>=-16474&&$asc<=-16213) $v['initial'] = 'K';
        if($asc>=-16212&&$asc<=-15641) $v['initial'] = 'L';
        if($asc>=-15640&&$asc<=-15166) $v['initial'] = 'M';
        if($asc>=-15165&&$asc<=-14923) $v['initial'] = 'N';
        if($asc>=-14922&&$asc<=-14915) $v['initial'] = 'O';
        if($asc>=-14914&&$asc<=-14631) $v['initial'] = 'P';
        if($asc>=-14630&&$asc<=-14150) $v['initial'] = 'Q';
        if($asc>=-14149&&$asc<=-14091) $v['initial'] = 'R';
        if($asc>=-14090&&$asc<=-13319) $v['initial'] = 'S';
        if($asc>=-13318&&$asc<=-12839) $v['initial'] = 'T';
        if($asc>=-12838&&$asc<=-12557) $v['initial'] = 'W';
        if($asc>=-12556&&$asc<=-11848) $v['initial'] = 'X';
        if($asc>=-11847&&$asc<=-11056) $v['initial'] = 'Y';
        if($asc>=-11055&&$asc<=-10247) $v['initial'] = 'Z';
    }

    return $data;
}
function exp_rule($day){
    $data = array();
    $rule = file_get_contents('rule.json');
    $rule = json_decode($rule,true);
    foreach ($rule as $k=>$v){
        if ($day>=$v['s_day']&&$day<=$v['e_day']){
            $data['kubi'] = $v['kubi'];
            $data['experience'] = $v['exp'];
            break;
        }
    }
    if ($day>=7){
        $data['experience'] += 10;
    }else{
        if(1==$day){
            $data['experience'] += 1;
        }elseif (2==$day||3==$day){
            $data['experience'] += 2;
        }elseif (4==$day||5==$day||6==$day){
            $data['experience'] += 5;
        }
    }
//    $k = rand(168,300);
//    $data['kubi']=$data['kubi']+$k;
    return $data;
}
//文件上传
function upload_file($savepath){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = 3145728 ;// 设置附件上传大小
    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath = "./Upload/"; // 设置附件上传根目录
    $upload->savePath = $savepath.'/'; // 设置附件上传（子）目录
    // $upload->autoSub = false;
    // 上传文件
//    $info = $upload->upload();
    $info = $upload->uploadOne($_FILES['image']);
    if(!$info) {// 上传错误提示错误信息
        return $upload->getError();
//        return $err['err'] = 'Err';
    }else{// 上传成功
//        foreach($info as $file){
//            $data[$file['name']] =  $file['savepath'].$file['savename'];
//        }
        $data = $info['savepath'].$info['savename'];
        return $data;
    }
}


//短信验证
function formatTime(){
    return date('mdHis');
}
function getMillisecond() {
    list($s1, $s2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}
function code($mobile){
    $code = rand(1000,9999);
    $redis = new \Redis();
    $redis->connect("localhost", 6379); //localhost也可以填你服务器的ip
    $redis->select(5);
    $redis->set($mobile,$code);
    $redis->expire($mobile,600); //EXPIREAT key 1377257300
    return $code;
}
function send_sms($username,$pwd,$mobile){
    $post_data = array();
    $post_data['UserName'] = $username;
    $timestamp = formatTime();//时间戳
    $code = code($mobile);
    $post_data['Key'] = md5($username.$pwd.$timestamp);
    $post_data['Timestemp'] = formatTime();
    $post_data['Mobiles'] = $mobile;
    $post_data['Content'] = urlencode("【演库科技】您的验证码是$code"."。演绎未来的通路");
    $post_data['CharSet'] = "utf-8";
    $post_data['SchTime'] = "";
    $post_data['Priority'] = "5";
    $post_data['PackID'] = "";
    $post_data['PacksID'] = "";
    $post_data['ExpandNumber'] = "";
    $post_data['SMSID'] = getMillisecond();//long型数据，此处案例使用了当前的毫秒值，也可根据实际情况进行处理
    $url='http://www.youxinyun.com:3070/Platform_Http_Service/servlet/SendSms';
    $o="";
    foreach ($post_data as $k=>$v)
    {
        $o.= "$k=".$v."&";
    }
    $post_data=substr($o,0,-1);
    $this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$this_header);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);//返回相应的标识，具体请参考我方提供的短信API文档
    curl_close($ch);
    return $result;
}
/*
 * 重要！！！！！！！！！！！！！！！！！1积分规则！！！
 * 请勿修改
 */
 function rule(){
    $array =  array(
        array("s_day"=>1,"e_day"=>6,"kubi"=>10,"exp"=>0),
        array("s_day"=>7,"e_day"=>13,"kubi"=>30,"exp"=>0),
        array("s_day"=>14,"e_day"=>29,"kubi"=>50,"exp"=>0),
        array("s_day"=>30,"e_day"=>30,"kubi"=>333,"exp"=>188),
        array("s_day"=>31,"e_day"=>51,"kubi"=>50,"exp"=>0),
        array("s_day"=>52,"e_day"=>52,"kubi"=>520,"exp"=>388),
        array("s_day"=>53,"e_day"=>65,"kubi"=>50,"exp"=>0),
        array("s_day"=>66,"e_day"=>66,"kubi"=>666,"exp"=>520),
        array("s_day"=>67,"e_day"=>87,"kubi"=>50,"exp"=>0),
        array("s_day"=>88,"e_day"=>88,"kubi"=>888,"exp"=>666),
        array("s_day"=>89,"e_day"=>131,"kubi"=>50,"exp"=>0),
        array("s_day"=>132,"e_day"=>132,"kubi"=>1320,"exp"=>888),
        array("s_day"=>133,"e_day"=>187,"kubi"=>50,"exp"=>0),
        array("s_day"=>188,"e_day"=>188,"kubi"=>1888,"exp"=>1314),
        array("s_day"=>189,"e_day"=>365,"kubi"=>50,"exp"=>0),
        array("s_day"=>366,"e_day"=>366,"kubi"=>3888,"exp"=>1888),
    );
    $array = json_encode($array,JSON_UNESCAPED_UNICODE);
    $myfile = fopen("rule.json", "w");
    fwrite($myfile, $array);
    fclose($myfile);
}
function array_to_object($arr) {
    if (gettype($arr) != 'array') {
        return $arr;
    }
    foreach ($arr as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object') {
            $arr[$k] = (object)array_to_object($v);
        }
    }
    return (object)$arr;
}
