<?php


namespace app\controller;

use app\model\FlinkModel;
use app\model\NavModel;
use houduanniu\base\Page;

/**
 * 运营管理控制器
 * @privilege 运营管理|Admin/Operation|c1a2f7e9-200a-11e7-8ad5-9cb3ab404081|1
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2016/7/29
 * Time: 14:12
 */
class OperationController extends UserBaseController
{

    /**
     * 站点导航管理
     */
    public function index()
    {

    }

    public function addNav()
    {
        if (IS_POST) {
            $nav = new NavModel();
            #验证
            $rule = array(
                'path' => 'required',
                'name' => 'required',
                'url' => 'required|url'
            );
            $attr = array(
                'path' => '路径',
                'name' => '导航名称',
                'url' => '链接'
            );
            $validate = $nav->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $path = isset($_POST['path']) ? trim($_POST['path']) : '';
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $url = isset($_POST['url']) ? trim($_POST['url']) : '';

            $data = array(
                'name' => $name,
                'path' => $path,
                'url' => $url,
                'created' => date('Y-m-d H:i:s', time()),
                'modified' => date('Y-m-d H:i:s', time()),
            );
            if ($id) {#更新记录
                unset($data['created']);
                $result = $nav->orm()
                    ->find_one($id)
                    ->set($data)
                    ->save();
            } else {#插入新纪录
                $result = $nav->orm()
                    ->create($data)
                    ->save();
            }
            if ($result) {
                $this->ajaxSuccess('添加站点导航菜单成功');
            } else {
                $this->ajaxFail('添加站点导航菜单失败');
            }
        } else {
            $navList = $this->handleNavList();
            $data = array(
                'list' => $navList,
            );
            #面包屑导航
            $this->crumb(array(
                '运营管理' => U('Operation/index'),
                '添加站点导航菜单' => ''
            ));
            $this->display('Operation/addNav', $data);
        }
    }

    protected function handleNavList()
    {
        $model = new NavModel();
        $model->init();
        $field = array('id', 'name', 'path');
        $list = $model->orm()
            ->select_many($field)
            ->find_array();
        return $this->handleColumnData($list);
    }

    /**
     * 添加友情链接
     * @privilege 添加友情链接|Admin/Operation/addFlink|c1ac6529-200a-11e7-8ad5-9cb3ab404081|3
     */
    public function addFlink()
    {
        if (IS_POST) {
            $FlinkModel = new FlinkModel();
            #验证
            $rule = array(
                'fname' => 'required',
                'furl' => 'required|url'
            );
            $attr = array(
                'fname' => '站点名称',
                'furl' => '站点链接'
            );
            $validate = $FlinkModel->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
            $furl = isset($_POST['furl']) ? trim($_POST['furl']) : '';
            $fdesc = isset($_POST['fdesc']) ? trim($_POST['fdesc']) : '';
            if (!stristr($furl, 'http://')) {
                $furl = 'http://' . $furl;
            }
            $data = array(
                'fname' => $fname,
                'furl' => $furl,
                'fdesc' => $fdesc,
                'created' => date('Y-m-d H:i:s', time()),
                'modified' => date('Y-m-d H:i:s', time()),
            );
            if ($id) {#更新记录
                unset($data['created']);
                $result = $FlinkModel->orm()
                    ->find_one($id)
                    ->set($data)
                    ->save();
            } else {#插入新纪录
                $result = $FlinkModel->orm()
                    ->create($data)
                    ->save();
            }
            if ($result) {
                $this->ajaxSuccess('友情链接添加成功');
            } else {
                $this->ajaxFail('友情链接添加失败');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $info = array(
                'furl' => '',
                'fname' => '',
                'fdesc' => '',
                'id' => $id,
            );
            if ($id) {
                $FlinkModel = new FlinkModel();
                $result = $FlinkModel->orm()
                    ->find_one($id)
                    ->as_array();
                $info = $result;
            }
            $data = array(
                'info' => $info
            );
            #面包屑导航
            $this->crumb(array(
                '运营管理' => U('Operation/index'),
                '添加友情链接' => ''
            ));
            $this->display('Operation/addFlink', $data);
        }
    }

    /**
     * 友情链接列表
     * @privilege 友情链接列表|Admin/Operation/flinkList|c217dd89-200a-11e7-8ad5-9cb3ab404081|2
     */
    public function flinkList()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 20;
        $flinkModel = new FlinkModel();
        $count = $flinkModel->getFlinkList('', '', true);
        $page = new Page($count, $p, $pageSize, false);
        $result = $flinkModel->getFlinkList($page->getOffset(), $pageSize, false);
        $data = array(
            'list' => $result,
            'page' => $page->getPageStruct(),
        );
        #面包屑导航
        $this->crumb(array(
            '运营管理' => U('Operation/index'),
            '友情链接管理' => ''
        ));
        $this->display('Operation/flinkList', $data);
    }

    /**
     * 删除友情链接
     * @privilege 删除友情链接|Admin/Operation/delFlink|c2235b2e-200a-11e7-8ad5-9cb3ab404081|3
     */
    public function delFlink()
    {
        if (!IS_POST) {
            $this->ajaxFail('非法访问');
        }
        $model = new FlinkModel();
        #验证
        $rule = array(
            'id' => 'required',
        );
        $attr = array(
            'id' => '友情链接ID',
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


    /**
     * 站点设置
     * @privilege 站点设置|Admin/Operation/siteSet|c22f1aa3-200a-11e7-8ad5-9cb3ab404081|2
     */
    public function siteSet()
    {
        if (IS_POST) {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $siteName = isset($_POST['site_name']) ? trim($_POST['site_name']) : '';
            $shortSiteName = isset($_POST['short_site_name']) ? trim($_POST['short_site_name']) : '';
            $siteKeyword = isset($_POST['site_keyword']) ? trim($_POST['site_keyword']) : '';
            $siteDescription = isset($_POST['site_description']) ? trim($_POST['site_description']) : '';
            $found_time = isset($_POST['found_time']) ? trim($_POST['found_time']) : date('Y-m-d', time());
            $model = new \app\model\SiteSetModel();
            $result = $model->orm()->find_one();
            $data = array(
                'site_name' => $siteName,
                'short_site_name' => $shortSiteName,
                'site_keyword' => $siteKeyword,
                'site_description' => $siteDescription,
                'found_time' => $found_time,
                'created' => date('Y-m-d H:i:s', time()),
                'modified' => date('Y-m-d H:i:s', time()),
            );
            if ($id) {
                unset($data['created']);
                $result = $model->orm()
                    ->find_one($id)
                    ->set($data)
                    ->save();
            } else {
                $result = $model->orm()
                    ->create($data)
                    ->save();
            }
            if (!$result) {
                $this->ajaxFail('站点设置失败');
            } else {
                $this->ajaxSuccess('站点设置成功');
            }
        } else {
            $info = array(
                'id' => 0,
                'site_name' => '',
                'site_keyword' => '',
                'site_description' => '',
            );
            $model = new \app\model\SiteSetModel();
            $result = $model->orm()->find_one();
            if ($result) {
                $info = $result;
            }
            #面包屑导航
            $this->crumb(array(
                '运营管理' => U('Operation/index'),
                '站点设置' => ''
            ));
            $data = array(
                'info' => $info,
            );
            $this->display('Operation/siteSet', $data);
        }
    }


    /**
     * 站点配置
     * @privilege 站点配置|Admin/Operation/siteOption|c22f1aa3-246a-11e7-8ad5-9cb3ab404081|2
     */
    public function siteOption()
    {

    }


}
