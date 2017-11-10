<?php
/**
 * Created by PhpStorm.
 * User: wangfuruo
 * Date: 2017/8/17
 * Time: 下午5:48
 */
namespace app\version1\controller;
use app\version1\model\City;
use app\version1\model\Schedule;
use app\version1\model\User;
use app\version1\model\Boss as boss_model;
use app\version1\model\userSchedule;
use think\Db;
use think\Request;
use think\response\Json;

class Boss extends Basic {
    private $options = array('client_id'=>'YXA6ryLaYB6qEee9ag-MCEtXOA',
        'client_secret'=>'YXA6P275ejRYIcHpCHj_eqtonJUmJb4',
        'org_name'=>'1134170411178481',
        'app_name'=>'yanku');
    function bossAuth(){
        switch ($this->method){
            case 'get': // get请求处理代码
                $user_id = Request::instance()->get('user_id');
                $token = Request::instance()->get('token');
                self::tokenAudit($user_id,$token);
                $boss = new boss_model();
                $bossRes = $boss->field('organization,status')
                                ->where('user_id',$user_id)
                                ->find();
                if ($bossRes['organization']==''){
                    $user = new User();
                    $userRes = $user->field('yk_user.IDcard_number,yk_user.user_positive,yk_nameaudit.status')
                                    ->join('yk_nameaudit','yk_user.user_id=yk_nameaudit.user_id')
                                    ->where('yk_user.user_id',$user_id)
                                    ->find();
                    if (''==$userRes['IDcard_number']||3==$userRes['status']){
                        $date['bossAuth'] = 0;
                        return Json(self::status(1,$date));
                    }else{
                        $date['bossAuth'] = 4;
                        return Json(self::status(1,$date));
                    }
                }elseif($bossRes['organization']=!''){
                    $date['bossAuth'] = $bossRes['status'];
                    return Json(self::status(1,$date));
                }
                break;
            case 'put': // put请求处理代码
                break;
            case 'post': // post请求处理代码
                $user_id = Request::instance()->post('user_id');
                $token = Request::instance()->post('token');
                self::tokenAudit($user_id,$token);
                $type = Request::instance()->post('type');
                $where['user_id'] = $user_id;
                $user = new User();
                $ins['realname'] = Request::instance()->post('realname');
                if (0==$type){
                    $file = self::upload('Audit');
                    $ins['user_positive'] = self::uploadOss($file);
                    $userRes = $user->save($ins,$where);
                    if ($userRes){
                        return Json(self::status(1));
                    }else{
                        return Json(self::status(0));
                    }
                }elseif (1==$type){
                    $userRes = $user->save($ins,$where);
                    $boss = new boss_model();
                    $bossIns['organization'] = Request::instance()->post('organization');
                    $file = self::upload('bossAuth');
                    $bossIns['organizationImg'] = self::uploadOss($file);
                    $bossIns['user_id'] = $user_id;
                    $boss->data($bossIns);
                    $bossRes = $boss->save();
                    //坑1 上传重复实名可能会导致返回0
                    if ($userRes&&$bossRes){
                        return Json(self::status(1));
                    }else{
                        return Json(self::status(0));
                    }
                }else{
                    return Json(self::status(0));
                }
                break;
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
        }
    }
    function schedule(){
        switch ($this->method){
            case 'get': // get请求处理代码
                $user_id = Request::instance()->get('user_id');
                $token = Request::instance()->get('token');
                self::tokenAudit($user_id,$token);
                $start = Request::instance()->get('start',0);
                $schdule = new Schedule();
                0==$schdule?$all = $schdule->field('count(user_id)')->where("user_id=$user_id")->find():'';
                $result['all'] = $all['count(user_id)'];
                $result['schdule'] = $schdule->field('yk_schedule.schedule_id,yk_schedule.schedule_img,yk_schedule.valcode,yk_schedule.createtime,yk_schedule.status,count(yk_user_schedule.schedule_id) as new')
                                  ->join("yk_user_schedule","yk_user_schedule.schedule_id=yk_schedule.schedule_id","LEFT")
                                  ->where("yk_schedule.schedule_id>$start AND yk_user_schedule.schedule_status=0")
                                  ->group('yk_schedule.schedule_id')
                                  ->limit(10)
                                  ->select();
                return Json(self::status(1,$result));
                break;
            case 'put': // put请求处理代码
                $user_id = Request::instance()->put('user_id');
                $token = Request::instance()->put('token');
                self::tokenAudit($user_id,$token);
                $where['schedule_id'] = Request::instance()->put('id');
                $schdule = new Schedule();
                $update['status'] = 2;
                $res = $schdule->save($update,$where);
                if ($res){
                    return Json(self::status(1));
                }else{
                    return Json(self::status(0));
                }
                break;
            case 'post': // post请求处理代码
                $user_id = Request::instance()->post('user_id');
                $token = Request::instance()->post('token');
                self::tokenAudit($user_id,$token);
                $file = self::uploads('Schedule');
                $ins['schedule_title'] = Request::instance()->post('schedule_title');
                $ins['acttime'] = Request::instance()->post('acttime');
                $ins['address'] = Request::instance()->post('address');
                $ins['sex'] = Request::instance()->post('sex',0);
                $ins['audit'] = Request::instance()->post('audit',0);
                $ins['schedule_type'] = Request::instance()->post('schedule_type',0);
                $ins['user_id'] = $user_id;
                $ins['createtime'] = time();
                $ins['valcode'] = rand(1000,9999);
                ''==Request::instance()->post('schedule_content')?'':$ins['schedule_content']=Request::instance()->post('schedule_content');
                $city = Request::instance()->post('city');
                $citymodel = new City();
                $res = $citymodel->where('city_name',$city)->find();
                if (''!=$res){
                    $ins['city'] = $res['city_id'];
                }else{
                    $insert['city_name'] = $city;
                    $citymodel->save($insert);
                    $ins['city'] = $citymodel->city_id;
                }
                foreach ($file as $k=>$v){
                    if ('scheduleimg'==$v['name']){
                        $ins['schedule_img'] = self::uploadOss($v);
                    }else{
                        $ins['schedule_content'] = '<URL>img.yankushidai.com/Schedule/'.self::uploadOss($v);
                    }
                }
                $schdule = new Schedule();
                $id = $schdule->save($ins);
                if ($id){
                    return Json(self::status(1));
                }else{
                    return Json(self::status(0));
                }
                break;
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
            default:
                return Json(self::status(0));
                break;
        }
    }
    function scheduleEnroll(){
        switch ($this->method){
            case 'get': // get请求处理代码
                $user_id = Request::instance()->get('user_id');
                $token = Request::instance()->get('token');
                self::tokenAudit($user_id,$token);
                $type = Request::instance()->get('type');
                $start = Request::instance()->get('start',0);
                $schedule_id = Request::instance()->get('schedule_id');
//                $res = Db::table('yk_user_schedule')
                $usmodel = new userSchedule();
                $res = $usmodel->field('yk_user_schedule.user_id,yk_user_schedule.schedule_status,yk_user.nickname,yk_user.user_img,yk_user.height,yk_user.weight,yk_user.birthday')
                           ->join('yk_user','yk_user_schedule.user_id=yk_user.user_id')
                           ->where("yk_user_schedule.schedule_id=$schedule_id AND yk_user_schedule.schedule_status=$type AND yk_user_schedule.user_id>$start")
                           ->limit(10)
                           ->select();
                if ($res){
                    return Json(self::status(1,$res));
                }else{
                    return Json(self::status(0,$res));
                }
                break;
            case 'put': // put请求处理代码
                break;
            case 'post': // post请求处理代码
                $user_id = Request::instance()->post('user_id');
                $token = Request::instance()->post('token');
//                self::tokenAudit($user_id,$token);
                $ins['user_id'] = $user_id;
                $ins['to_user_id'] = Request::instance()->post('id');
                $ins['interviewtime'] = Request::instance()->post('interviewtime');
                $ins['address'] = Request::instance()->post('address');
                $ins['mark'] = Request::instance()->post('mark');
                $ins['schedule_id'] = Request::instance()->post('schedule_id');
                $res = Db::table('yk_interview')->insert($ins);
                $send = $ins;
                unset($send['user_id']);
                unset($send['to_user_id']);
                $send['msg_type'] = 1;
                $send['status'] = 0;
                if ($res){
                    $user = Db::table('yk_user')
                                 ->field('yk_user.user_img as iconURL,yk_user.nickname as name,yk_schedule.schedule_title,yk_schedule.schedule_id')
                                 ->join('yk_schedule','yk_user.user_id=yk_schedule.user_id','RIGHT')
                                 ->where("yk_schedule.schedule_id=$ins[schedule_id]")
                                 ->find();
                    $send = array_merge($send,$user);
                    $send = $this->img_url('iconURL',$send);
                    $hx = new Hx($this->options);
                    $result = $hx->sendText($user_id,'users',array($ins['to_user_id']),'',$send);
                    var_dump($result);
                    if ($result){
                        return Json(self::status(1));
                    }else{
                        return Json(self::status(0));
                    }
                }else{
                    return Json(self::status(0));
                }
                break;
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
            default:
                return Json(self::status(0));
                break;
        }
    }


    /**
     * @return Json
     * boss个人中心
     */
    function personalCenter(){
        switch ($this->method){
            case 'get': // get请求处理代码
                echo 1;die;
                break;
            case 'put': // put请求处理代码
                break;
            case 'post': // post请求处理代码
                break;
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
            default:
                return Json(self::status(0));
                break;
        }
    }

    function demo(){
        switch ($this->method){
            case 'get': // get请求处理代码
                break;
            case 'put': // put请求处理代码
                break;
            case 'post': // post请求处理代码
                break;
            case 'delete': // delete请求处理代码
                return Json(self::status(0));
                break;
            default:
                return Json(self::status(0));
                break;
        }
    }
}