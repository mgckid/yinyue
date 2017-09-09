<?php $this->layout('Layout/home')?>
<script>
    $(function(){
        $('#Marquee_x').jcMarquee({ 'marquee':'x','margin_right':'10px','speed':20 });
        // DIVCSS5提示：10px代表间距，第二个20代表滚动速度
    });
</script>
<div class="maindiv">
    <div class="mainleft" id="mainleft">
        <div class="maintit"><h3>赛事简介</h3></div>
        <div class="maininfo">
            <?=msubstr(htmlspecialchars_decode($introduce['page_content']),0,500)?>
        </div>
        <div class="maintit"><h3>报名方式</h3></div>
        <div class="maininfo">
            <?=msubstr(htmlspecialchars_decode($registration['page_content']),0,500)?>
        </div>
        <div class="maintit">
            <h3>获奖选手奖金</h3></div>
        <div class="maininfo">
            <?=msubstr(htmlspecialchars_decode($price['page_content']),0,500)?>
        </div>
        <div class="maintit">
            <h3>赛事掠影</h3><span><a href="<?=U('Index/category',['cate'=>'photo'])?>"><img src="/static/home/images/more01.gif" alt="更多比赛图片" width="45" height="18" /></a></span></div>
        <div id="Marquee_x">
            <ul>
                <?php foreach ($photo as $value): ?>
                    <li>
                        <div>
                            <a href='<?=getImage($value['image_name'])?>' title='<?=$value['title']?>' data-lightbox='saiimg'>
                                <img src="<?=getImage($value['image_name'])?>"/>
                            </a>
                            <span><?=$value['title']?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="maintit"><h3>大赛章程</h3><span><a href="<?=U('Index/category',['cate'=>'rule'])?>"><img src="/static/home/images/more01.gif" alt="大赛章程" width="45" height="18" /></a></span></div>
        <div class="maininfo">
            <?=msubstr(htmlspecialchars_decode($rule['page_content']),0,800)?>
        </div>
    </div>
    <div class="mainright" id="mainright">

        <a href="<?=$adList['index_baomingbiaoxiazai'][0]['ad_link']?>" target="_blank"><img
                src="<?=$adList['index_baomingbiaoxiazai'][0]['image_url']?>" width="277" height="98" class="mainrdown"/>
        </a>

        <div class="mainrtit" style="background:none">
            <h3><img src="/static/home/images/web_icon_026.gif" width="16"   height="16"/>赛事资讯</h3><span><a href="<?=U('Index/category',['cate'=>'news'])?>"><img
                        src="/static/home/images/more01.gif" width="45" height="18"/></a></span></div>
        <ul class="mainrinfo">
            <?php foreach ($news as $value): ?>
            <li><a href='<?= U('Index/detail', array('id' => $value['title_alias'])) ?>' title='<?=$value['title']?>' target=_blank><?=$value['title']?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="mainrtit">
            <h3><img src="/static/home/images/home.gif" width="16" height="16" />组织机构</h3></div>
        <div class="maininfo">
            <?=htmlspecialchars_decode($construct['page_content'])?>
        </div>
<!--        <p align="center" style="margin:8px auto"><a href="info_view.asp?id=68" target="_blank"><img src="/static/home/images/italtno_doc.png" width="265" height="63" /></a></p>-->
        <div class="mainrtit">
            <h3><img src="/static/home/images/email.gif" width="16" height="17" />联系我们</h3></div>
        <div class="maininfo">
            <?=htmlspecialchars_decode($weixin['page_content'])?>
        </div>
    </div>
</div>