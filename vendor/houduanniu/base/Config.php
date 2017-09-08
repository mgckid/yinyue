<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/22
 * Time: 15:54
 */

namespace houduanniu\base;


class Config extends \Noodlehaus\Config
{
    protected function getDefaults()
    {
        return array(
            /* 数据库设置 开始 */
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
            /* 数据库设置 结束*/

            /*应用设置 开始*/
            'EXT_CONTROLLER'=>'Controller',
            'EXT_MODEL'=>'Model',
            'DIR_CONTROLLER' => 'controller',
            'DIR_MODEL' => 'model',
            'DIR_VIEW' => 'view',
            'THEME'=>'default',
            /*应用设置 结束*/

            /*http请求设置 开始*/
            /* URL设置 */
            'URL_MODE' => 0, //url访问模式  0：默认动态url传参模式 1：pathinfo模式 2:兼容模式
            /*默认设置*/
            'ALLOW_MODULE_LIST' => 'home',
            'DEFAULT_MODULE' => 'home',
            'DEFAULT_CONTROLLER' => 'Index',
            'DEFAULT_ACTION' => 'index',
            /* 系统变量名设置 */
            'VAR_CONTROLLER' => 'c',
            'VAR_ACTION' => 'a',
            'VAR_MODULE' => 'm',
            'VAR_ROUTE' => 'route',
            /*子域名泛解析设置*/
            'MAIN_DOMAIN' => '',
            'SUB_DOMAIN_OPEN' => true,
            'SUB_DOMAIN_RULES' => [
                'www' => 'home'
            ],
            /*http请求设置 结束*/

            /*自定义载入文件*/
            'LOAD_FILES' => [

            ]
        );
    }
}