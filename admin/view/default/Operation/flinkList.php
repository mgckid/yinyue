<?php $this->layout('Layout/admin'); ?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="operate_box">
            <a class="btn btn-sm btn-success" data-power="Operation/addFlink" href="<?= U('Operation/addFlink') ?>">添加友情链接</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>id</th>
                    <th>站点名称</th>
                    <th>友情链接</th>
                    <th>创建时间</th>
                    <th>修改时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $v) { ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><?= $v['fname'] ?></td>
                        <td><?= $v['furl'] ?></td>
                        <td><?= $v['created'] ?></td>
                        <td><?= $v['modified'] ?></td>
                        <td>
                            <button name="delFlink" id="<?= $v['id'] ?>" data-power="Operation/delFlink" class="btn btn-xs btn-danger">删除</button>
    <!--                            <a href="<?= U('Operation/delFlink') ?>" data-power="Operation/delFlink" class="btn btn-xs btn-danger">删除</a>-->
                            <a href="<?= U('Operation/addFlink', array('id' => $v['id'])) ?>" data-power="Operation/addFlink" class="btn btn-xs btn-success">编辑</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!--/列表-->
        <?= $page ?>
        <!--/分页-->
    </div>
</div>
<!--删除记录 开始-->
<form name="delFlinkForm" action="<?= U('Operation/delFlink') ?>" method="post">
    <input type="hidden" value="" name="id" />
</form>
<script>
    $('[name=delFlink]').on('click', function () {
        var id = $(this).attr('id');
        layer.confirm('确定要删除么?', function (index) {
            doDel(id);
            layer.close(index);
        });
        function doDel(id) {
            $('[name=delFlinkForm] [name=id]').val(id);
            $('form[name="delFlinkForm"]').ajaxSubmit({
                dataType: 'json',
                success: function (data) {
                    layer.alert(data.msg)
                    if (data.status == 1) {
                        window.location.reload();
                    }
                },
                error: function () {
                    layer.alert('服务器访问出错');
                }
            });
        }
    });
</script>
<!--删除记录 结束-->