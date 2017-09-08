<?php $this->layout('Layout/admin'); ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-8">
            <form class="form form-horizontal" name="add" action="<?= U('Operation/addNav') ?>"   method="post">


                <div class="form-group">
                    <label class="col-sm-2 control-label">所属菜单</label>

                    <div class="col-sm-10">
                        <select name="path" class="form-control">
                            <option value="">请选择栏目</option>
                            <?php foreach ($list as $v) { ?>
                                <option value="<?= $v['path'] ?>" > <?= $v['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">菜单名称</label>

                    <div class="col-sm-10">
                        <input type="text" name="name"    class="form-control" placeholder="点击输入菜单名称">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">菜单url</label>

                    <div class="col-sm-10">
                        <input type="text" name="url"  class="form-control"
                               placeholder="点击输入菜单url">
                    </div>
                </div>
<!--                <div class="form-group">-->
<!--                    <label class="col-sm-2 control-label">排列顺序</label>-->
<!---->
<!--                    <div class="col-sm-10">-->
<!--                        <input type="text" name="sort" class="form-control"   placeholder="点击输入排列顺序">-->
<!--                    </div>-->
<!--                </div>-->

                <div class="form-group">
                    <label class="col-sm-2 control-label">操作</label>

                    <div class="col-sm-10">
                        <button class="btn btn-success " data-power="Operation/addNav">保存</button>
                        <button type="reset" class="btn btn-danger ml10">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/panel-->

<script type="text/javascript">
    $('form[name=add]').ajaxForm({
        dataType: 'json',
        error: function () {
            layer.msg('服务器连接失败');
        },
        success: function (data) {
            if (data.status == 1) {
                $('form').find('input:reset').click();
            }
            layer.alert(data.msg)
        }
    });
</script>