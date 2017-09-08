<?php

namespace app\controller;

require '../../vendor/phpQuery/phpQuery/phpQuery.php';
use app\model\CollectItemPoolModel;
use app\model\CmsPostModel;
use app\model\CollectRuleModel;
use houduanniu\base\Page;
use phpFastCache\CacheManager;
use QL\QueryList;

/**
 * 采集管理
 * @privilege 采集管理|Admin/Collect|c6d6d95c-2008-11e7-8ad5-9cb3ab404081|1
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/3/27
 * Time: 15:13
 */
class CollectController extends UserBaseController
{
    /**
     * 采集内容列表
     * @privilege 采集内容列表|Admin/Collect/cacheList|c6e03a63-2008-11e7-8ad5-9cb3ab404081|2
     */
    public function cacheList()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 50; #每页条数
        $collectItemPoolModel = new CollectItemPoolModel();
        $collectStatusConfig = [
            0 => '未采集',
            10 => '已采集',
            20 => '采集已处理'
        ];
        $count = $collectItemPoolModel->getItemList('', '', '', true);
        $page = new Page($count, $p, $pageSize);
        $field = 'id,collect_status,title,url,created';
        $itemList = $collectItemPoolModel->getItemList('', $page->getOffset(), $pageSize, false, $field);
        foreach ($itemList as $key => $value) {
            $value['collect_status_text'] = $collectStatusConfig[$value['collect_status']];
            $itemList[$key] = $value;
        }
        #面包屑导航
        $this->crumb(array(
            '采集管理' => U('Collect/index'),
            '缓存列表' => ''
        ));

