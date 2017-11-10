<?php

namespace Back2\Model;

use Think\Model;

class CatalogModel extends Model
{
    protected $tableName = 'yk_catalog';
    static public function getData($role){
        $role += 0;
        $data['list'] = M()->query("
            SELECT c1.* , GROUP_CONCAT(c2.catalog_name) children_names,GROUP_CONCAT(c2.catalog_view) children_views
            FROM (SELECT c1.*
            FROM yk_admin a
            LEFT JOIN yk_role_catalog rc ON a.role_id = rc.role_id
            LEFT JOIN yk_catalog c1 ON rc.catalog_id = c1.catalog_id
            WHERE a.role_id = $role
            GROUP BY c1.catalog_id) c1
            LEFT JOIN (
                SELECT c1.*
            FROM yk_admin a
            LEFT JOIN yk_role_catalog rc ON a.role_id = rc.role_id
            LEFT JOIN yk_catalog c1 ON rc.catalog_id = c1.catalog_id
            WHERE a.role_id = $role
            GROUP BY c1.catalog_id) c2 ON c1.catalog_id = c2.catalog_parent_id
            WHERE c1.catalog_level = 1
            GROUP BY c1.catalog_id
            ORDER BY c1.catalog_id
        ");
        foreach($data['list'] as $k => $v){
            if($v['children_names']){
                $data['list'][$k]['children_names'] = explode(',',$v['children_names']);
                $data['list'][$k]['children_views'] = explode(',',$v['children_views']);
                $count = count($data['list'][$k]['children_names']);
                for($i=0;$i<$count;$i++){
                    $data['list'][$k]['combine'][$i]['children_names'] =  $data['list'][$k]['children_names'][$i];
                    $data['list'][$k]['combine'][$i]['children_views'] =  $data['list'][$k]['children_views'][$i];
                }
            }else{
                $data['list'][$k]['children_names'] = [];
                $data['list'][$k]['children_views'] = [];
                $data['list'][$k]['combine'] = [];
            }
        }
        return $data;
    }


}