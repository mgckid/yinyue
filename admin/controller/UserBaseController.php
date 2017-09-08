<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 20:52
 */

namespace app\controller;


use houduanniu\base\Application;
use app\model\SiteSetModel;
use houduanniu\base\View;

class UserBaseController extends BaseController
{
    public $loginInfo;
    #面包屑导航
    protected $crumbHtml;


    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        if (!$this->checkPower() && $this->environment != 'develop') {
            if (IS_POST || IS_AJAX) {
                $this->ajaxFail('没有权限');
            } else {
                die('没有权限');
            }
        };
    }

    /**
     * 检查用户是否登录
     */
    public function checkLogin()
    {
        if (isset($_SESSION['loginInfo']) && !empty($_SESSION['loginInfo'])) {
            $this->setInfo('loginInfo', $_SESSION['loginInfo']);
            $this->loginInfo = $this->getInfo('loginInfo');
            return $this->loginInfo;
        } else {
            $this->redirect(U('Login'));
        }
    }

    public function checkPower($access = '')
    {
        $userId = $this->getInfo('loginInfo')['user_id'];
        if (!$access) {
            $access = strtolower(Application::getController() . '/' . Application::getAction());
        } else {
            $access = trim(strtolower($access), '/');
        }

        #跳过共享权限
        $sharePower = array('Index/index', 'Rbac/ajaxCheckPower');
        foreach ($sharePower as $v) {
            if (strtolower($access) == strtolower($v)) {
                return true;
            }
        }
        $userModel = new \app\model\UserModel();
        $accessList = $userModel->orm()
            ->table_alias('u')
            ->select(array('a.module', 'a.controller', 'a.action'))
            ->left_outer_join('admin_user_role', array('u.user_id', '=', 'ur.user_id'), 'ur')
            ->left_outer_join('admin_role_access', array('ur.role_id', '=', 'ra.role_id'), 'ra')
            ->left_outer_join('admin_access', array('ra.access_sn', '=', 'a.access_sn'), 'a')
            ->where(array('u.user_id' => $userId))
            ->where_not_equal(array('a.action' => ''))
            ->find_array();
        $accessArr = array();
        foreach ($accessList as $v) {
            $accessArr[] = strtolower(trim($v['controller']) . '/' . trim($v['action']));
        }
        if (in_array($access, $accessArr)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /**
     * 面包屑导航
     * @param $crumb
     */
    public function crumb($crumbs = array())
    {
        $crumbsHtml = '';
        if (!empty($crumbs)) {
            $crumbsHtml .= '<li><a href="' . U('/Index/index') . '"><i class="fa fa-home"></i> 首页</a></li>';
            $n = 0;
            foreach ($crumbs as $key => $value) {
                $n++;
                $link_s = !empty($value) ? '<a href="' . $value . '">' : '';
                $link_e = !empty($value) ? '</a>' : '';
                if ($n == count($crumbs)) {
                    $crumbsHtml .= '<li class="active color4">' . $key . '</li>';
                } else {
                    $crumbsHtml .= '<li>' . $link_s . $key . $link_e . '</li>';
                }
            }
        }
        //赋值给公共变量
        $this->crumbHtml = $crumbsHtml;
    }

    public function display($view, $data = array())
    {
        #登录信息
        $loginInfo = $this->getInfo('loginInfo');
        $shareData = array(
            'loginInfo' => $loginInfo,
        );
        #站点信息
        $siteModel = new SiteSetModel();
        $siteInfo = $siteModel->orm()->find_one()->as_array();
        $shareData['siteInfo'] = $siteInfo;
        #左侧菜单
        $model = new \app\model\UserModel();
        $roleInfo = $this->getInfo('loginInfo')['roleInfo'];
        $list = $model->orm('admin_access')
            ->select_expr('ac.*')
            ->table_alias('ac')
            ->left_join('admin_role_access', array('arc.access_sn', '=', 'ac.access_sn'), 'arc')
            ->where_in('arc.role_id', $roleInfo)
            ->where_in('ac.level',[1,2])
            ->find_array();
        foreach ($list as $k => $v) {
            $v['url'] = U($v['controller'] . '/' . $v['action']);
            $v['active'] = strtolower(Application::getController()) == strtolower($v['controller']) ? 'active menu-open' : '';
            $list[$k] = $v;
        }
        $shareData['menu'] = treeStructForLayer($list);
        $shareData['crumbs'] = $this->crumbHtml;
        View::addData($shareData);
        parent::display($view, $data);
    }

}