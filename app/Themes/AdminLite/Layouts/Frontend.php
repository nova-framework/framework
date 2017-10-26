<?php
/**
 * Frontend Default Layout
 */

$siteName = Config::get('app.name', SITETITLE);

// Prepare the current User Info.
$user = Auth::user();

// Generate the Language Changer menu.
$langCode = Language::code();
$langName = Language::name();

$languages = Config::get('languages');

if (isset($user->image) && $user->image->exists()) {
    $imageUrl = resource_url('images/users/' .basename($user->image->path));
} else {
    $imageUrl = vendor_url('dist/img/avatar5.png', 'almasaeed2010/adminlte');
}

?>
<!DOCTYPE html>
<html lang="<?= $langCode; ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title; ?> | <?= $siteName; ?></title>
    <?= isset($meta) ? $meta : ''; // Place to pass data ?>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php
    Assets::css(array(
        // Bootstrap 3.3.5
        vendor_url('bower_components/bootstrap/dist/css/bootstrap.min.css', 'almasaeed2010/adminlte'),
        // Bootstrap XL
        theme_url('css/bootstrap-xl-mod.min.css', 'AdminLite'),
        // Font Awesome
        vendor_url('bower_components/font-awesome/css/font-awesome.min.css', 'almasaeed2010/adminlte'),
        // Ionicons
        vendor_url('bower_components/Ionicons/css/ionicons.min.css', 'almasaeed2010/adminlte'),
        // iCheck
        vendor_url('plugins/iCheck/square/blue.css', 'almasaeed2010/adminlte'),
        // Theme style
        vendor_url('dist/css/AdminLTE.css', 'almasaeed2010/adminlte'),
        // AdminLTE Skins
        vendor_url('dist/css/skins/_all-skins.min.css', 'almasaeed2010/adminlte'),
        // Custom CSS
        theme_url('css/style.css', 'AdminLite'),
    ));

    echo isset($css) ? $css : ''; // Place to pass data
?>

<style>
.pagination {
    margin: 0;
}

.pagination > li > a, .pagination > li > span {
  padding: 5px 10px;
}
</style>

<?php
    //Add Controller specific JS files.
    Assets::js(array(
            vendor_url('bower_components/jquery/dist/jquery.min.js', 'almasaeed2010/adminlte'),
            resource_url('js/sprintf.min.js'),
        )
    );

    ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-<?= Config::get('app.color_scheme', 'blue'); ?> layout-top-nav">
<div class="wrapper">
  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a href="<?= site_url(); ?>" class="navbar-brand"><strong><?= __d('admin_lite', 'Private Area'); ?></strong></a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>
        <!-- Navbar Left Menu -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <?php foreach ($navbarLeftItems as $item) { ?>
                    <?= View::partial('Partials/Frontend/NavbarItems', 'AdminLite', array('item' => $item)); ?>
                <?php } ?>
            </ul>
            <!-- Search Form -->
            <?php if (isset($hasNavbarSearch) && ($hasNavbarSearch === true)) { ?>
            <form class="navbar-form navbar-left" role="search" action="<?= site_url('search'); ?>" method="GET">
                <div class="form-group">
                    <input type="text" name="query" class="form-control" id="navbar-search-input" placeholder="<?= __d('admin_lite', 'Search...'); ?>">
                </div>
            </form>
             <?php } ?>
        </div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php foreach ($navbarRightItems as $item) { ?>
                    <?= View::partial('Partials/Frontend/NavbarItems', 'AdminLite', array('item' => $item)); ?>
                <?php } ?>
                <li class="dropdown language-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class='fa fa-language'></i> <?= $langName; ?>
                    </a>
                    <ul class="dropdown-menu">
                    <?php foreach ($languages as $code => $info) { ?>
                        <li <?= ($code == $langCode) ? 'class="active"' : ''; ?>>
                            <a href='<?= site_url('language/' .$code); ?>' title='<?= $info['info']; ?>'><?= $info['name']; ?></a>
                        </li>
                    <?php } ?>
                    </ul>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="<?= $imageUrl ?>" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs"><?= $user->username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                        <img src="<?= $imageUrl ?>" class="img-circle" alt="User Image">

                        <p>
                            <?= $user->realname; ?> - <?= implode(', ', $user->roles->lists('name')); ?>
                            <?php $sinceDate = $user->created_at->formatLocalized(__d('admin_lite', '%d %b %Y, %R')); ?>
                            <small><?= __d('admin_lite', 'Member since {0}', $sinceDate); ?></small>
                        </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= site_url('account'); ?>" class="btn btn-default btn-flat"><?= __d('admin_lite', 'Account'); ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <?= __d('admin_lite', 'Sign out'); ?>
                                </a>
                                <form id="logout-form" action="<?= site_url('logout'); ?>" method="POST" style="display: none;">
                                    <input type="hidden" name="csrfToken" value="<?= csrf_token(); ?>" />
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
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
      <small><!-- DO NOT DELETE! - Statistics --></small>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.novaframework.com/" target="_blank"><b>Nova Framework <?= $version; ?> / Kernel <?= VERSION; ?></b></a> - </strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<?php
Assets::js(array(
    // Bootstrap 3.3.5
    vendor_url('bower_components/bootstrap/dist/js/bootstrap.min.js', 'almasaeed2010/adminlte'),
    // iCheck
    vendor_url('plugins/iCheck/icheck.min.js', 'almasaeed2010/adminlte'),
    // AdminLTE App
    vendor_url('dist/js/adminlte.min.js', 'almasaeed2010/adminlte'),
));

echo isset($js) ? $js : ''; // Place to pass data

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

<script>

$(function () {
    var lastNotificationId = 0;

    handleHeartbeat = function () {
        $.post("<?= site_url('heartbeat'); ?>",
        {
            path: '<?= Request::path(); ?>',
            last_id: lastNotificationId
        })
        .done(function (data) {
            handleMessagesMenu(data.messages);

            handleNotificationsMenu(data.notifications);
        });
    };

    handleMessagesMenu = function (data) {
        var count = data.count;

        if (count === 0) {
            return;
        }

        var menuLabel = $('li.messages-menu > a > span.label');

        menuLabel.html(count);

        menuLabel.show();
    };

    handleNotificationsMenu = function (data) {
        var count = data.count;

        if (count === 0) {
            return;
        }

        // We store the last notification ID.
        else if (data.lastId > 0) {
            lastNotificationId = data.lastId;
        }

        var notificationsHeader = $('#notifications-header');

        var notificationsList = $('#notifications-list');

        // Handle the menu item label.
        var menuLabel = $('li.notifications-menu > a.dropdown-toggle > span.label');

        menuLabel.html(count);

        menuLabel.show();

        // Handle the dropdown header.
        var title = sprintf("<?= __d('system', 'You have %d notification(s)'); ?>", count);

        notificationsHeader.html(title);

        // Handle the notifications list.
        if (data.items.length > 0) {
            var html = parseNotificationItems(data.items);

            notificationsList.prepend(html);
        }
    };

    parseNotificationItems = function (items) {
        return items.map(function (item) {
            var icon  = item.icon  ? item.icon  : 'bell';
            var color = item.color ? item.color : 'aqua';

            return sprintf('<li><a href="%s?read=%s" target="_blank"><i class="fa fa-%s text-%s"></i> %s</s><li>', item.link, item.id, icon, color, item.message);
        });
    }

    // Setup the CSRF header on AJAX requests.
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_token(); ?>'
        }
    });

    // We refresh the notifications every minute.
    setInterval(function() {
        handleHeartbeat();

    }, 10000);

    handleHeartbeat();
});

</script>

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
