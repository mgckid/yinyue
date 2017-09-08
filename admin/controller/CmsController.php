<?php


namespace app\controller;

use app\model\BaseModel;
use app\model\CmsCategoryModel;
use app\model\CmsPostModel;
use app\model\CmsPageModel;
use app\model\CmsTagModel;
use app\model\CoreTextModel;
use houduanniu\base\BosonNLP;
use houduanniu\base\Page;
use Overtrue\Pinyin\Pinyin;

/**
 * 内容管理控制器
 * @privilege 内容管理|Admin/Cms|e902296d-2006-11e7-8ad5-9cb3ab404081|1
 * @date 2016年5月4日 21:17:23
 * @author Administrator
 */
class CmsController extends UserBaseController
{

    /**
     * 添加栏目
     * @privilege 添加栏目|Admin/Cms/addColumn|e90e8dd1-2006-11e7-8ad5-9cb3ab404081|2
     */
    public function addColumn()
    {
        if (IS_POST) {
            $model = new CmsCategoryModel();
            #验证
            $rule = array(
                'id' => 'integer',
                'pid' => 'required|integer',
                'name' => 'required|alpha',
                'jump_url' => 'required_if:cate_type,30'
            );
            $attr = array(
                'name' => '栏目名称',
                'path' => '栏目路径',
                'sort' => '排序值',
                'id' => '栏目id',
                'jump_url' => '跳转页链接',
            );
            $validate = $model->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $seoName = isset($_POST['alias']) ? trim($_POST['alias']) : '';
            $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 0;
            $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
            $pageContent = isset($_POST['page_content']) ? htmlspecialchars($_POST['page_content']) : '';
            $cateType = isset($_POST['cate_type']) ? intval($_POST['cate_type']) : 10;
            $navDisplay = isset($_POST['nav_display']) ? intval($_POST['nav_display']) : 10;
            $listTemplate = isset($_POST['list_template']) ? trim($_POST['list_template']) : '';
            $detailTemplate = isset($_POST['detail_template']) ? trim($_POST['detail_template']) : '';
            $jumpUrl = isset($_POST['jump_url']) ? trim($_POST['jump_url']) : '';

            if (!empty($id) && $id == $pid) {
                $this->ajaxFail('请选择其他栏目作为父栏目');
            }

            $pinyin = new Pinyin();
            $data = array(
                'id' => $id,
                'name' => $name,
                'alias' => empty($seoName) ? $pinyin->permalink($name, '') : $seoName,
                'path' => 0,
                'pid' => $pid,
                'sort' => $sort,
                'keyword' => $keyword,
                'description' => $description,
                'page_content' => $pageContent,
                'cate_type' => $cateType,
                'nav_display' => $navDisplay,
                'list_template' => $listTemplate,
                'detail_template' => $detailTemplate,
                'jump_url'=>$jumpUrl,
            );
            $result = $model->addColumn($data);
            if ($result) {
                $this->ajaxSuccess('栏目添加成功');
            } else {
                $this->ajaxFail('栏目添加失败');
            }
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $model = new CmsCategoryModel();
            $list = $model->orm()
                ->select(array('id', 'name', 'pid'))
                ->find_array();
            if (!$list) {
                // $model->initColumn();
            }
            $info = array(
                'id' => $id,
                'name' => '',
                'pid' => 0,
                'alias' => '',
                'sort' => 100,
                'path' => '',
                'keyword' => '',
                'description' => '',
                'page_content' => '',
                'cate_type' => '10',
                'nav_display' => '10',
                'list_template' => '',
                'detail_template' => '',
                'jump_url'=>'',
            );
            if ($id) {
                $info = $model->orm()
                    ->find_one($id)
                    ->as_array();
            }
            $data = array(
                'list' => $model::unlimitedForLevel($list, '|__'),
                'info' => $info
            );
            #面包屑导航
            $this->crumb(array(
                '内容管理' => U('Cms/index'),
                '添加栏目' => ''
            ));
            $this->display('Cms/addColumn', $data);
        }
    }

    /**
     * 栏目列表
     * @privilege 栏目列表|Admin/Cms/index|e91d2442-2006-11e7-8ad5-9cb3ab404081|2
     */
    public function index()
    {
        $model = new CmsCategoryModel();
        $field = 'id,name,pid,sort';
        $list = $model->getColumnList([], $field);
        foreach ($list as $k => $v) {
            $articleCount = $model->orm('cms_post')
                ->where(array('column_id' => $list[$k]['id']))
                ->count();
            $v['article_count'] = $articleCount;
            $list[$k] = $v;
        }
        $data = array(
            'list' => $model::unlimitedForLevel($list)
        );
        #面包屑导航
        $this->crumb(array(
            '内容管理' => U('Cms/index'),
            '栏目管理' => ''
        ));
        $this->display('Cms/index', $data);
    }

