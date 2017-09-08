<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/22
 * Time: 15:54
 */

namespace maplesoft\base;


class Config extends \Noodlehaus\Config
{
    protected function getDefaults()
    {
        return array(
            /* 数据库设置 */
            'DB' => array(
                'default' => array(
                    'DB_NAME' => '',
                    'DB_HOST' => '',
                    'DB_USER' => '',
                    'DB_PWD' => '',
                    'DB_PREFIX' => '',
                    'DB_TYPE' => 'mysql',
                    'DB_PORT' => '3306',
                    "DB_CHARSET" => 'utf8',
                )
            ),
            /*应用设置*/
            'APP' => array(
                'CLASS_EXT' => '.php', #类文件后缀名
                'CONTROLLER_EXT' => 'Controller', #控制器后缀名;
                'MODEL_EXT' => 'Model', #模型后缀名
                'DIR_CONTROLLER' => 'controller',
                'DIR_MODEL' => 'model',
            ),
            /*http请求设置*/
            'REQUEST' => array(
                /* URL设置 */
                'URL_MODE' => 0, //url访问模式  0：默认动态url传参模式 1：pathinfo模式 2:兼容模式
                /*默认设置*/
                'DEFAULT_CONTROLLER' => 'Index',
                'DEFAULT_ACTION' => 'index',
                /* 系统变量名设置 */
                'VAR_CONTROLLER' => 'c',
                'VAR_ACTION' => 'a',
                'VAR_MODULE' => 'm',
            ),
            'VALIDATE_MESSAGE' => '../../vendor/overtrue/zh-CN/validation.php',
        );
    }
}