<?php

/**
 * Description of DB
 * 2016年4月18日 22:59:59
 * @author Administrator
 */

namespace maplesoft\base;

use idiorm\orm\ORM;

class DB extends ORM {

    /**
     * 设置ORM配置
     * @param type $key
     * @return type
     */
    protected static function setOrm($key)
    {
        if (isset(self::$_db[$key])) {
            return;
        }
        $config = C('DB');
        $dbConfig = $config[$key];
        $dsn = sprintf('mysql:host=%s;dbname=%s;port=%s', $dbConfig['DB_HOST'], $dbConfig['DB_NAME'], $dbConfig['DB_PORT']);
        $user = $dbConfig['DB_USER'];
        $pwd = $dbConfig['DB_PWD'];
        ORM::configure(
            array(
                'connection_string' => $dsn,
                'username' => $user,
                'password' => $pwd,
                'logging' => TRUE,
                'driver_options' => array(
//                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $dbConfig['DB_CHARSET'],
                )
            ), NULL, $key
        );
    }

    /**
     * 调用ORM for_table方法
     * @param type $tableName
     * @param type $key
     * @return type
     */
    public static function table($tableName, $key = self::DEFAULT_CONNECTION) {
        self::setOrm($key);
        return ORM::for_table($tableName, $key);
    }

    /**
     * 开启事务
     */
    public static function beginTransaction($key = self::DEFAULT_CONNECTION) {
        self::setOrm($key);
        self::get_db($key)->beginTransaction();
    }

    /**
     * 事务回滚
     */
    public static function rollBack($key = self::DEFAULT_CONNECTION) {
        self::setOrm($key);
        self::get_db($key)->rollBack();
    }

    /**
     * 事务提交
     */
    public static function commit($key = self::DEFAULT_CONNECTION) {
        self::setOrm($key);
        self::get_db($key)->commit();
    }

    /**
     * 获取最后一次执行的sql语句
     * @return type
     */
    public static function _sql($key = self::DEFAULT_CONNECTION) {
        return ORM::get_last_query($key);
    }

    /**
     * 获取最后一次执行的sql语句
     * @return type
     */
    public static function sqlLog($key = self::DEFAULT_CONNECTION) {
        return ORM::get_query_log($key);
    }

}
