<?php

/**
 * Description of CmsPostModel
 *
 * @date 2016年5月8日 14:11:16
 *
 * @author Administrator
 */

namespace app\model;

use app\model\BaseModel;
use Core\DB;

class CmsPostModel extends BaseModel
{

    public $tableName = 'cms_post';
    public $pk = 'id';

    /**
     * 获取文章列表
     * @param type $condition 条件
     * @param type $offset 偏移量
     * @param type $limit 获取条数
     * @param type $forCount 统计
     * @param type $field 字段
     * @return type
     */
    public function getArticleList($condition, $offset, $limit, $forCount = false, $field = 'a.*')
    {
        $orm = $this->orm()
            ->table_alias('a')
            ->select_expr($field)
            ->left_outer_join('cms_category', array('a.column_id', '=', 'c.id'), 'c');
        if ($condition) {
            foreach ($condition as $key => $value) {
                $orm = call_user_func_array(array($orm, $key), $value);
            }
        }
        if ($forCount) {
            $result = $orm->count();
        } else {
            $result = $orm
                ->limit($limit)
                ->offset($offset)
                ->order_by_desc('id')
                ->find_array();
        }
        return $result;
    }

    /**
     * 获取文章信息
     * @param type $id 文章id
     * @param type $field 字段名
     * @return type
     */
    public function getArticleInfoById($id, $field = "a.*")
    {
        $result = $this->orm()
            ->table_alias('a')
            ->left_outer_join('cms_category', array('a.column_id', '=', 'c.id'), 'c')
            ->select_expr($field)
            ->find_one($id);
        if (!$result) {
            return false;
        }
        return $result->as_array();;
    }

    /**
     * 根据条件获取内容信息
     * @access public
     * @author furong
     * @param $condition
     * @param $field
     * @return bool
     * @since 2017年4月26日 10:11:55
     * @abstract
     */
    public function getPostInfo($condition, $field = 'a.*')
    {
        $orm = $this->orm();
        if ($condition) {
            foreach ($condition as $key => $value) {
                $orm = call_user_func_array(array($orm, $key), $value);
            }
        }
        $result = $orm->table_alias('a')
            ->select_expr($field)
            ->left_outer_join('cms_category', array('a.column_id', '=', 'c.id'), 'c')
            ->find_one();
        if (!$result) {
            return false;
        }
        return $result->as_array();
    }

    /**
     * 添加文章
     *
     * @access public
     * @author furong
     * @param $data
     * @return bool
     * @since  2017年4月10日 09:59:33
     * @abstract
     */
    public function addPost($data)
    {
        $return = false;
        $id = $data[$this->pk];
        $data['modified'] = $this->getDateTime();
        $model = $this->orm();
        if (empty($data[$this->pk])) {
            #添加
            unset($data[$this->pk]);
            $data['created'] = $this->getDateTime();
            $return = $model->create($data)
                ->save();
            $id = $model->id();
        } else {
            #修改
            $result = $model->find_one($id);
            if ($result) {
                $return = $result->set($data)
                    ->save();
            } else {
                $this->setMessage('文章不存在');
            }
        }
        return $return ? $id : $return;
    }

    /**
     * 获取推荐文章列表
     * @param array $condition
     * @param $rows
     * @param string $field
     * @return type
     */
    public function getRecommendArticleList(array $condition, $rows, $field = '*')
    {
        return $this->getArticleList($condition, 0, $rows, false, $field);
    }

    /**
     * 获取下一篇文章
     * @param $id
     * @param $cateid
     * @param string $filed
     * @return mixed
     */
    public function getNext($id, $cateid, $filed = 'id,title,title_alias')
    {
        $result = $this->orm()
            ->select_expr($filed)
            ->where('column_id', $cateid)
            ->where_gt('id', $id)
            ->order_by_asc('id')
            ->find_one();
        if ($result) {
            $result = $result->as_array();
        }
        return $result;
    }

    /**
     * 获取上一篇文章
     * @param $id
     * @param $cateid
     * @param string $filed
     * @return mixed
     */
    public function getPre($id, $cateid, $filed = 'id,title,title_alias')
    {
        $result = $this->orm()
            ->select_expr($filed)
            ->where('column_id', $cateid)
            ->where_lt('id', $id)
            ->order_by_desc('id')
            ->find_one();
        if ($result) {
            $result = $result->as_array();
        }
        return $result;
    }

    /**
     * 获取热门文章
     * @access public
     * @author furong
     * @param int $limit
     * @param string $field
     * @param string $cateId
     * @return mixed
     * @since 2017年4月24日 16:09:09
     * @abstract
     */
    public function getHotPost($limit = 10, $field = 'a.*', $cateId = '')
    {
        $orm = $this->orm()
            ->table_alias('a')
            ->select_expr($field)
            ->left_outer_join('cms_category', array('a.column_id', '=', 'c.id'), 'c');
        if ($cateId) {
            $orm = $orm->where('a.column_id', $cateId);
        }
        $result = $orm
            ->limit($limit)
            ->order_by_desc('click')
            ->find_array();
        return $result;
    }

    /**
     * 获取相关文章
     * @access public
     * @author furong
     * @param $postId
     * @param string $field
     * @param int $limit
     * @return array
     * @since 2017年4月25日 18:01:30
     * @abstract
     */
    public function getRelatedPost($postId, $field = 'p.*', $limit = 10)
    {

        $cateRelatedResult = [];
        #标签相关文章
        $tagRelatedResult = $this->orm()
            ->for_table('cms_post_tag')
            ->table_alias('pt')
            ->select_expr($field)
            ->left_join('cms_post_tag', 'ptt.tag_id = pt.tag_id', 'ptt')
            ->left_join('cms_post', 'ptt.post_id = p.id', 'p')
            ->where('pt.post_id', $postId)
            ->where_not_equal('p.id', $postId)
            ->group_by('p.id')
            ->order_by_desc('p.id')
            ->limit($limit)
            ->find_array();
        $resultRows = count($tagRelatedResult);
        #栏目相关文章
        if ($resultRows < $limit) {

            $limit = $limit - $resultRows;
            $cateId = current($this->getArticleInfoById($postId,'a.column_id'));
            $cateModel = new CmsCategoryModel();
            $condition = [
                'where' => ['cate_type', $cateModel::CATE_TYPE_LIST],
            ];
            $cateList = $cateModel->getColumnList($condition);
            $subCate = array_column(treeStructForLevel($cateList, $cateId), 'id');
            $subCate[] = $cateId;
            $notInPostId = array_values(array_column($tagRelatedResult, 'id'))+[$postId];
            $condition = [
                'where_not_in' => ['a.id',$notInPostId],
                'where_in' => ['a.column_id', $subCate],
            ];
            $cateRelatedResult = $this->getArticleList($condition, 0, $limit, false, 'a.id,a.title,a.image_name,a.title_alias');
        }
        $result = array_merge($tagRelatedResult, $cateRelatedResult);
        return $result;
    }

}
