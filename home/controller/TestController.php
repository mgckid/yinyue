<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/5
 * Time: 9:38
 */

namespace app\controller;


class TestController
{
    public function index(){
      $aaa=  'http://img.my.csdn.net/uploads/201107/14/0_1310654533q26t.png';
      $re = get_headers($aaa,1);
      var_dump($re);

    }
}