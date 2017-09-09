<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/12
 * Time: 9:32
 */

namespace app\controller;


use app\model\SiteSetModel;
use app\model\CmsCategoryModel;
use app\model\FlinkModel;
use houduanniu\base\Controller;
use houduanniu\base\View;
use houduanniu\base\Application;
use app\model\AdvertisementListModel;

class BaseController extends Controller
{
    #面包屑导航
    protected $crumbHtml;
    public $imageSize;

    public $siteInfo;

    function __construct()
    {
        parent::__construct();
        $this->imageSize = C('IMAGE_SIZE');
        $this->siteInfo = $this->getSiteInfo();
    }

    /**
     * 面包屑导航
     * @param $crumb
     */
    public function crumb($crumbs = array())
    {
        $crumbsHtml = '';
        if (!empty($crumbs)) {
            $crumbsHtml .= '<li><a href="' . U('/Index/index') . '">首页</a></li>';
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

    /**
     * 获取站点信息
     */
    public function getSiteInfo()
    {
        $siteModel = new SiteSetModel();
        $siteInfo = $siteModel->orm()
            ->select_expr('site_name,site_keyword as keyword,site_description as description,short_site_name as short_name,found_time,icp_code')
            ->find_one()
            ->as_array();
        return $siteInfo;
    }

    /**
     * 输出模版方法
     * @param type $view
     * @param type $data
     */
    public function display($view, $data = array(), $seoInfo = array())
    {
        #站点信息
        {
            $siteInfo = $this->siteInfo;
            $siteInfo['title'] = !empty($seoInfo['title']) ? $seoInfo['title'] . '_' . $siteInfo['site_name'] : $siteInfo['site_name'];
            $siteInfo['keyword'] = !empty($seoInfo['keyword']) ? $seoInfo['keyword'] : $siteInfo['keyword'];
            $siteInfo['description'] = !empty($seoInfo['description']) ? $seoInfo['description'] : $siteInfo['description'];

            $reg['siteInfo'] = $siteInfo;
            $reg['crumbs'] = $this->crumbHtml;
        }
        #获取头部导航
        {
            $cateModel = new CmsCategoryModel();
            $condition = [
                'where' => array('nav_display', $cateModel::NAV_DISPLAY),
            ];
            $filed = 'id,pid,name,alias,jump_url,cate_type,sort';
            $result = $cateModel->getColumnList($condition, $filed);
            $navList = treeStructForLayer($result);
            $sort = array_column($navList, 'sort');
            array_multisort($sort, SORT_ASC, $navList);
            $reg['navList'] = $navList;
        }
        #友情链接
        {
            $flinkModel = new FlinkModel();
            $result = $flinkModel->getFlinkList(0, 10);
            $reg['flink'] = $result;
        }
        #获取广告
        {
            $adList = $this->getSiteAllAdvertisement();
            $reg['adList'] = $adList;
        }
        #获取微信微博数据
        {
            $reg['weixin'] = $cateModel->getCateInfoById(12);
        }
        View::addData($reg);
        View::setViewDir(__PROJECT__ . '/' . strtolower(Application::getModule()) . '/' . C('DIR_VIEW'));
        View::display($view, $data);
    }

    /**
     * 获取网站全站广告
     * @access protected
     * @author furong
     * @return array
     * @since 2017年3月12日 11:33:38
     * @abstract
     */
    protected function getSiteAllAdvertisement()
    {
        $model = new AdvertisementListModel();
        $list = $model->getAdvertisementList([], 0, 999, false, 'al.*,ap.position_name,ap.position_key');
        $data = [];
        foreach ($list as $value) {
            $value['image_url'] = getImage($value['ad_image']);
            $data[$value['position_key']][] = $value;
        }
        return $data;
    }


} 