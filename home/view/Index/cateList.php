<?php $this->layout('Layout/home')?>
<style>
    .page ul{margin-left: 10px;}
    .page ul li{float: left;padding: 0 5px;}
</style>
<div class="maindiv">
    <div class="mainleft" id="mainleft">
        <div class="maintit">
            <h3><?=$cateInfo['name']?> </h3></div>
        <div class="maininfo">
            <ul class='newslist'>
                <?php foreach ($latestList as $key => $value): ?>
                <li>
                    <span><?= date('Y/m/d', strtotime($value['public_time'])) ?></span>
                    <a href="<?= U('Index/detail', array('id' => $value['title_alias'])) ?>" title='<?= $value['title'] ?>' target=_blank ><?= $value['title'] ?></a>
                </li>
                <?php endforeach;?>
            </ul>
            <div class="page">
                <?=$pages?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="mainright" id="mainright"> <a href="/static/home/images/20160413161310411.doc" target="_blank"><img src="/static/home/images/downfile.gif" width="277" height="98" class="mainrdown" /></a>
        <div class="mainrtit">
            <h3><img src="/static/home/images/email.gif" width="16" height="17" />联系我们</h3></div>
        <div class="maininfo">
            电话：151-5887-4292 唐老师 <br />
            E-Mail：tangchaow@foxmail.com<br />
            新浪微博：wb<br />
            官方微信：wx<br><img src='/static/home/images/20150119154937349.png'>
        </div>
    </div>
</div>