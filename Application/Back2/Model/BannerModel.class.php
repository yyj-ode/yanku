<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 12:53
 */
namespace Back2\Model;

use Think\Model;

class BannerModel extends Model
{
    protected $tableName = 'yk_banner';
    //列表类接口必须有查找和分页!!
    static public function getData($page,$rows,$search_key){
        $rows += 0;
        $start = ($page-1) * $rows;
        $data['list'] = M('Banner b')
            ->field('b.banner_id,s.schedule_title,s.schedule_img')
            ->join('yk_schedule s on b.banner_id = s.schedule_id','LEFT')
            ->where("b.banner_type = 0 and s.schedule_title like '%{$search_key}%'")
            ->limit($start,$rows)
            ->select();
//        echo '<pre>';var_dump($data);die;
        foreach($data['list'] as $k => $v){
            $data['list'][$k]['schedule_img'] = FatherModel::$img_host.$v['schedule_img'];
        }
        $count =  M('Banner b')
            ->field('b.banner_id,s.schedule_title,s.schedule_img')
            ->join('yk_schedule s on b.banner_id = s.schedule_id','LEFT')
            ->where("b.banner_type = 0 and s.schedule_title like '%{$search_key}%'")
            ->count();
        $data['count'] = ceil($count/$rows);
        return $data;
    }

    static public function getScheduleData($page,$rows,$search_key){
        $start = ($page - 1) * $rows;
        $data['list'] = M()->query("
            SELECT s.schedule_id,s.schedule_title,b.banner_id,s.schedule_img
            FROM yk_schedule s
            LEFT JOIN yk_banner b ON s.schedule_id = b.banner_id
            WHERE s.schedule_title LIKE '%{$search_key}%'
            HAVING b.banner_id IS NULL 
            ORDER BY s.schedule_id DESC 
            LIMIT $start,$rows
        ");
        foreach( $data['list'] as $k => $v){
            $data['list'][$k]['schedule_img'] = FatherModel::$img_host.$v['schedule_img'];
            unset( $data['list'][$k]['banner_id']);
        }
        $count = M()->query("
            SELECT s.schedule_id,s.schedule_title,b.banner_id,s.schedule_img
            FROM yk_schedule s
            LEFT JOIN yk_banner b ON s.schedule_id = b.banner_id
            WHERE s.schedule_title LIKE '%{$search_key}%'
            HAVING b.banner_id IS NULL 
        ");
        $data['count'] = ceil(count($count)/$rows);
        return $data;
    }

}