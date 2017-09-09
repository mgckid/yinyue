<div class="maintop" style="background: url("<?=$index_top_lunbo[0]['image_url']?>")"><p id="topbanner"></p></div>
<div class="topmenu">
    <div class="nav" id="menunav">
        <a href="index.html">首 页</a>
        <?php foreach ($navList as $value):?>
        <a href="<?= $value['cate_type'] == 30 ? $value['jump_url'] : U('Index/category', array('cate' => $value['alias'])) ?>"><?= $value['name'] ?></a>
        <?php endforeach;?>
    </div>
</div>
<div class="clear"></div>
<script language="javascript">
    var params = {menu: 'false',quality:'high',wmode:'transparent',allowScriptAccess:'always'};
    var attributes = {id:'top_star',name:'top_star',wmode:'transparent',allowScriptAccess:'always'};
    swfobject.embedSWF("/static/home/images/7.swf", "topbanner", "419", "139", "6.0.65.0", "/static/home/js/expressInstall.swf", params, attributes);
    $(function(){
        var myNav =$("#menunav a"),i;
        for(i=0;i<myNav.length;i++){
            var links = myNav.eq(i).attr("href"),myURL = document.URL;
            if(myURL.indexOf(links) != -1) {
                myNav.eq(i).attr('class','menuhover');
            }
        }
        if(myURL.substr(myURL.length-1,1)=="/")myNav.eq(0).attr('class','menuhover');
        AutoLFHeight();
    })
    //二级页面左右自动高度
    function AutoLFHeight() {
        var lh=$("#mainleft").height();
        var rh=$("#mainright").height();
        if(rh>lh)$("#mainleft").css("height",rh+"px");
    }
</script>