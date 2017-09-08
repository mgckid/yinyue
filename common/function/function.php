<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2016/10/8
 * Time: 14:14
 */

/**
 * 获取图片链接
 * @param type $name 图片名
 * @param type $mode 模式
 * @param type $size 缩略图尺寸
 * @return string 图片链接
 */
//function getImage($name, $mode = 'cms', $size = '_600_400')
//{
//    $uploadPath = getFilePath($mode);
//    $_size = explode('_', substr($size, 1));
//    $handName = function ($name, $size) {
//        $names = explode('.', $name);
//        $ext = array_pop($names);
//        $newName = array_pop($names);
//        return implode('.', array($newName . $size, $ext));
//    };
//    $_image = $handName($name, $size);
//    $_imagePath = $uploadPath . $_image;
//    if (is_file($uploadPath . $name)) {
//        if (!is_file($_imagePath)) {
//            $imageManage = new \Intervention\Image\ImageManager(array('driver' => 'gd'));
//            $imageManage->make($uploadPath . $name)
//                ->resize($_size[0], $_size[1])
//                ->save($uploadPath . $_image);
//        }
//    } else {
//        $imageUrl = '/' . 'public/common/images/default' . $size . '.jpg';
//        return $imageUrl;
//    }
//    $imageUrl = '';
//    switch ($mode) {
//        case 'cms':
//            $imageUrl = '/' . C('UPLOAD_DIR') . '/cms/' . $_image;
//            break;
//    }
//    return $imageUrl;
//}

/**
 * 获取图片
 *
 * @param $name
 * @return string
 */
function getImage($name, $size = '')
{
    if (empty($name)) {
        return '/static/Common/images/no_image.jpg';
    }
    $uploadPath = C('UPLOAD_PATH');
    $staticPath = C('STATIC_PATH');
    $staticUrl = '/';
    $imagePath = $uploadPath . '/' . $name;
    if (!empty($size)) {
        $handName = function ($name, $size) {
            $names = explode('.', $name);
            $ext = array_pop($names);
            $newName = array_pop($names);
            return implode('.', array($newName . $size, $ext));
        };
        $imageName = $handName($name, $size);
        $thumbPath = $uploadPath . '/' . $imageName;
        if (file_exists($imagePath) && !file_exists($thumbPath)) {
            $_size = explode('_', substr($size, 1));
            $imageManage = new \Intervention\Image\ImageManager(array('driver' => 'gd'));
            $imageManage->make($imagePath)
                ->resize($_size[0], $_size[1])
                ->save($thumbPath);
        }
        $imagePath = $thumbPath;
    }
    $_image = str_replace($staticPath, $staticUrl, $imagePath);
    return $_image;
}

/**
 * 获取上传存储路径
 *
 * @param string $mode
 * @return string|type
 */
function getFilePath($mode = '')
{
    $uploadPath = C('UPLOAD_PATH');
    switch ($mode) {
        case 'cms':
            $uploadPath .= '/cms';
            break;
        case 'ad':
            $uploadPath .= '/ad';
            break;
        default:
            $uploadPath .= '';
    }
    return $uploadPath;
}

/**
 * 无线分类 一层结构
 * @param $cate
 * @param int $pid
 * @param int $layer
 * @param string $placeHolder
 * @param string $placeHolderBegin
 * @return array
 */
function treeStructForLevel($cate, $pid = 0, $layer = 0, $placeHolder = '--', $placeHolderBegin = '|')
{
    static $data = [];
    foreach ($cate as $value) {
        if ($value['pid'] == $pid) {
            $placeHolderBegin = $layer == 0 ? '' : $placeHolderBegin;
            $value['placeHolder'] = $placeHolderBegin . str_repeat($placeHolder, $layer);
            $data[] = $value;
            treeStructForLevel($cate, $value['id'], $layer + 1);
        }
    }
    return $data;
}


function treeStructForLayer($cate, $pid = 0)
{
    $data = [];
    foreach ($cate as $value) {
        if ($value['pid'] == $pid) {
            $value['sub'] = treeStructForLayer($cate, $value['id']);
            $data[] = $value;
        }
    }
    return $data;
}

function getParents($cate, $id)
{
    static $data = [];
    foreach ($cate as $value) {
        if ($value['id'] == $id) {
            $data[] = $value;
            getParents($cate, $value['pid']);
        }
    }
    $sort = array_column($data, 'id');
    array_multisort($sort, $data, SORT_ASC);
    return $data;
}

function getChilden($cate, $id)
{
    static $data = [];
    foreach ($cate as $value) {
        if ($value['pid'] == $id) {
            $data[] = $value;
            getChilden($cate, $value['id']);
        }
    }
    return $data;
}


