<?php
return array(
    #静态资源目录
    'STATIC_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/',
    #上传路径
    'UPLOAD_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/upload',
    #上传目录
    'UPLOAD_DIR' => 'upload',
    #上传最大尺寸
    'maxSize' => '5MB',
    #图片尺寸
    'IMAGE_SIZE' => array('_120_75', '_160_100', '_300_185', '_250_155', '_320_200'),
    #运行环境 develop|product
    'ENVIRONMENT' => 'develop',
    'BosonNLP_TOKEN' => 'uOgFY9PY.14201.tQWY5S0DTcNu',

    /*http请求设置 开始*/
    /* URL设置 */
    'URL_MODE' => 2, //url访问模式  0：默认动态url传参模式 1：pathinfo模式 2:兼容模式
    /*http请求设置 结束*/

    /*自定义载入文件*/
    'LOAD_FILES' => [
        __PROJECT__ . '/common/function/function.php'
    ]


);