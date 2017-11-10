<?php
/**
 * Created by PhpStorm.
 * User: wangfuruo
 * Date: 2017/8/27
 * Time: 下午3:41
 */
namespace app\version1\controller;
use think\Db;
use think\Request;

class User extends Basic {
    function userSchedule(){
        switch ($this->method){
            case 'get': // get请求处理代码
                break;
            case 'put': // put请求处理代码
                $user_id = Request::instance()->put('user_id');
                $token = Request::instance()->put('token');
//                self::tokenAudit($user_id,$token);
                $schedule_id = Request::instance()->put('id');
                $up['status'] = Request::instance()->put('status');
                $res = Db::table('yk_interview')
                    ->where("to_user_id=$user_id AND schedule_id=$schedule_id")
                    ->update($up);
                $up1['schedule_status'] = $up['status'];
                $res1 = Db::table('yk_user_schedule')
                    ->where("user_id=$user_id AND schedule_id=$schedule_id")
                    ->update($up1);
                if ($res&&$res1){
                    return Json(self::status(1));
                }else{
                    return Json(self::status(0));
                }
                break;
                break;
            case 'post': // post请求处理代码
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
            default:
                return Json(self::status(0));
                break;
        }
    }
}