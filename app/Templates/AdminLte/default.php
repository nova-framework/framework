<?php
/**
 * Frontend Default Layout
 */

use Helpers\Profiler;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title; ?> | <?= SITETITLE; ?></title>
    <?= $meta; // Place to pass data / plugable hook zone ?>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php
    Assets::css(array(
        // Bootstrap 3.3.5
        template_url('bootstrap/css/bootstrap.min.css', 'AdminLte'),
        // Font Awesome
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',
        // Ionicons
        'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        // Theme style
        template_url('dist/css/AdminLTE.min.css', 'AdminLte'),
        // AdminLTE Skins
        template_url('dist/css/skins/_all-skins.min.css', 'AdminLte'),
        // iCheck
        template_url('plugins/iCheck/square/blue.css', 'AdminLte')
    ));

    echo $css; // Place to pass data / plugable hook zone

    //Add Controller specific JS files.
    Assets::js(array(
            template_url('plugins/jQuery/jQuery-2.2.0.min.js', 'AdminLte'),
        )
    );

    ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="<?= site_url(); ?>" class="navbar-brand"><strong><?= SITETITLE; ?></strong></a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if (Auth::check()) { ?>
                <li <?php if($currentUri == 'profile') echo 'class="active"'; ?>>
                    <a href='<?= site_url('profile'); ?>'><i class='fa fa-user'></i> <?= __d('default', 'Profile'); ?></a>
                </li>
                <li>
                    <a href='<?= site_url('logout'); ?>'><i class='fa fa-sign-out'></i> <?= __d('default', 'Logout'); ?></a>
                </li>
                <?php } else { ?>
                <li <?php if($currentUri == 'login') echo 'class="active"'; ?>>
                    <a href='<?= site_url('login'); ?>'><i class='fa fa-sign-out'></i> <?= __d('default', 'User Login'); ?></a>
                </li>
                <li <?php if($currentUri == 'password/remind') echo 'class="active"'; ?>>
                    <a href='<?= site_url('password/remind'); ?>'><i class='fa fa-user'></i> <?= __d('default', 'Forgot Password?'); ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container">
            <!-- Main content -->
            <section class="content">
                <?= $content; ?>
            </section>
        </div>
    </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <?php if(ENVIRONMENT == 'development') { ?>
      <small><?= Profiler::getReport(); ?></small>
      <?php } ?>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.novaframework.com/" target="_blank"><b>Nova Framework</b></a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<?php
Assets::js(array(
    // Bootstrap 3.3.5
    template_url('bootstrap/js/bootstrap.min.js', 'AdminLte'),
    // AdminLTE App
    template_url('dist/js/app.min.js', 'AdminLte'),
    // iCheck
    template_url('plugins/iCheck/icheck.min.js', 'AdminLte'),
));

echo $js; // Place to pass data / plugable hook zone
echo $footer; // Place to pass data / plugable hook zone
?>

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
