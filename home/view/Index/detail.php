<?php $this->layout('Layout/Home');?>
<div class="maindiv">
    <div class="bigcontent">
        <p style="height:30px;line-height:30px;border-bottom:1px solid #CCC;padding-left:10px;margin-bottom:10px">赛事资讯>>正文浏览</p>
        <h2 align="center"><?=$info['title']?></h2>
        <div class="newspub">发布时间：<?=date('Y/m/d',strtotime($info['public_time']))?> 浏览次数：<?=$info['click']?></div>
        <?=htmlspecialchars_decode($info['content'])?>
    </div>
</div>
<!--统计文章阅览数 开始-->
<script src="/static/common/ajaxForm/jquery.form.js"></script>
<form id="ajaxCountView" action="<?=U('Index/ajaxCountView')?>" method="post">
    <input type="hidden" name="id" value="<?=$info['id']?>"/>
</form>
<script>
    $(function(){
        $('#ajaxCountView').ajaxSubmit({
            dataType: 'json',
            error: function () {
                layer.msg('服务器连接错误')
            },
            sunccess:function(data){
                if(data.status==1){
                    $('.countView').html(data.data.click)
                }
            }
        });
    })
</script>
<!--统计文章阅览数 结束-->
