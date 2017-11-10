<?php
namespace Back2\Controller;
header('Access-Control-Allow-Origin:*');

use Think\Controller;
use Back2\Model\BannerModel;

class PhpController extends Controller
{
    public function index(){
        phpinfo();
        $arr = [1,2,3,4,5];
        dumpp($arr);
    }
}