<?php $this->layout('Layout/admin') ?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-8">
            <form name="addRole" action="<?= U('Rbac/addRole') ?>" method="post" class="form form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">角色名</label>

                    <div class="col-sm-10">
                        <input type="text" name="role_name" class="form-control" placeholder="点击输入角色名">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">操作</label>

                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success" data-power="Rbac/addRole">保存</button>
                        <button type="reset" class="btn btn-danger ml10">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!--本页js 开始-->
<script>
    $(function () {
        $('form[name=addRole]').ajaxForm({
            dataType: 'json',
            error: function () {
                layer.msg('服务器无法连接')
            },
            success: function (data) {
                layer.alert(data.msg)
            }
        });
    })
</script>
<!--本页js 结束-->