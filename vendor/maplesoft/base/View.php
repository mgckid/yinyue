<?php

/**
 * Description of View 视图类
 * 2016年4月18日 23:19:56
 * @author Administrator
 */

namespace maplesoft\base;

use League\Plates\Engine;

class View
{

    protected static $instance;
    protected $engineObj;
    protected $viewDir;
    protected $data = [];

    private function __construct()
    {

    }

    /**
     * 获取自身实例
     * @return type
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            self::$instance->engineObj = new Engine();
        }
        return self::$instance;
    }

    /**
     * 获取模版引擎实例化对象
     * @return type
     */
    static function getEngine()
    {
        return self::getInstance()->engineObj;
    }


    /**
     * 输出页面
     * @param type $view
     * @param array $data
     */
    public static function display($view, $data = array())
    {
        die(self::render($view, $data));
    }

    /**
     * 渲染页面
     * @param type $view 模版
     * @param array $data 模版变量
     * @return type
     */
    public static function render($view, array $data = array())
    {
        if (!self::getEngine()->exists($view)) {
           throw new \exception('模版不存在');
        }
        $data = array_merge(self::getInstance()->data, $data);
        return self::getEngine()->render($view, $data);
    }


    /**
     * 设置模版根目录
     * @param $viewDir
     */
    public static function setViewDir($viewDir)
    {
        self::getEngine()->setDirectory($viewDir);
    }

    /**
     * 模版赋值
     * @param $key
     * @param $value
     */
    public static function addData($data, $template = null)
    {
        self::getEngine()->addData($data, $template);
    }

}
