<?php
namespace Back\Controller;
use Think\Page;
use Think\Controller;
class LevelController extends Controller {
    //积分等级
    public function integralgrade(){
        $this->display();
    }
    //礼物等级
    public function classgrade(){
        $this->display();
    }
}