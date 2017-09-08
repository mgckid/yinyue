<?php
/**
 * 框架启动文件
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 14:44
 */
#设置页面字符编码
header("content-type:text/html; charset=utf-8");

#错误报告级别(默认全部)
error_reporting(E_ALL);

#时区设置
date_default_timezone_set('PRC');

#载入函数库
require __DIR__ . '/function.php';

/*框架常量设置 开始*/
#是否ajax请求
define('IS_AJAX', isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : FALSE);
#是否get请求
define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? true : false);
#是否post请求
define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : FALSE);
#项目路径
defined('__PROJECT__') or define('__PROJECT__', dirname(dirname($_SERVER['DOCUMENT_ROOT'])));
#框架组件路径
defined('__FRAMEWORK__') or define('__FRAMEWORK__', __DIR__);
#框架组件路径
defined('__VENDOR__') or define('__VENDOR__', dirname(__FRAMEWORK__));
#当前域名
defined('__HOST__') or define('__HOST__', $_SERVER['HTTP_HOST']);
/*框架常量设置 结束*/

#注册类
require __FRAMEWORK__ . '/base/Register.php';
$register = new \houduanniu\base\Register();


#注册自动加载类
require __VENDOR__ . '/auraphp/autoload/autoload.php';
$register->set('autoloader', new \Aura\Autoload\Loader());
$register->get('autoloader')->register();
$register->get('autoloader')->setPrefixes(require(__VENDOR__ . '/classMap.php'));

#注册错误处理
$register->set('errorRporter', new \Whoops\Run);
$register->get('errorRporter')->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$register->get('errorRporter')->register();

\houduanniu\base\Application::run($register);