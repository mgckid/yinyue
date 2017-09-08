<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/22
 * Time: 14:21
 */

namespace maplesoft\base;


class Application
{
    public $config;
    public $request;
    public $message;
    public $info;
    public static $instance;

    private function __construct()
    {
        #载入初始化脚本
        require dirname(__DIR__) . '/init.php';
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
    public static function run($config)
    {
        #初始化应用配置
        self::setConfig($config);
        #打包http请求
        self::setRequest();
        $controllerName = 'app\\' . C('APP.DIR_CONTROLLER') . '\\' . self::getController() . C('APP.CONTROLLER_EXT');
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
     * 设置配置
     *
     * @access public
     * @author furong
     * @param $config
     * @return void
     * @since  2017年3月22日 16:30:24
     * @abstract
     */
    public static function setConfig($config)
    {
        self::getInstance()->config = new Config($config);
    }

    /**
     * 设置http请求
     *
     * @access public
     * @author furong
     * @return void
     * @since  2017年3月22日 16:46:59
     * @abstract
     */
    protected static function setRequest()
    {
        self::getInstance()->request = new Dispatch(
            array(
                'urlMode' => C('REQUEST.URL_MODE'),
                'defaultModule' => C('REQUEST.DEFAULT_MODULE'),
                'defaultController' => C('REQUEST.DEFAULT_CONTROLLER'),
                'defaultAction' => C('REQUEST.DEFAULT_ACTION'),
                'varModule' => C('REQUEST.VAR_MODULE'),
                'varController' => C('REQUEST.VAR_CONTROLLER'),
                'varAction' => C('REQUEST.VAR_ACTION'),
            )
        );
    }

    /**
     * 获取路由打包数据
     * @param type $name
     * @return type
     */
    public static function getRouter($name = NULL)
    {
        if (!isset(self::getInstance()->info['dispatch'])) {
            self::getInstance()->info['dispatch'] = self::getInstance()->request->run();
        }
        $return = self::getInstance()->info['dispatch'];
        if ($name) {
            $return = $return[$name];
        }
        return $return;
    }


    /**
     * 获取url路由请求控制器
     * @return mixed
     */
    public static function getController()
    {
        return self::getRouter('controller');
    }

    /**
     * 获取url路由请求方法
     * @return mixed
     */
    public static function getAction()
    {
        return self::getRouter('action');
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