        $data = [
            'itemList' => $itemList,
            'pages' => $page->getPageStruct()
        ];
        $this->display('Collect/cacheList', $data);
    }

    /**
     * 规则列表
     * @privilege 规则列表|Admin/Collect/ruleList|c6e972e9-2008-11e7-8ad5-9cb3ab404081|2
     */
    public function ruleList()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageSize = 20; #每页条数
        $collectRuleModel = new CollectRuleModel();
        $count = $collectRuleModel->getRuleList('', '', '', true);
        $page = new Page($count, $p, $pageSize);
        $itemList = $collectRuleModel->getRuleList('', $page->getOffset(), $pageSize, false);
        #面包屑导航
        $this->crumb(array(
            '采集管理' => U('Collect/index'),
            '规则' => ''
        ));

        $data = [
            'itemList' => $itemList,
            'pages' => $page->getPageStruct()
        ];
        $this->display('Collect/ruleList', $data);
    }

    /**
     * 添加采集规则
     * @privilege 添加采集规则|Admin/Collect/addRule|c73c806e-2008-11e7-8ad5-9cb3ab404081|2
     * @access public
     * @author furong
     * @return void
     * @since 2017年3月29日 16:36:10
     * @abstract
     */
    public function addRule()
    {
        if (IS_POST) {
            $collectRuleModel = new CollectRuleModel();
            #验证
            $rule = array(
                'rule_name' => 'required',
            );
            $attr = array(
                'rule_name' => '规则名称',
            );
            $validate = $collectRuleModel->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $ruleId = isset($_POST['rule_id']) ? intval($_POST['rule_id']) : 0;
            $ruleName = isset($_POST['rule_name']) ? trim($_POST['rule_name']) : '';
            $ruleDescription = isset($_POST['rule_description']) ? trim($_POST['rule_description']) : '';
            $latestListUrl = isset($_POST['latest_list_url']) ? trim($_POST['latest_list_url']) : '';
            $listUrlFormat = isset($_POST['list_url_format']) ? trim($_POST['list_url_format']) : '';
            $maxPageNum = isset($_POST['max_page_num']) ? intval($_POST['max_page_num']) : 20;
            $listRule = isset($_POST['list_rule']) ? trim($_POST['list_rule']) : '';
            $detailRule = isset($_POST['detail_rule']) ? trim($_POST['detail_rule']) : '';
            $outputEncode = isset($_POST['output_encode']) ? trim($_POST['output_encode']) : 'utf-8';
            $inputEncode = isset($_POST['input_encode']) ? trim($_POST['input_encode']) : 'utf-8';
            $siteUrl = isset($_POST['site_url']) ? trim($_POST['site_url']) : '';
            #组装数据
            $data = [
                'rule_id' => $ruleId,
                'rule_name' => $ruleName,
                'rule_description' => $ruleDescription,
                'latest_list_url' => $latestListUrl,
                'list_url_format' => $listUrlFormat,
                'max_page_num' => $maxPageNum,
                'list_rule' => $listRule,
                'detail_rule' => $detailRule,
                'output_encode' => $outputEncode,
                'input_encode' => $inputEncode,
                'site_url' => $siteUrl,
            ];
            $result = $collectRuleModel->addRule($data);
            if (!$result) {
                $this->ajaxFail('采集规则添加失败');
            } else {
                $this->ajaxSuccess('采集规则添加成功');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $collectRuleModel = new CollectRuleModel();
            $ruleInfo = [
                'rule_id' => '',
                'rule_name' => '',
                'rule_description' => '',
                'latest_list_url' => '',
                'list_url_format' => '',
                'max_page_num' => 20,
                'list_rule' => '',
                'paging_rule' => '',
                'detail_rule' => '',
                'output_encode' => 'utf-8',
                'input_encode' => 'utf-8',
                'site_url' => '',
            ];
            if ($id) {
                $ruleInfo = $collectRuleModel->getRuleInfo($id);
            }
            #面包屑导航
            $this->crumb(array(
                '采集管理' => U('Collect/index'),
                '添加规则' => ''
            ));

            $data = [
                'ruleInfo' => $ruleInfo,
            ];
            $this->display('Collect/addRule', $data);
        }
    }

    public function caijiDetail()
    {
        $id = $_GET['id'];
        $collectItemPoolModel = new CollectItemPoolModel();
        $result = $collectItemPoolModel->getItemInfo($id);
        $rules = [
            'title' => ['.titmain h1', 'text'],
            'description' => ['.titmain .ty', 'text'],
            'content' => ['.titmain .texttit_m1', 'html'],
        ];
        $collectResult = QueryList::Query($result['url'], $rules, '', 'utf-8', 'gb2312')->getData();
        if ($collectResult) {
            $htmlContent = json_encode($collectResult);
            $data = array(
                'id' => $id,
                'collect_status' => 10,
                'html_content' => $htmlContent,
            );
            $collectItemPoolModel->addItem($data);
        }
    }

    public function process()
    {
        $id = $_GET['id'];
        $collectItemPoolModel = new CollectItemPoolModel();
        $cmsArticleModel = new CmsPostModel();
        $result = $collectItemPoolModel->getItemInfo($id);
        $htmlContent = current(json_decode($result['html_content'], true));
        $remoteImageList = $this->getImageFromContent($htmlContent['content']);
        #抓取远程图片
        if ($remoteImageList) {
            $imageMap = '';
            $remoteImageList = $remoteImageList;
            $save_dir = __PROJECT__ . '/upload';
            foreach ($remoteImageList as $key => $value) {
                $fileName = md5($value) . strrchr($value, '.');
                $result = $this->getImage($value, $save_dir, $fileName);
                if ($result['error'] == 0) {
                    $imageMap[] = C('SITE.STATIC_URL') . '/' . $fileName;
                }
            }
            $htmlContent['content'] = str_replace($remoteImageList, $imageMap, $htmlContent['content']);
        }
        $data = array(
            'title' => isset($htmlContent['title']) ? trim($htmlContent['title']) : '',
            'description' => isset($htmlContent['description']) ? trim($htmlContent['description']) : '',
            'content' => isset($htmlContent['content']) ? htmlspecialchars_decode($htmlContent['content']) : '',
        );
        $cmsArticleModel->addPost($data);
    }

    /**
     * 获取分页
     * @access public
     * @author furong
     * @param $listUrlFormat
     * @param $maxPageNum
     * @param $latestListUrl
     * @return array
     * @since 2017年3月29日 17:11:57
     * @abstract
     */
    protected function getPageIng($listUrlFormat, $maxPageNum, $latestListUrl)
    {
        $pageIng = [];
        for ($i = 1; $i <= $maxPageNum; $i++) {
            $pageIng[] = sprintf($listUrlFormat, $i);
        }
        $pageIng[0] = $latestListUrl;
        krsort($pageIng);
        return $pageIng;
    }

    protected function getImageFromContent($content)
    {
        //匹配IMG标签
        $content = htmlspecialchars_decode($content);
        $img_pattern = "/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i";
        preg_match_all($img_pattern, $content, $img_out);
        return $img_out[2];
    }

    /**
     * 下载远程图片
     * @access  protected
     * @author furong
     * @param $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type
     * @return array
     * @since  2017年3月29日 13:26:37
     * @abstract
     */
    protected function getImage($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . rand(0, 10000) . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }

    public function update()
    {
//        if (!IS_POST) {
//            $this->ajaxFail('非法请求');
//        }
        $collectRuleModel = new CollectRuleModel();
//        #验证
//        $rule = array(
//            'rule_id' => 'required|numeric|integer',
//        );
//        $attr = array(
//            'rule_id' => '规则id',
//        );
//        $validate = $collectRuleModel->validate($_POST, $rule, $attr);
//        if (false === $validate->passes()) {
//            $this->ajaxFail($validate->messages()->first());
//        }
//        $ruleId = isset($_POST['rule_id']) ? intval($_POST['rule_id']) : 0;
        $ruleId = $_GET['rule_id'];
        #获取验证规则
        $ruleInfo = $collectRuleModel->getRuleInfo($ruleId);
        if (empty($ruleInfo)) {
            $this->ajaxFail('采集规则不存在');
        }
        $latestListUrl = $ruleInfo['latest_list_url'];
        $listUrlFormat = $ruleInfo['list_url_format'];
        $maxPageNum = $ruleInfo['max_page_num'];
        $listRule = $ruleInfo['list_rule'] ? json_decode($ruleInfo['list_rule'], true) : [];
        $detailRule = $ruleInfo['detail_rule'] ? json_decode($ruleInfo['detail_rule'], true) : [];
        $outputEncode = $ruleInfo['output_encode'];
        $inputEncode = $ruleInfo['input_encode'];
        $siteUrl = $ruleInfo['site_url'];
        $pageList = $this->getPageIng($listUrlFormat, $maxPageNum, $latestListUrl);
        CacheManager::setDefaultConfig('path', __PROJECT__ . '/runtime');
        CacheManager::setDefaultConfig('securityKey', 'cache');
        $cacheInstance = CacheManager::getInstance('Files');
        $collectItemPoolModel = new CollectItemPoolModel();
        foreach ($pageList as $key => $value) {
            $cacheKey = 'caiji_list_' . $key;
            $cachedString = $cacheInstance->getItem($cacheKey);
            $result = '';
//            if (is_null($cachedString->get())) {
//                $url = $value;
//                $rules = $listRule;
//                $result = QueryList::Query($url, $rules, '', 'utf-8')->getData(function ($v) {
//                    $v['id'] = floatval($v['url']);
//                    return $v;
//                });
//                if (empty($result)) {
//                    continue;
//                }
//                $cachedString->set($result)->expiresAfter(300);
//                $cacheInstance->save($cachedString);
//            } else {
//                $result = $cachedString->get();
//            }
            $url = $value;
            $rules = $listRule;
            $result = QueryList::Query($url, $rules, '', $outputEncode, $inputEncode)->getData(function ($value) use ($siteUrl) {
                if (false == strpos($value['url'], 'http')) {
                    $value['url'] = $siteUrl . $value['url'];
                }
                return $value;
            });
            if (empty($result)) {
                continue;
            }
            krsort($result);
            foreach ($result as $value) {
                if (!$value['title'] || !$value['url']) {
                    continue;
                }
                $itemSn = md5($value['title']);
                $data = [
                    'item_sn' => $itemSn,
                    'title' => $value['title'],
                    'url' => $value['url'],
                ];
                $condition = ['where' => ['item_sn', $itemSn]];
                $count = $collectItemPoolModel->getItemList($condition, '', '', true);
                if (!$count) {
                    $collectItemPoolModel->addItem($data);
                }
            }
        }
    }

    public function collectTest()
    {
        if (IS_POST) {
            $testType = $_REQUEST['test_type'];
            if (empty($testType)) {
                $this->ajaxFail('测试类型不能为空');
            }
            $result = '';
            switch ($testType) {
                case 'list':
                    $result = $this->saveListCollectData();
                    break;
                case 'detail':
                    $result = $this->saveDetailCollectData();
                    break;
            }
            if (!$result) {
                $this->ajaxFail('测试类型错误');
            } else {
                $this->ajaxSuccess('保存成功');
            }
        } else {
            $testType = $_REQUEST['test_type'];
            if (empty($testType)) {
                $this->ajaxFail('测试类型不能为空');
            }
            switch ($testType) {
                case 'list':
                    $result = $this->testListCollect();
                    break;
                case 'detail':
                    $result = $this->testDetailCollect();
                    break;
            }
        }
    }

    protected function saveListCollectData()
    {
        $collectRuleModel = new CollectRuleModel();
        #验证
        $rule = array(
            'rule_name' => 'required',
            'site_url' => 'required',
            'latest_list_url' => 'required',
            'list_rule' => 'required',
            'output_encode' => 'required',
            'input_encode' => 'required',
        );
        $attr = array(
            'rule_name' => '规则名称',
            'site_url' => '网站域名',
            'latest_list_url' => '最新列表页链接',
            'list_rule' => '列表页规则',
            'output_encode' => '本站编码',
            'input_encode' => '目标站编码',
        );
        $validate = $collectRuleModel->validate($_POST, $rule, $attr);
        if (false === $validate->passes()) {
            $this->ajaxFail($validate->messages()->first());
        }
        #获取参数
        $ruleName = isset($_POST['rule_name']) ? trim($_POST['rule_name']) : '';
        $latestListUrl = isset($_POST['latest_list_url']) ? trim($_POST['latest_list_url']) : '';
        $listRule = isset($_POST['list_rule']) ? trim($_POST['list_rule']) : '';
        $outputEncode = isset($_POST['output_encode']) ? trim($_POST['output_encode']) : 'utf-8';
        $inputEncode = isset($_POST['input_encode']) ? trim($_POST['input_encode']) : 'utf-8';
        $siteUrl = isset($_POST['site_url']) ? trim($_POST['site_url']) : '';
        #保存
        $data = array(
            'rule_name' => $ruleName,
            'latest_list_url' => $latestListUrl,
            'list_rule' => $listRule,
            'output_encode' => $outputEncode,
            'input_encode' => $inputEncode,
            'site_url' => $siteUrl,
        );
        CacheManager::setDefaultConfig('path', __PROJECT__ . '/runtime');
        CacheManager::setDefaultConfig('securityKey', 'cache');
        $cacheInstance = CacheManager::getInstance('Files');
        $cacheKey = 'collect_list_test';
        $cachedString = $cacheInstance->getItem($cacheKey);
        $cachedString->set($data)->expiresAfter(1800);
        $cacheInstance->save($cachedString);
        return true;
    }


    protected function testListCollect()
    {
        CacheManager::setDefaultConfig('path', __PROJECT__ . '/runtime');
        CacheManager::setDefaultConfig('securityKey', 'cache');
        $cacheInstance = CacheManager::getInstance('Files');
        $cacheKey = 'collect_list_test';
        $cachedString = $cacheInstance->getItem($cacheKey);
        $tempRule = $cachedString->get($cacheKey);

        $latestListUrl = $tempRule['latest_list_url'];
        $listRule = json_decode($tempRule['list_rule'], true);
        $outputEncode = $tempRule['output_encode'];
        $inputEncode = $tempRule['input_encode'];
        $result = QueryList::Query($latestListUrl, $listRule, '', $outputEncode, $inputEncode)->getData();
        echo '<h1>' . $tempRule['rule_name'] . '列表页采集测试</h1>';
        print_g($result);
    }

    protected function saveDetailCollectData()
    {
        $collectRuleModel = new CollectRuleModel();
        #验证
        $rule = array(
            'rule_name' => 'required',
            'latest_list_url' => 'required',
            'list_rule' => 'required',
            'detail_rule' => 'required',
            'output_encode' => 'required',
            'input_encode' => 'required',
        );
        $attr = array(
            'rule_name' => '规则名称',
            'latest_list_url' => '最新列表页链接',
            'list_rule' => '列表页规则',
            'detail_rule' => '详情页规则',
            'output_encode' => '本站编码',
            'input_encode' => '目标站编码',
        );
        $validate = $collectRuleModel->validate($_POST, $rule, $attr);
        if (false === $validate->passes()) {
            $this->ajaxFail($validate->messages()->first());
        }
        CacheManager::setDefaultConfig('path', __PROJECT__ . '/runtime');
        CacheManager::setDefaultConfig('securityKey', 'cache');
        $cacheInstance = CacheManager::getInstance('Files');
        $cacheKey = 'collect_detail_test';
        $cachedString = $cacheInstance->getItem($cacheKey);
        $cachedString->set($_POST)->expiresAfter(1800);
        $cacheInstance->save($cachedString);
        return true;
    }

    protected function testDetailCollect()
    {
        CacheManager::setDefaultConfig('path', __PROJECT__ . '/runtime');
        CacheManager::setDefaultConfig('securityKey', 'cache');
        $cacheInstance = CacheManager::getInstance('Files');
        $cacheKey = 'collect_detail_test';
        $cachedString = $cacheInstance->getItem($cacheKey);
        $ruleData = $cachedString->get($cacheKey);
        $siteUrl = $ruleData['site_url'];
        $latestListUrl = $ruleData['latest_list_url'];
        $listRule = json_decode($ruleData['list_rule'], true);
        $outputEncode = $ruleData['output_encode'];
        $inputEncode = $ruleData['input_encode'];
        $detailRule = json_decode($ruleData['detail_rule'], true);
        $listData = QueryList::Query($latestListUrl, $listRule, '', $outputEncode, $inputEncode)->getData();
        echo '<h1>' . $ruleData['rule_name'] . '列表页采集测试</h1>';
        echo "<pre>";
        print_r($listData[0]);
        if ($listData && $detailRule) {
            echo '<h1>' . $ruleData['rule_name'] . '详情页采集测试</h1>';
            $detailData = QueryList::Query($listData[0]['url'], $detailRule, '', $outputEncode, $inputEncode)->getData();
            print_r($detailData);
        }
    }


}