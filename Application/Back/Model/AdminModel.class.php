<?php
namespace Back\Model;
use Think\Model;

class AdminModel extends Model{
	//开启批量验证
	protected $patchValidate = true;
	
	protected $_validate = [
	];

	//填充
	protected $_auto = [
		['tijiao','time',self::MODEL_INSERT,'function'],
		['chuli','time',self::MODEL_BOTH,'function'],
	];
}