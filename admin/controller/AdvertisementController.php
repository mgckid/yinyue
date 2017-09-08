<?php

namespace app\controller;


use app\model\AdvertisementListModel;
use app\model\AdvertisementPositioModel;
use houduanniu\base\Page;

/**
 * 广告管理
 *
 * @privilege 广告管理|Admin/Advertisement|5e46e259-2002-11e7-8ad5-9cb3ab404081|1
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/25
 * Time: 22:05
 */
class AdvertisementController extends UserBaseController
{
    /**
     * 增加广告
     * @privilege 增加广告|Admin/Advertisement/addPosition|7c5b0f9a-2002-11e7-8ad5-9cb3ab404081|2
     */
    public function addPosition()
    {
        if (IS_POST) {
            $advertisementPositionModel = new AdvertisementPositioModel();
            #验证
            $rule = array(
                'position_name' => 'required',
                'position_key' => 'required|alpha_dash',
                'ad_width' => 'required|numeric|integer',
                'ad_height' => 'required|numeric|integer',
            );
            $attr = array(
                'position_name' => '广告位名称',
                'position_key' => '广告位标识',
                'ad_width' => '广告宽度',
                'ad_height' => '广告高度',
            );
            $validate = $advertisementPositionModel->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $positionName = isset($_POST['position_name']) ? trim($_POST['position_name']) : '';
            $positionKey = isset($_POST['position_key']) ? trim($_POST['position_key']) : '';
            $positionDescription = isset($_POST['position_description']) ? trim($_POST['position_description']) : '';
            $adWidth = isset($_POST['ad_width']) ? intval($_POST['ad_width']) : 0;
            $adHeight = isset($_POST['ad_height']) ? intval($_POST['ad_height']) : 0;
            #组装数据
            $data = array(
                'id' => $id,
                'position_name' => $positionName,
                'position_key' => $positionKey,
                'position_description' => $positionDescription,
                'ad_width' => $adWidth,
                'ad_height' => $adHeight
            );
            $result = $advertisementPositionModel->addAdvertisementPositio($data);
            if (!$result) {
                $this->ajaxFail('广告位添加失败');
            } else {
                $this->ajaxSuccess('广告位添加成功');
            }

        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

            $advertisementPositionModel = new AdvertisementPositioModel();
            $positionInfo = array(
                'id' => $id,
                'position_name' => '',
                'position_key' => '',
                'position_description' => '',
                'ad_width' => '',
                'ad_height' => ''
            );
            if ($id) {
                $positionInfo = $advertisementPositionModel->getPostionInfoByID($id);
            }
            $data['positionInfo'] = $positionInfo;

            #面包屑导航
            $this->crumb([
                '广告管理' => U('Advertisement/index'),
                '添加广告位' => ''
            ]);
            $this->display('Advertisement/addPosition', $data);
        }
    }

    /**
     * 广告位列表
     * @privilege 广告位列表|Admin/Advertisement/index|92224545-2002-11e7-8ad5-9cb3ab404081|2
     */
    public function index()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 20;

