<?php

/**
 * 登录注册控制器
 * @date 2016年5月1日 11:11:33
 * @author Administrator
 */

namespace app\controller;

use app\model\UserRoleModel;
use app\model\UserModel;

class LoginController extends BaseController
{



    /**
     * 后台登录
     */
    public function index()
    {
        if (IS_POST) {
            $userModel = new UserModel();
            #验证
            $rules = array(
                'username' => 'required|alpha',
                'password' => 'required|alpha_num|min:6',
            );
            $attr = array(
                'username' => '用户名',
                'password' => '密码',
            );
            $validate = $userModel->validate($_POST, $rules, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }

            #获取参数
            $userName = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            $userRoleModel = new UserRoleModel();
            if (!$userModel->validatePassword($userName, $password)) {
                $this->ajaxFail($this->getMessage());
            }
            $userinfo = $userModel->getUserInfo($userName);
            $_SESSION['loginInfo'] = $userinfo;
            $_SESSION['loginInfo']['roleInfo'] = $userRoleModel->getRoleByUserId($userinfo['user_id']);
            $this->ajaxSuccess('登陆成功');
        } else {
            if (isset($_SESSION['loginInfo']) && !empty($_SESSION['loginInfo'])) {
                $this->redirect(U('Index'));
            }
            $this->display('Login/index');
        }
    }

    /**
     * 登出系统
     */
    public function logout()
    {
        if (!IS_POST)
            $this->ajaxFail('非法访问');
        $logout = isset($_POST['logout']) ? $_POST['logout'] : false;
        if (!$logout)
            $this->ajaxFail('非法访问');
        if (isset($_SESSION['loginInfo'])) {
            unset($_SESSION['loginInfo']);
        }
        $this->ajaxSuccess();
    }

}
