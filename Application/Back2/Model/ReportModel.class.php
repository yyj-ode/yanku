<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1 0001
 * Time: 14:23
 */
namespace Back2\Model;

use Think\Model;

class ReportModel extends Model
{
    protected $tableName = 'yk_report';
    static public function getData($page, $rows,$view, $search_key){
        $start = $rows * ($page-1);
        $where = [
            "yk_user.user_id like '%{$search_key}%' or yk_user.realname like '%{$search_key}%' or yk_user.nickname like '%{$search_key}%'",
            "yk_report.view in ($view)",
        ];

        $data = M()->query("
            SELECT r.*,u1.nickname report_nickname,u2.nickname user_nickname,group_concat(ri.report_img) report_imgs 
            FROM yk_report r
            LEFT JOIN yk_user u1 ON r.report_id = u1.user_id
            LEFT JOIN yk_user u2 ON r.user_id = u2.user_id
            LEFT JOIN yk_report_img ri ON r.id = ri.id
            WHERE (r.view in ($view))
            AND (
                u2.user_id LIKE '%{$search_key}%' OR u2.realname LIKE '%{$search_key}%' OR u2.nickname LIKE '%{$search_key}%'
            )
            GROUP BY r.id
            ORDER BY r.id DESC 
            LIMIT $start,$rows
        ");
        $change_view = [
            0=>'未读',
            1=>'已读',
        ];
        $change_report_type = [
            0=>'通告',
            1=>'演库',
            2=>'直播',
        ];
        foreach($data as $k => $v){
            $data[$k]['view'] = $change_view[$v['view']];
            $data[$k]['report_type'] = $change_report_type[$v['report_type']];
        }
        $count = M('Report')
            ->field('yk_report.*,yk_user.nickname')
            ->join('yk_user ON yk_report.user_id = yk_user.user_id')
            ->where($where)
            ->count();
        $arr['list'] = $data;
        $arr['count'] = ceil($count/$rows);
        return $arr;

    }
}