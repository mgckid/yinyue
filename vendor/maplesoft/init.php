<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/22
 * Time: 16:13
 */
#开启会话
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('PRC');

#是否ajax请求
define('IS_AJAX', isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : FALSE);
#是否get请求
define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? true : false);
#是否post请求
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : FALSE);
#项目路径
define('__PROJECT__', dirname(dirname($_SERVER['DOCUMENT_ROOT'])));
#载入函数库
require 'function.php';

$applicationFuntionLibrary = '../../common/function/function.php';
if(file_exists($applicationFuntionLibrary)){
    require $applicationFuntionLibrary;
}

//注册类加载位置
require '../../vendor/auraphp/autoload/src/Loader.php';
$loader = new \Aura\Autoload\Loader();
$loader->register();
$loader->setPrefixes(require('../../vendor/classMap.php'));

#错误处理
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


