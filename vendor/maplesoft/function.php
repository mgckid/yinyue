<?php

/**
 * 组装url
 * @param $url
 * @param array $parm
 * @return string
 */
function U($url, $parm = array())
{
    $url = trim($url, '/');
    $root = '/';
    switch (C('REQUEST.URL_MODE')) {
        case 0:
            $pattern_arr = explode('/', $url);
            $count = count($pattern_arr);
            $key_arr = array(C('REQUEST.VAR_CONTROLLER'), C('REQUEST.VAR_ACTION'));
            $_path = array_combine(array_slice($key_arr, 0, $count), $pattern_arr);
            $_path = array_merge($_path, $parm);
            $path = '?' . http_build_query($_path);
            break;
        case 1:
            // $root.= '/index.php/';
            if ($parm) {
                foreach ($parm as $k => $v) {
                    $k = trim($k);
                    $v = trim($v);
                    $_param .= '/' . $k . '/' . $v;
                }
            }
            break;
    }
    return $root . $path;
}

function W($method, array $parm = array())
{
    $_method = explode('/', $method);
    $className = array_shift($_method);
    $methodName = array_shift($_method);
    if (!$className || !$methodName) {
        return '';
    }
    $className = 'app\\widget\\' . ucfirst($className) . 'Widget';
    if (!class_exists($className) || !method_exists($className, $methodName)) {
        return '';
    }
    return call_user_func_array(array(new $className, $methodName), $parm);
}

/**
 * 获取客户端ip
 * @return string
 */
function getClientIp()
{
    if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
    return $ip;
}

/**
 * 打印数组 调试用
 * @param type $var
 */
function print_g($var)
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    exit();
}

/**
 * 递归创建多级目录
 * @param type $dirPath
 * @param type $mode
 * @return boolean
 */
function mkdirs($dirPath, $mode = 0777)
{
    if (!is_dir($dirPath)) {
        if (!mkdirs(dirname($dirPath))) {
            return FALSE;
        }
        if (!mkdir($dirPath, $mode)) {
            return FALSE;
        }
    }
    return TRUE;
}

/**
 * 创建文件
 * @param type $filePath 文件路径
 * @param type $content 文件内容
 * @return boolean
 */
function mkFile($filePath, $content)
{
    if (is_file($filePath))
        return true;
    mkdirs(dirname($filePath));
    $handle = fopen($filePath, 'w');
    if (!$handle)
        return FALSE;
    if (!fwrite($handle, $content))
        return false;
    fclose($handle);
    return true;
}

/**
 * 框架内置打印调试信息函数
 * @param type $var
 */
function dump($var, $echo = true, $label = null, $strict = true)
{
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * 创建应用目录
 * @param type $check
 * @return boolean
 */
function createApplication()
{
    #创建应用目录
    if (!C('CREATE_APPLICATION'))
        return true;
    $helper = new \Extend\Helper();
    $helper->buildApplication();
}





/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suff = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if (mb_strlen($str, 'utf8') > $length && $suff) {
        return $slice . '...';
    } else {
        return $slice;
    }
}

/**
 * curl get获取数据
 * @param $url 请求链接
 * @param array $data 表单数据
 * @return mixed
 */
function requestGet($url, array  $data = array())
{
    $header = array(
        'token:' . md5(time()),
        'username:furong'
    );
    if (is_array($data) && !empty($data)) {
        $url .= http_build_query($data, '', '&');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    {
        curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1');
        curl_setopt($ch, CURLOPT_PROXYPORT, '7777');
    }
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function requestData($url, $data = array(), $isPost = false)
{
    if (!$isPost) {
        return requestGet($url, $data);
    }

}

/* * ********************别名函数 开始********************************* */

/**
 * 框架getConfig方法 别名函数
 * @param type $configName
 * @return type
 */
function C($configName)
{
    return \maplesoft\base\Application::getInstance()->config->get($configName);
}

/**
 * 设置失败信息
 * @param type $msg
 * @return boolean
 */
function fail($msg = '执行失败')
{
    \System::setMessage($msg);
    return FALSE;
}

/**
 * 设置成功信息
 * @param type $msg
 * @return boolean
 */
function success($msg = '执行成功')
{
    \System::setMessage($msg);
    return TRUE;
}

/* * ********************别名函数 结束********************************* */
