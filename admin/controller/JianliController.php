<?php


namespace app\controller;
use houduanniu\base\Page;

/**
 * 运营管理控制器
 * @privilege 简历管理|Admin/Jianli|4f44935e-250d-11e7-8adc-2145f6063705|1
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2016/7/29
 * Time: 14:12
 */
class JianliController extends UserBaseController
{
    /**
     * 添加工作记录
     * @privilege 添加工作记录|Admin/Jianli/addJob|8124db6e-250d-11e7-8adc-2145f6063705|3
     * @since  2017年4月12日 17:21:38
     */
    public function addJob()
    {
        if (IS_POST) {
            $model = new \app\model\JobRecordModel();
            #验证
            $rule = array(
                'id' => 'required|integer',
                'title' => 'required',
                'begin_time' => 'required|date',
            );
            $attr = array(
                'id' => '事件ID',
                'title' => '事件标题',
                'begin_time' => '开始时间',
            );
            $validate = $model->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $eventId = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $subTitle = isset($_POST['sub_title']) ? trim($_POST['sub_title']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $beginTime = isset($_POST['begin_time']) ? trim($_POST['begin_time']) : '';
            $endTime = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
            $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 100;


            $data = array(
                'event_id' => $eventId,
                'title' => $title,
                'sub_title' => $subTitle,
                'description' => $description,
                'begin_time' => $beginTime,
                'end_time' => $endTime,
                'sort' => $sort,
                'created' => date('Y-m-d H:i:s', time()),
                'modified' => date('Y-m-d H:i:s', time()),
            );
            if ($id) {#更新记录
                unset($data['created']);
                $result = $model->orm()
                    ->find_one($id)
                    ->set($data)
                    ->save();
            } else {#插入新记录
                $result = $model->orm()
                    ->create($data)
                    ->save();
            }

            if (!$result) {
                $this->ajaxFail('添加失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $info = array(
                'id' => $id,
                'event_id' => '',
                'title' => '',
                'sub_title' => '',
                'description' => '',
                'begin_time' => '',
                'end_time' => '',
                'sort' => 100
            );
            if ($id) {
                $model = new \app\model\JobRecordModel();
                $result = $model->orm()
                    ->find_one($id)
                    ->as_array();
                $info = $result;
            }
            $eventTypeModel = new \app\model\EventTypeModel();
            $eventType = $eventTypeModel->orm()
                ->select(array('event_id', 'event_name'))
                ->find_array();
            $data = array(
                'info' => $info,
                'event_type' => $eventType,
            );
            #面包屑导航
            $this->crumb(array(
                '运营管理' => U('Operation/index'),
                '添加事件' => ''
            ));
            $this->display('Jianli/addJob', $data);
        }
    }

    /**
     * 工作记录管理
     * @privilege 工作记录管理|Admin/Jianli/jobList|a14ba4ea-250d-11e7-8adc-2145f6063705|2
     * @since  2017年4月12日 17:21:38
     */
    public function jobList()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 20;
        $model = new \app\model\JobRecordModel();
        $count = $model->getEventList('', '', true);
        $page = new Page($count, $p, $pageSize, false);
        $result = $model->getEventList($page->getOffset(), $pageSize, false);
        $data = array(
            'list' => $result,
            'page' => $page->getPageStruct(),
        );
        #面包屑导航
        $this->crumb(array(
            '运营管理' => U('Operation/index'),
            '事件管理' => ''
        ));
        $this->display('Jianli/jobList', $data);
    }

    /**
     * 删除工作
     * @privilege 删除工作|Admin/Jianli/deljob|d897a8d3-250d-11e7-8adc-2145f6063705|3
     * @since  2017年4月12日 17:21:38
     */
    public function deljob()
    {
        if (!IS_POST) {
            $this->ajaxFail('非法访问');
        }
        $model = new \app\model\JobRecordModel();
        #验证
        $rule = array(
            'id' => 'required|numeric|integer',
        );
        $attr = array(
            'id' => '事件ID',
        );
        $validate = $model->validate($_POST, $rule, $attr);
        if (false === $validate->passes()) {
            $this->ajaxFail($validate->messages()->first());
        }
        #获取参数
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $record = $model->orm()
            ->find_one($id);
        if (!$record) {
            $this->ajaxFail('记录不存在');
        }
        if (!$record->delete()) {
            $this->ajaxFail('删除失败');
        } else {
            $this->ajaxSuccess('删除成功');
        }
    }
}