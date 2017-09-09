<?php $this->layout('Layout/home')?>
<style>
    .page ul{margin-left: 10px;}
    .page ul li{float: left;padding: 0 5px;}
</style>
<div class="maindiv">
    <div class="mainleft" id="mainleft">
        <div class="maintit">
            <h3>赛事掠影</h3></div>
        <div class="maininfo">
            <?php foreach ($latestList as $key => $value): ?>
            <div class='saiphoto'><a href='<?=getImage($value['image_name'])?>' title='<?=$value['title']?>' data-lightbox='saiimg'><img
                        src="<?=getImage($value['image_name'])?>"></a><span><?=$value['title']?></span></div>
            <?php endforeach;?>
            <div class="clear"></div>
        </div>
        <div class="page">
            <?=$pages?>
            <div class="clear"></div>
        </div>
    </div>
    <!--侧边栏开始-->
    <?=$this->insert('Common/mainright')?>
    <!--侧边栏结束-->
</div>