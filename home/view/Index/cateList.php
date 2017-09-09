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
    <!--侧边栏开始-->
    <?=$this->insert('Common/mainright')?>
    <!--侧边栏结束-->
</div>