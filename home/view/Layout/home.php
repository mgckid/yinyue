<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $siteInfo['title']?></title>
    <meta name="keywords" content="<?= $siteInfo['keyword']?>"/>
    <meta name="description" content="<?= $siteInfo['description']?>"/>
    <link href="/static/home/css/css.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/static/home/css/lightbox.css">
    <script src="/static/home/js/jquery-1.11.1.min.js"></script>
    <script language="javascript" src="/static/home/js/swfobject.js"></script>
    <script src="/static/home/js/lightbox.js"></script>
    <script src="/static/home/js/jQuery-jcMarquee.js" type="text/javascript"></script>
</head>
<body>
<?php $this->insert('Common/header') ?>
<?= $this->section('content'); ?>
<?php $this->insert('Common/footer') ?>
</body>
</html>