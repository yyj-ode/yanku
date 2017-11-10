<?php
/**
 * Created by PhpStorm.
 * User: beckh
 * Date: 2017/5/4 0004
 * Time: 16:03
 */



namespace Version2\Model;

use Think\Model;

class UserModel extends Model
{
    // 开启批量验证
    protected $patchValidate = true;
    // 验证
    protected $_validate = [
        // 自己补充验证规则
    ];

    // 填充
     protected $_auto = [
        // 自己补充填充规则
        ['salt', 'mkSalt', self::MODEL_BOTH, 'callback'],
        ['password', 'mkPassword', self::MODEL_BOTH, 'callback'],
        // 仅仅需要在插入维护
        ['created_at', 'time', self::MODEL_INSERT, 'function'],
        // 更新时间, 插入和更新时 都需要更新
        ['updated_at', 'time', self::MODEL_BOTH, 'function'],
    ];

    // 生产盐值
    public function mkSalt($value=null)
    {
        // 生产一段5个长度的随机字符串
        $salt = substr(md5(time()), 0, 5);
        $this->salt = $salt;// 记录下来
        return $salt;
    }
    // 生产密码
    public function mkPassword($value)
    {
        // 盐值+密码 sha1 混淆
        return sha1($this->salt . $value);
    }
    function _call($function_name,$arguments)
    {
       return $this->$function_name($arguments);
    }
}