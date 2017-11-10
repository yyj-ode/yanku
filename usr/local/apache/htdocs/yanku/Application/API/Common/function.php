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