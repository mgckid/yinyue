<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$siteInfo['short_site_name']?>网站管理</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/static/admin/adminlte/src/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/static/admin/adminlte/project/plugins/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/static/admin/adminlte/project/plugins/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/static/admin/adminlte/src/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/static/admin/adminlte/src/dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->

    <!-- jQuery 3.1.1 -->
    <script src="/static/admin/adminlte/src/plugins/jQuery/jquery-3.1.1.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="/static/admin/adminlte/src/bootstrap/js/bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="/static/admin/adminlte/src/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="/static/admin/adminlte/src/plugins/fastclick/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="/static/admin/adminlte/src/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="/static/admin/adminlte/src/dist/js/demo.js"></script>

    <!--扩展js引入 开始-->
    <script src="/static/common/layer-v2.1/layer/layer.js"></script>
    <script src="/static/common/ajaxForm/jquery.form.js"></script>
    <!--扩展js引入 结束-->
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<!-- Site wrapper -->
<div class="wrapper">
    <!--头部 开始-->
    <?= $this->insert('Common/main-header') ?>
    <!--头部 结束-->
    <!-- =============================================== -->
    <!--左侧菜单 开始-->
    <?= $this->insert('Common/main-sidebar') ?>
    <!--左侧菜单 结束-->
    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
<!--        <section class="content-header">
            <h1>
                Fixed Layout
                <small>Blank example to the fixed layout</small>
            </h1>

        </section>-->
        <!-- Main content -->
        <section class="content">
            <ol class="breadcrumb">
                <?= $crumbs ?>
            </ol>
<!--            <div class="callout callout-info">
                <h4>Tip!</h4>

                <p>Add the fixed class to the body tag to get this layout. The fixed layout is your best option if your sidebar
                    is bigger than your content because it prevents extra unwanted scrolling.</p>
            </div>-->
            <!-- Default box -->
            <div class="box">
                <?= $this->section('content') ?>
            </div>
            <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- footer start -->
    <?= $this->insert('Common/main-footer') ?>
    <!-- /footer end-->
    <!-- Control Sidebar  start-->
    <?= $this->insert('Common/control-sidebar') ?>
    <!-- Control Sidebar end-->
</div>
<!-- ./wrapper -->

<!--检查权限 开始-->
<?= $this->insert('Common/checkPower') ?>
<!--检查权限 结束-->
<!--页面js 开始-->
<script>
    function logout() {
        $.post('<?= U('Login/logout') ?>', {'logout': true}, function (data) {
            if (data.status == 1) {
                window.location.reload();
            }
        }, 'json')
    }
</script>
<!--页面js 结束-->
</body>
</html>
