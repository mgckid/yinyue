<?php $this->layout('Layout/home'); ?>
<div class="maindiv">
    <div class="mainleft" id="mainleft">
        <div class="maininfo">
            <div class="bigcontent">
                <?=htmlspecialchars_decode($cateInfo['page_content'])?>
            </div>
            <div id="allmap"></div>
        </div>
    </div>
    <!--侧边栏开始-->
    <?=$this->insert('Common/mainright')?>
    <!--侧边栏结束-->
</div>