    /**
     * 删除目录
     * @privilege 删除目录|Admin/Cms/delColumn|e92a1a4e-2006-11e7-8ad5-9cb3ab404081|3
     */
    public function delColumn()
    {
        $model = new CmsCategoryModel();
        #验证
        $rule = array(
            'id' => 'required',
        );
        $attr = array(
            'id' => '栏目ID',
        );
        $validate = $model->validate($_POST, $rule, $attr);
        if (false === $validate->passes()) {
            $this->ajaxFail($validate->messages()->first());
        }
        #获取参数
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$model->deleteColumn($id)) {
            $this->ajaxFail($this->getMessage());
        } else {
            $this->ajaxSuccess('删除成功');
        }
    }

    /**
     * 文章列表
     * @privilege 文章列表|Admin/Cms/articleList|c68ffb0f-2008-11e7-8ad5-9cb3ab404081|2
     */
    public function articleList()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0; #栏目id
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1; #当前页
        $fetchRow = 20; #每页条数
        $model = new CmsPostModel();
        $columnModel = new CmsCategoryModel();
        $condition = [];
        if ($id) {
            $condition['where'] = array('a.column_id', $id);
        }
        #统计记录数
        $count = $model->getArticleList($condition, '', '', TRUE);
        #分页
        $page = new Page($count, $p, $fetchRow);
        #文章记录
        $field = 'a.id,a.title,a.description,a.keyword,a.editor,a.image_name,a.column_id,' .
            'a.click,a.is_publish,a.created,a.modified,a.is_recommend,a.is_top,a.is_image,c.name as column_name,a.public_time';
        $list = $model->getArticleList($condition, $page->getOffset(), $fetchRow, FALSE, $field);
        foreach ($list as $k => $v) {
            $v['is_recommend_text'] = $v['is_recommend'] == 10 ? '是' : '否';
            $v['is_top_text'] = $v['is_top'] == 10 ? '是' : '否';
            $v['is_image_text'] = $v['is_image'] == 10 ? '是' : '否';
            $list[$k] = $v;
        }
        $data = array(
            'list' => $list,
            'page' => $page->getPageStruct(),
        );
        #面包屑导航
        $this->crumb(array(
            '内容管理' => U('Cms/index'),
            '文章管理' => ''
        ));
        $this->display('Cms/articleList', $data);
    }

    protected function getImageFromContent($content)
    {
        //匹配IMG标签
        $content = htmlspecialchars_decode($content);
        $img_pattern = "/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i";
        preg_match_all($img_pattern, $content, $img_out);
        return $img_out[2];
    }

    protected function getImageUrlFromUrl($url)
    {
        $_url = explode('/', $url);
        return end($_url);
    }

    /**
     * 添加文章
     * @privilege 添加文章|Admin/Cms/addArticle|c69c73ec-2008-11e7-8ad5-9cb3ab404081|3
     */
    public function addArticle()
    {
        if (IS_POST) {
            $model = new CmsPostModel();
            #验证
            $rule = array(
                'title' => 'required',
                'column_id' => 'required|integer',
            );
            $attr = array(
                'title' => '标题名称',
                'column_id' => '栏目ID',
            );
            $validate = $model->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $editor = isset($_POST['editor']) ? trim($_POST['editor']) : '';
            $imageUrl = isset($_POST['image_name']) ? trim($_POST['image_name']) : '';
            $columnId = isset($_POST['column_id']) ? intval($_POST['column_id']) : 0;
            $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $content = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '';
            $isRecommend = isset($_POST['is_recommend']) ? intval($_POST['is_recommend']) : 0;
            $isTop = isset($_POST['is_top']) ? intval($_POST['is_top']) : 0;
            $publicTime = (isset($_POST['public_time']) && strtotime($_POST['public_time']) > 0) ? date('Y-m-d H:i:s', strtotime($_POST['public_time'])) : date('Y-m-d H:i:s', time());
            $tagName = isset($_POST['tag_name']) && !empty($_POST['tag_name']) ? explode(',', trim($_POST['tag_name'])) : [];
            $titleAlias = isset($_POST['title_alias']) ? htmlspecialchars(trim($_POST['title_alias'])) : '';
            #缩略图为空 取文章图片为缩略图
            if (!$imageUrl) {
                $img = $this->getImageFromContent($content);
                if ($img) {
                    $imageUrl = $this->getImageUrlFromUrl(current($img));
                }
            }
            $isImage = $imageUrl ? 10 : 0;
            if(!$titleAlias){
                $pinyin = new Pinyin();
                $titleAlias = htmlspecialchars(join('-',$pinyin->convert($title)));
            }
            $model->beginTransaction();
            try {
                #创建文章主表记录
                $data = array(
                    'id' => $id,
                    'title' => $title,
                    'editor' => $editor,
                    'image_name' => $imageUrl,
                    'column_id' => $columnId,
                    'content' => $content,
                    'public_time' => $publicTime,
                    'keyword' => $keyword,
                    'description' => $description,
                    'is_top' => $isTop,
                    'is_recommend' => $isRecommend,
                    'is_image' => $isImage,
                    'title_alias'=>$titleAlias
                );
                $postId = $model->addPost($data);
                if (!$postId) {
                    throw new \Exception('文章内容添加失败');
                }
                #文章标签操作
                {
                    $cmsTagModel = new CmsTagModel();
                    $addTagIdIn = [];
                    #添加标签
                    if (!empty($tagName)) {
                        foreach ($tagName as $val) {
                            $tagId = $cmsTagModel->addTag(['tag_name' => $val]);
                            $addTagIdIn[] = $tagId;
                            if (!$tagId) {
                                throw new \Exception('添加文章标签失败');
                            }
                            if (!$cmsTagModel->addTagPost($tagId, $postId)) {
                                throw new \Exception('添加文章标签关联失败');
                            }
                        }
                    }
                    #删除文章标签关联
                    if (!$cmsTagModel->delTagPost($postId, $addTagIdIn)) {
                        throw new \Exception('删除文章标签关联失败');
                    }
                }

                $model->commit();
            } catch (\Exception $ex) {
                $model->rollBack();
                $this->ajaxFail($ex->getMessage());
            }
            $this->ajaxSuccess('文章添加成功');
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $columnId = isset($_GET['column_id']) ? intval($_GET['column_id']) : 0;
            $info = array(
                'id' => $id,
                'title' => '',
                'description' => '',
                'keyword' => '',
                'public_time' => '',
                'editor' => $this->getInfo('loginInfo')['true_name'],
                'created' => '',
                'image_name' => '',
                'column_id' => $columnId,
                'click' => '',
                'is_publish' => '',
                'content' => '',
                'thumb' => '',
                'is_top' => 0,
                'is_recommend' => 0,
                'is_image' => 0,
                'title_alias'=>''
            );
            if ($id) {
                $articleModel = new CmsPostModel();
                $info = $articleModel->getArticleInfoById($id);
                $info['thumb'] = $info['image_name'] ? getImage($info['image_name']) : '';
            }
            #栏目列表
            $columnModel = new CmsCategoryModel();
            $list = $columnModel->getColumnList(['where' => ['cate_type', $columnModel::CATE_TYPE_LIST]], 'id,pid,name');
            $data = array(
                'list' => $columnModel::unlimitedForLevel($list, '|__'),
                'info' => $info,
            );
            #获取tag列表
            {
                $CmsTagModel = new CmsTagModel();
                $result = $CmsTagModel->getTagList(0, 999, false, 'tag_id,tag_name');
                $data['tag_list'] = $result;
                $postTag = $CmsTagModel->getTagByPostId($id);
//                $postTag=array_column($postTag,'tag_name');
                $data['post_tag'] = $postTag;
            }
            #面包屑导航
            $this->crumb(array(
                '内容管理' => U('Cms/index'),
                '添加文章' => ''
            ));
            $this->display('Cms/addArticle', $data);
        }
    }

    /**
     * 删除文章
     * @privilege 删除文章|Admin/Cms/delArticle|c6a7aa7b-2008-11e7-8ad5-9cb3ab404081|3
     */
    public function delArticle()
    {
        if (IS_POST) {
            $model = new BaseModel();
            #验证
            $rule = array(
                'id' => 'required|array',
            );
            $attr = array(
                'id' => '文章ID',
            );
            $validate = $model->validate($_POST, $rule, $attr);
            if (false === $validate->passes()) {
                $this->ajaxFail($validate->messages()->first());
            }
            #获取参数
            $id = $_POST['id'];

            $success = 0;
            foreach ($id as $v) {
                if ($this->delArticles($v)) {
                    $success++;
                }
            }
            if ($success != count($id)) {
                $this->ajaxFail($this->getMessage());
            }
            $this->ajaxSuccess('删除成功');
        }
    }

    protected function delArticles($id)
    {
        $model = new CmsPostModel();
        $article = $model->orm()->find_one($id);
        if (!$article->delete()) {
            return false;
        }
        return TRUE;
    }


    /**
     * 添加标签
     * @privilege 添加标签|Admin/Cms/addTag|c6b424e9-2008-11e7-8ad5-9cb3ab404081|3
     */
    public function addTag()
    {
        if (IS_POST) {
            $rules = array(
                'tag_name' => 'required',
                'tag_description' => 'required',
            );
            $attr = array(
                'tag_name' => '标签名称',
                'tag_description' => '标签描述'
            );
            $model = new CmsTagModel();
            $validator = $model->validate($_POST, $rules, $attr);
            if (!$validator->passes()) {
                $this->ajaxFail($validator->messages()->first());
            }
            $tagId = isset($_POST['tag_id']) ? intval($_POST['tag_id']) : 0;
            $tagName = isset($_POST['tag_name']) ? trim($_POST['tag_name']) : '';
            $tagDescription = isset($_POST['tag_description']) ? trim($_POST['tag_description']) : '';
            $tagSort = isset($_POST['tag_sort']) ? intval($_POST['tag_sort']) : 100;
            $data = array(
                'tag_id' => $tagId,
                'tag_name' => $tagName,
                'tag_description' => $tagDescription,
                'tag_sort' => $tagSort,
            );
            $result = $model->addTag($data);
            if (!$result) {
                $this->ajaxFail('标签添加失败,' . $this->getMessage());
            } else {
                $this->ajaxSuccess('标签添加成功');
            }
        } else {
            $tagId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $model = new CmsTagModel();
            $data = array(
                'tag_id' => $tagId,
                'tag_name' => '',
                'tag_description' => '',
                'tag_sort' => 100
            );
            if (!empty($tagId)) {
                $result = $model->orm()
                    ->find_one($tagId);
                if ($result) {
                    $data = $result->as_array();
                }
            }
            #面包屑导航
            $this->crumb(array(
                '内容管理' => U('Cms/index'),
                '添加标签' => ''
            ));
            $info = array(
                'data' => $data
            );
            $this->display('Cms/addTag', $info);
        }
    }

    /**
     * 标签列表
     * @privilege 标签列表|Admin/Cms/tag|c6c0d2cc-2008-11e7-8ad5-9cb3ab404081|2
     */
    public function tag()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $pageRow = 20;
        $model = new CmsTagModel();
        $count = $model->getTagList('', '', true);
        $page = new Page($count, $p, $pageRow);
        $result = $model->getTagList($page->getOffset(), $pageRow);
        #面包屑导航
        $this->crumb(array(
            '内容管理' => U('Cms/index'),
            '标签管理' => ''
        ));
        $data = array(
            'list' => $result,
            'pages' => $page->getPageStruct(),
        );
        $this->display('Cms/tag', $data);
    }

    /**
     * 文章分词
     * @privilege 文章分词|Admin/Cms/ajaxFenci|c6ce19bb-2008-11e7-8ad5-9cb3ab404081|3
     */
    public function ajaxFenci()
    {
        if (!IS_POST) {
            $this->ajaxFail('非法请求');
        }
        $text = isset($_POST['content']) ? trim(strip_tags($_POST['content'])) : '';
        if (empty($text)) {
            $this->ajaxFail('源数据不能为空');
        }
        $fenci = new BosonNLP(C('BosonNLP_TOKEN'));
        $tagModel = new CmsTagModel();
        //提取关键字
        $pram = [
            'top_k' => 10,
        ];
        $result = $fenci->analysis($fenci::ACTION_KEYWORDS, $text, $pram);
        if (!$result) {
            $this->ajaxFail('分词失败');
        }
        $keyword = [];
        foreach ($result[0] as $key => $val) {
            $keyword[] = $val[1];
        }
        //提取描述
        $data = [
            'content' => $text,
            'not_exceed' => 0,
            'percentage' => 0.1,
        ];
        $result = $fenci->analysis($fenci::ACTION_SUMMARY, $data);
        $summary = !empty($result) ? str_replace(PHP_EOL, "", $result) : '';
        $return = [
            'keyword' => join(',', $keyword),
            'tag' => join(',', array_slice($keyword, 0, 5)),
            'description' => $summary,
        ];
        $this->ajaxSuccess('获取成功', $return);
    }

}
