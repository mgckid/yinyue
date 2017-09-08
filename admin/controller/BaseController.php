<?php

/**
 * Description of BaseController
 *
 * @author Administrator
 */

namespace app\controller;

use houduanniu\base\Controller;
use houduanniu\base\View;
use houduanniu\base\Application;

class BaseController extends Controller
{


    /**
     * 输出模版方法
     * @param type $view
     * @param type $data
     */
    public function display($view, $data = array())
    {
        View::setViewDir(__PROJECT__ . '/' . strtolower(Application::getModule()) . '/' . C('DIR_VIEW').'/'.C('THEME'));
        View::display($view, $data);
    }


}
