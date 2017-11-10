<?php
/**
 * 功能 : 把数组转化成3级树的形式，主要用于生成管理页面左侧的导航栏，或网站的菜单栏
 * @param $array
 * 需要传入的数组
 * @param $parent_id_name
 * 父级id的字段名
 * @param $child_id_name
 * 子级id的字段名
 * @param $level_name
 * 层级名：层级最小为1，不是0
 * @return $result
 * 返回一个树形的数组
 */
function array_to_tree3($array,$parent_id_name,$child_id_name,$level_name){
    $result = [];
    $level_max = array_column($array,$level_name);
    $max = max($level_max);
//    echo '<pre>';print_r($max);die;
    foreach($array as $k => $v){
        if($v[$level_name] == 1){
            array_push($result,$v);
            unset($array[$k]);
        }
    }

    foreach($array as $k1 => $v1){
        foreach($result as $k2 => $v2){
            if($v1[$parent_id_name] == $v2[$child_id_name]){
                $result[$k2]['children'][] = $v1;
                unset($array[$k1]);
            }
        }
    }

    foreach($array as $k1 => $v1){
        foreach($result as $k2 => $v2){
            if(isset($result[$k2]['children'])){
                foreach($result[$k2]['children'] as $k3 => $v3) {
                    if ($v1[$parent_id_name] == $v3[$child_id_name]) {
                        $result[$k2]['children'][$k3]['children'][] = $v1;
                        unset($array[$k1]);
                    }
                }
            }
        }
    }
    return $result;
}

//====================================以下为 array_to_tree3 的例子=========================================
//$arr1 = [
//    ['catalog_id' => 11, 'catalog_name' => '本站用户', 'catalog_parent_id' => 2, 'catalog_level' => 2, 'catalog_view' => 'user',],
//    ['catalog_id' => 12, 'catalog_name' => '身份认证', 'catalog_parent_id' => 2, 'catalog_level' => 2, 'catalog_view' => 'identity',],
//    ['catalog_id' => 13, 'catalog_name' => '演绎认证', 'catalog_parent_id' => 2, 'catalog_level' => 2, 'catalog_view' => 'deductive',],
//    ['catalog_id' => 25, 'catalog_name' => '通告统计', 'catalog_parent_id' => 7, 'catalog_level' => 2, 'catalog_view' => 'statistics',],
//    ['catalog_id' => 26, 'catalog_name' => '通告统计的儿子', 'catalog_parent_id' => 25, 'catalog_level' => 3, 'catalog_view' => 'xxxxxx',],
//    ['catalog_id' => 2, 'catalog_name' => '用户管理', 'catalog_parent_id' => 0, 'catalog_level' => 1, 'catalog_view' => '',],
//    ['catalog_id' => 1, 'catalog_name' => '后台首页', 'catalog_parent_id' => 1, 'catalog_level' => 1, 'catalog_view' => 'home',],
//    ['catalog_id' => 7, 'catalog_name' => '通告管理', 'catalog_parent_id' => 0, 'catalog_level' => 1, 'catalog_view' => '',]
//];
//echo '<pre>';print_r($arr1);
//$parent_id_name = 'catalog_parent_id';
//$child_id_name = 'catalog_id';
//$level_name = 'catalog_level';
//$arr2 = array_to_tree3($arr1,$parent_id_name,$child_id_name,$level_name);
//echo '<br>';echo '<pre>';print_r($arr2);echo '<br>';
//==================================以上为 array_to_tree3 的例子=========================================


/**
 * 功能:打印数组
 * @param $array
 * @return bool
 */
function dumpp($array){
    if(is_array($array)){
        echo '<pre>';
        print_r($array);
        echo '</pre>';die;
    }else{
        die;
    }
}

//基于tp3.2的文件上传



function miximg($char,$pic){
    $imgTest = imagecreatetruecolor(750,400);
    $imgReal = imagecreatefromjpeg("/usr/local/apache/htdocs/yanku5/material/big/$pic.png");
    $width = imagesx ($imgTest);
    $height = imagesy ($imgTest);
    if (8>=mb_strlen($char)){
        $fontSize = 54;
        $textWidth = $fontSize * mb_strlen ($char)*4/3;
        $textHeight = $fontSize*4/3;
        $x = round(($width-$textWidth)/2);//璁＄.?..?.按骞充?缃
        $y = round(($height+$textHeight)/2);//璁＄.?..?..?翠?缃
        $arr = imgwrite($imgTest,$fontSize,$x,$y,$char);
        $x = round(($arr[0]+(750-$arr[2]))/2);
        imgwrite($imgReal,$fontSize,$x,$y,$char);
    }else{
        $char1 = mb_substr($char,-(mb_strlen ($char)-8));
        $char = mb_substr($char,0,8);
        $fontSize = 36;
        $textWidth = $fontSize * mb_strlen ($char)*4/3;
        $textHeight = $fontSize*4/3;
        $x = round(($width-$textWidth)/2);//璁＄.?..?.按骞充?缃
        $y = round(($height-$textHeight)/2-18);//璁＄.?..?..?翠?缃
        $arr = imgwrite($imgTest,$fontSize,$x,$y,$char);
        $x = round(($arr[0]+(750-$arr[2]))/2);
        imgwrite($imgReal,$fontSize,$x,$y,$char);
        $textWidth = $fontSize * mb_strlen ($char1)*4/3;
        $x = round(($width-$textWidth)/2);//璁＄.?..?.按骞充?缃
        $y = round(($height+$textHeight)/2+18);//璁＄.?..?..?翠?缃
        $arr = imgwrite($imgTest,$fontSize,$x,$y,$char1);
        $x = round(($arr[0]+(750-$arr[2]))/2);
        imgwrite($imgReal,$fontSize,$x,$y,$char1);
    }
    $ran = substr(md5(time()),0,15).rand(0,99);
    $save_path = "/upload/Schedule/$ran.jpeg";
    $file = "Schedule/$ran.jpeg";
    imagejpeg ($imgReal,$save_path);
    imagedestroy($imgTest);
    imagedestroy($imgReal);
    return $file;
}
function imgwrite($img,$fontSize,$x,$y,$char){
    $white = imagecolorallocate($img, 255, 255, 255);
    $shadow = imagecolorclosestalpha($img,0,0,0,40);
    $shadow1 = imagecolorclosestalpha($img,0,0,0,100);
    imagefttext($img, $fontSize, 0, $x+2, $y+2, $shadow, "/usr/local/apache/htdocs/yanku5/material/font.otf", $char);
    imagefttext($img, $fontSize, 0, $x+4, $y+4, $shadow1, "/usr/local/apache/htdocs/yanku5/material/font.otf", $char);
    $arr = imagefttext($img, $fontSize, 0, $x, $y, $white, "/usr/local/apache/htdocs/yanku5/material/font.otf", $char);
    return $arr;
}