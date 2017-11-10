<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3 0003
 * Time: 13:08
 */
namespace Back2\Model;

use Think\Model;

class FatherModel extends Model
{
    //上传服务器之前，注意把配置切换成线上服务器配置

    //测试服务器配置
    static public $img_host = 'http://img.yankushidai.com/';//图片上传地址(拼字符串用)
//    static public $html = 'http://39.108.104.233';//后台静态页面地址（回调用）

    //线上服务器配置
//    static public $img_host = 'http://rest.yankushidai.com/Upload/';//图片上传地址(拼字符串用)
    static public $html = 'http://www.yankushidai.com/back3';//后台静态页面地址（回调用）

}