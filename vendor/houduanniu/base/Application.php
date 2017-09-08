<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/5/26
 * Time: 16:07
 */

namespace houduanniu\base;


class Application
{
    public static $instance;
    public $register;
    public $route;
    public $message;
    public $info;

    private function __construct()
    {
    }

    /**
     * 定义应用程序常量
     *
     * @access  private
     * @author furong
     * @return void
     * @since
     * @abstract
     */
    private static function  defineAppCons()
    {

    }

    /**
     * 运行应用
     * @access public
     * @author furong
     * @param $config
     * @return void
     * @since  2017年3月22日 16:44:31
     * @abstract
     */
    public static function run($register)
    {
        self::getInstance()->register = $register;
        #打包http请求
        self::getInstance()->register->set('request', new Request((new Config(__PROJECT__ . '/common/config'))->all()));
        self::getInstance()->route = self::getInstance()->register->get('request')->run();

        #应用配置
        self::getInstance()->register->set('config', new Config([__PROJECT__ . '/' . strtolower(self::getModule()) . '/config', __PROJECT__ . '/common/config']));
        #应用加载
        $appPath = array(
            __PROJECT__ . '/' . strtolower(self::getModule()),
            __PROJECT__ . '/common',
        );
        self::getInstance()->register->get('autoloader')->addPrefix('app', $appPath);

        #载入自定义函数库
        foreach (C('LOAD_FILES') as $file) {
            require $file;
        }
        #开启session
        session_start();
        #运行程序
        $controllerName = 'app\\' . C('DIR_CONTROLLER') . '\\' . self::getController() . C('EXT_CONTROLLER');
        if (!class_exists($controllerName)) {
            throw new \exception('控制器不存在');
        } elseif (!method_exists($controllerName, self::getAction())) {
            throw new \exception('方法不存在');
        } else {
            #执行方法
            call_user_func(array(new $controllerName, self::getAction()));
        }
    }

    /**
     * 获取类实例化对象
     * @return type
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 获取url路由请求模块
     * @return mixed
     */
    public static function getModule()
    {
        return self::getInstance()->route['module'];
    }

    /**
     * 获取url路由请求控制器
     * @return mixed
     */
    public static function getController()
    {
        return self::getInstance()->route['controller'];
    }

    /**
     * 获取url路由请求方法
     * @return mixed
     */
    public static function getAction()
    {
        return self::getInstance()->route['action'];
    }

    /**
     * 获取路由打包数据
     * @param type $name
     * @return type
     */
    public static function getRouter($name = NULL)
    {
        $return = self::getInstance()->route;
        if (!empty($name)) {
            $return = $return[$name];
        }
        return $return;
    }


    public function setMessage($msg)
    {
        $this->message = $msg;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setInfo($name, $value = NULL)
    {
        $this->info[$name] = $value;
    }

    public function getInfo($name)
    {
        if (!isset($this->info[$name])) {
            return NULL;
        }
        return $this->info[$name];
    }
} 