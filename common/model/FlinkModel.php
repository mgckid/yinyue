<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2016/7/29
 * Time: 15:01
 */

namespace app\model;


class FlinkModel extends BaseModel
{
    protected $tableName = 'site_flink';

    /**
     * 获取权限列表
     * @param type $field
     * @return type
     */
    public function getFlinkList($offset, $limit, $isCount = FALSE, $field = array('*'))
    {
        $obj = $this->orm();
        if ($isCount) {
            $result = $obj->count();
        } else {
            $result = $obj->select($field)
                ->limit($limit)
                ->offset($offset)
                ->findArray();
        }
        return $result;
    }
} 