        $advertisementPositionModel = new AdvertisementPositioModel();
        $count = $advertisementPositionModel->getPostionList('', '', true);
        $page = new Page($count, $p, $pageSize);
        $result = $advertisementPositionModel->getPostionList($page->getOffset(), $pageSize, false);
        $data['positionList'] = $result;
        $data['pages'] = $page->getPageStruct();
        #面包屑导航
        $this->crumb([
            '广告管理' => U('Advertisement/index'),
            '广告位列表' => ''
        ]);
        $this->display('Advertisement/index', $data);
    }

    /**
     * 添加广告
     *
     * @privilege 添加广告|Admin/Advertisement/addAd|cd34d4cd-2002-11e7-8ad5-9cb3ab404081|2
     * @access public
     * @author furong
     * @return void
     * @since  2017年4月13日 12:39:59
     * @abstract
     */
    public function addAd()
    {
        if (IS_POST) {
            $advertisementListModel = new AdvertisementListModel();
            #验证
            $rule = array(
                'position_id' => 'required|numeric|integer',
                'ad_title' => 'required',
                'ad_image' => 'required',
                'ad_link' => 'url',
                'sort' => 'integer',
            );
            $attr = array(
                'position_id' => '广告位ID',
                'ad_title' => '广告标题',
                'ad_image' => '广告图片',
                'ad_link' => '广告链接',
                'sort' => '排序值',
            );
            $validate = $advertisementListModel->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $positionId = isset($_POST['position_id']) ? intval($_POST['position_id']) : 0;
            $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 0;
            $adLink = isset($_POST['ad_link']) ? trim($_POST['ad_link']) : '';
            $adTitle = isset($_POST['ad_title']) ? trim($_POST['ad_title']) : '';
            $adImage = isset($_POST['ad_image']) ? trim($_POST['ad_image']) : '';
            $data = array(
                'id' => $id,
                'position_id' => $positionId,
                'ad_title' => $adTitle,
                'ad_image' => $adImage,
                'ad_link' => $adLink,
                'sort' => $sort,
            );
            $result = $advertisementListModel->addAdvertisement($data);
            if (!$result) {
                $this->ajaxFail('添加广告失败' . $this->getMessage());
            } else {
                $this->ajaxSuccess('添加广告成功');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            #获取广告
            $adInfo = array(
                'id' => $id,
                'position_id' => 0,
                'ad_title' => '',
                'ad_image' => '',
                'ad_link' => '',
                'sort' => 100,
            );
            if ($id) {
                $advertisementListModel = new AdvertisementListModel();
                $adInfo = $advertisementListModel->getAdvertisementInfoByID($id);
            }
            $adInfo['ad_image_url'] = $adInfo['ad_image'] ? getImage($adInfo['ad_image']) : '';
            $data['adInfo'] = $adInfo;
            #获取广告位
            $AdvertisementPositionModel = new AdvertisementPositioModel();
            $positionList = $AdvertisementPositionModel->getPostionList(0, 999, false, 'id,position_name,ad_width,ad_height');
            $data['positionList'] = $positionList;
            #面包屑导航
            $this->crumb([
                '广告管理' => U('Advertisement/index'),
                '添加广告' => ''
            ]);
            $this->display('Advertisement/addAd', $data);
        }
    }

    /**
     * 广告列表
     *
     * @privilege 广告列表|Admin/Advertisement/advertisementList|f0a3b5e6-2002-11e7-8ad5-9cb3ab404081|2
     * @access public
     * @author furong
     * @return void
     * @since  2017年4月13日 12:39:59
     * @abstract
     */
    public function advertisementList()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 20;

        $advertisementListModel = new AdvertisementListModel();
        $count = $advertisementListModel->getAdvertisementList(array(), '', '', true);
        $page = new Page($count, $p, $pageSize);
        $result = $advertisementListModel->getAdvertisementList(array(), $page->getOffset(), $pageSize, false);
        $data['adList'] = $result;
        $data['pages'] = $page->getPageStruct();
        #面包屑导航
        $this->crumb([
            '广告管理' => U('Advertisement/index'),
            '广告位列表' => ''
        ]);
        $this->display('Advertisement/advertisementList', $data);
    }

    /**
     * 删除广告位
     *
     * @privilege 删除广告位|Admin/Advertisement/delPosition|2a7bd490-2003-11e7-8ad5-9cb3ab404081|3
     * @access public
     * @author furong
     * @return void
     * @since  2017年4月13日 12:39:59
     * @abstract
     */
    public function delPosition()
    {
        if (!IS_POST) {
            $this->ajaxFail('非法请求');
        }
        $advertisementPositionModel = new AdvertisementPositioModel();
        $advertisementListModel = new AdvertisementListModel();
        #验证
        $rule = array(
            'id' => 'required|numeric|integer',
        );
        $attr = array(
            'id' => '广告位ID',
        );
        $validate = $advertisementPositionModel->validate($_POST, $rule, $attr);
        if (false === $validate->passes()) {
            $this->ajaxFail($validate->messages()->first());
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        #统计广告位下广告
        $where = array(
            'where' => array('al.position_id', $id),
        );
        $adCount = $advertisementListModel->getAdvertisementList($where, '', '', true);
        if ($adCount > 0) {
            $this->ajaxFail('请先删除广告位下广告');
        }
        #删除广告位
        $result = $advertisementPositionModel->orm()
            ->find_one($id)
            ->delete();
        if (!$result) {
            $this->ajaxFail('删除失败');
        } else {
            $this->ajaxSuccess('删除成功');
        }
    }


}