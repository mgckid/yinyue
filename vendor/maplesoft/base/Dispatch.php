<?php

/**
 * Created by PhpStorm.
 * User: mgckid
 * Date: 2015/11/4 0004
 * Time: 下午 23:33
 */

namespace maplesoft\base;

class Dispatch
{

    private $request;
    private $config = array(
        'urlMode' => 1,
        'defaultModule' => 'Home',
        'defaultController' => 'Index',
        'defaultAction' => 'index',
        'varModule' => 'm',
        'varController' => 'c',
        'varAction' => 'a',
    );

    public function __construct($config = array())
    {
        $this->request = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO']) : $this->getRequestUri();
        if ($config) {
            $this->config($config);
        }
    }

    protected function getRequestUri()
    {
        $secriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $secriptName = str_replace('index.php', '', $secriptName);
        $requestUri = str_replace($secriptName,'',$requestUri);
        return $requestUri;
    }

    public function __get($name)
    {
        return $this->config[$name];
    }

    /**
     * 过滤URL中无效字符串
     * @param type $request
     * @return type
     */
    protected function trimRequest($request)
    {
        $filt_str = array('index.php', '?');
        foreach ($filt_str as $v) {
            $request = stristr($request, $v) ? str_replace($v, '', $request) : $request;
        }
        return trim($request, '/');
    }

    /**
     * 打包参数
     * @return $this
     */
    public function run($request = '')
    {
        $request = !empty($request) ? $this->trimRequest($request) : $this->trimRequest($this->request);
        if ((stristr($request, '=') || stristr($request, '&')) && !stristr($request, '/')) {
            $data = $this->dispatchByDynamic();
        } else {
            switch ($this->urlMode) {
                #pathinfo模式
                case 1:
                    $data = $this->dispatchByPathinfo($request);
                    break;
                #兼容模式
                case 2:
                    $data = $this->dispatchByCompromise($request);
                    break;
                default:
                    $data = $this->dispatchByCompromise($request);
            }
        }
        $data['module'] = empty($data['module']) ? ucfirst($this->defaultModule) : ucfirst($data['module']);
        $data['controller'] = empty($data['controller']) ? ucfirst($this->defaultController) : ucfirst($data['controller']);
        $data['action'] = empty($data['action']) ? lcfirst($this->defaultAction) : lcfirst($data['action']);
        return $data;
    }

    public function dispatchByPathinfo($request)
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        $path = explode('/', $request);
        if (!$path) {
            return $data;
        }
        foreach ($data as $k => $v) {
            $data[$k] = array_shift($path);
        }
        $param = array();
        if ($path) {
            $key = array();
            $value = array();
            foreach ($path as $k => $v) {
                if ($k % 2 == 0) {
                    $key[] = $v;
                } else {
                    $value[] = $v;
                }
            }
            if (count($key) > count($value)) {
                for ($i = 0; $i <= count($key) - count($value); $i++) {
                    $value[] = '';
                }
            }
            $param = array_combine($key, $value);
        }
        $_GET = array_merge($_GET, $param);
        $_REQUEST = array_merge($_REQUEST, $param);
        return $data;
    }

    /**
     * 打包url参数by兼容模式
     * @param $data
     * @return mixed
     */
    public function dispatchByCompromise($request)
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        $request = trim($request, '/,?');
        $getParam = '';
        if (strstr($request, '&')) {
            $getParam = substr($request, strpos($request, '&'));
            $request = substr($request, 0, strpos($request, '&'));
        }
        if (stristr($request, '/')) {
            $request = explode('/', $request);
        } else {
            return $this->dispatchByDynamic($request);
        }
        if (!empty($request)) {
            $data['module'] = array_shift($request);
            $data['controller'] = array_shift($request);
            $data['action'] = array_shift($request);
            if (!empty($request)) {
                foreach ($request as $k => $v) {
                    if ($k % 2 == 0) {
                        $_GET[$v] = empty($request[$k + 1]) ? '' : $request[$k + 1];
                    }
                }
            }
        }
        if ($getParam) {
            $getParam = trim($getParam, '&');
            $getParam = explode('&', $getParam);
            array_walk($getParam, function ($v) {
                $param = explode('=', $v);
                $_GET[$param[0]] = $param[1];
            });
        }
        return $data;
    }

    /**
     * 打包url参数by动态传参模式
     * @param type $request
     * @return type
     */
    public function dispatchByDynamic()
    {
        $data = array(
            'module' => '',
            'controller' => '',
            'action' => '',
        );
        $pattern_arr = array(
            $this->varModule => 'module',
            $this->varController => 'controller',
            $this->varAction => 'action'
        );
        foreach ($pattern_arr as $k => $v) {
            if (isset($_GET[$k])) {
                $data[$v] = $_GET[$k];
                unset($_GET[$k]);
                unset($_REQUEST[$k]);
            }
        }
        return $data;
    }

    public function config($name, $value = NULL)
    {
        if (is_array($name)) {
            array_walk($name, function ($v, $k) {
                $this->config[$k] = $v;
            });
        } else {
            if (array_key_exists($name, $this->config)) {
                $this->config[$name] = $value;
            }
        }
    }

}
