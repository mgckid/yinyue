<?php

/**
 * Description of Model
 *
 * @author Administrator
 *
 * 2016年4月14日 23:33:22
 */

namespace houduanniu\base;

use Overtrue\Validation\Factory;
use Overtrue\Validation\Translator;

class Model
{
    protected $validateFactory;
    protected $tableName = '';
    protected $pk = '';

    /**
     * 获取orm 对象
     * @param type $table
     * @param type $dbName
     * @return type
     */
    public function orm($tableName = '', $key = DB::DEFAULT_CONNECTION)
    {
        $tableName = empty($tableName) ? $this->tableName : $tableName;
        if (!$tableName) {
            trigger_error('表名不能为空', E_USER_ERROR);
        }
//        if (!isset($this->data[$tableName])) {
//            $this->data[$tableName] = DB::table($tableName, $key);
//        }
        $orm = DB::table($tableName, $key);
        if ($this->pk) {
            $orm = $orm->use_id_column($this->pk);
        }
        return $orm;
    }

    /**
     * 开启事务
     */
    public function beginTransaction($key = DB::DEFAULT_CONNECTION)
    {
        DB::beginTransaction($key);
    }

    /**
     * 事务回滚
     */
    public function rollBack($key = DB::DEFAULT_CONNECTION)
    {
        DB::rollBack($key);
    }

    /**
     * 事务提交
     */
    public function commit($key = DB::DEFAULT_CONNECTION)
    {
        DB::commit($key);
    }

    /**
     * 获取最后一次执行的sql语句
     * @return type
     */
    public function _sql($key = DB::DEFAULT_CONNECTION)
    {
        return DB::_sql($key);
    }


    /**
     * 获取日期时间
     * @param string $format
     * @return bool|string
     */
    public function getDateTime($format = 'Y-m-d H:i:s')
    {
        return date($format, time());
    }

    public function setMessage($msg)
    {
        return Application::getInstance()->setMessage($msg);
    }

    public function getMessage()
    {
        return Application::getInstance()->getMessage();
    }

    public function getInfo($key)
    {
        return Application::getInstance()->getInfo($key);
    }

    public function setInfo($key, $value)
    {
        Application::getInstance()->setInfo($key, $value);
    }


    public function validate(array $data, array $rules, array $customAttributes = [])
    {
        return $this->getValidateFactory()->make($data, $rules, array(), $customAttributes);
    }

    private function getValidateFactory()
    {
        if (empty($this->validateFactory)) {
            $lang = require __VENDOR__ . '/overtrue/zh-CN/validation.php';
            $this->validateFactory = new Factory(new Translator($lang));
            require __VENDOR__ . '/overtrue/validation/src/helpers.php';
        }
        return $this->validateFactory;
    }

}
