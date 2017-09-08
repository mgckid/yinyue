<?php $this->layout('Layout/admin'); ?>
<!--/breadcrumb-->
<div class="panel panel-default">


    <div class="panel-body">
        <div class="operate_box">
            <a class="btn btn-sm btn-success" data-power="Rbac/addRole" href="<?= U('Rbac/addRole') ?>">添加角色</a>
        </div>
        <!--/operate_box-->
        <table class="table">
            <thead>
            <tr>
                <th>id</th>
                <th>角色名</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $v) { ?>
                <tr>
                    <td><?= $v['role_id'] ?></td>
                    <td><?= $v['role_name'] ?></td>
                    <td><?= $v['created'] ?></td>
                    <td>
                        <a href="<?= U('Rbac/delRole') ?>" data-power="Rbac/delRole"  class="btn btn-xs btn-danger">删除</a>
                        <a href="<?= U('Rbac/addRoleAccess', array('id' => $v['role_id'])) ?>" data-power="Rbac/addRoleAccess" class="btn btn-xs btn-success">分配权限</a>
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
