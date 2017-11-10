<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/4/27 0027
 * Time: 13:42
 */

namespace API\Model;
use Think\Model\ViewModel;
class YankuModel extends ViewModel{
    public $viewFields = array(
        'user'=>array('user_id','nickname','nameaudit','_as'=>'u'),
        'attention'=>array('a.attu_id','_on'=>'a.attu_id=u.user_id','_as'=>'a'),
        'user_type'=>array('type','group_concat(distinct type)'=>'type_cont','_on'=>'a.attu_id=t.user_id','_type'=>'LEFT','_as'=>'t'),
    );
}
