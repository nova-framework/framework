<?php
/**
 * Demos Layout
 */

use Nova\Helpers\Assets;
use Nova\Helpers\Profiler;
use Nova\Net\Url;

// Calculate the current URL
$segments = Url::segments();

$current_uri = array_shift($segments).'/'.((count($segments) > 0) ? array_shift($segments) : 'index');

$current_url = site_url($current_uri);

?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<?php
	// Add Controller specific data.
    if (is_array($headerMetaData)) {
        foreach($headerMetaData as $str) {
            echo $str;
        }
    }
	?>
	<title><?= $title.' - '.SITE_TITLE; ?></title>

	<!-- CSS -->
	<?php
	Assets::css(array(
		'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
		'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
		site_url('templates/default/assets/css/style.css')
	));

	//Add Controller specific CSS files.
    Assets::css($headerCSSheets);

    Assets::js(array(
        '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
    ));

    //Add Controller specific JS files.
    Assets::js($headerJScripts);
	?>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= $dashboardUrl; ?>"><strong><?= __d('default', 'Nova Framework'); ?></strong></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">

                <?php foreach ($topMenuItems as $parent => $parent_params): ?>
                    <?php if (empty($parent_params['children'])): ?>
                        <?php $active = ($current_url == $parent_params['url']); ?>
                        <li class='<?php if ($active) echo 'active'; ?>'>
                            <a href='<?php echo $parent_params['url']; ?>'>
                                <i class='<?php echo $parent_params['icon']; ?>'></i> <?php echo $parent_params['name']; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <?php
                            $parent_active = false;
                            foreach ($parent_params['children'] as $child) {
                                if($current_url == $child['url']) {
                                    $parent_active = true;
                                    break;
                                }
                            }
                        ?>
                        <li class='dropdown <?php if ($parent_active) echo 'active'; ?>'>
                            <a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class='<?php echo $parent_params['icon']; ?>'></i> <?php echo $parent_params['name']; ?> <span class="caret"></span></i>
                            </a>
                            <ul class='dropdown-menu'>
                            <?php foreach ($parent_params['children'] as $child_params): ?>
                                <?php $child_active = ($current_url == $child_params['url']); ?>
                                <li <?php if ($child_active) echo 'class="active"'; ?>>
                                    <a href='<?php echo $child_params['url']; ?>'>
                                        <i class='<?php echo $child_params['icon']; ?>'></i> <?php echo $child_params['name']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container">
    <!-- Content Area -->
    <?= $content; ?>
</div>

<footer class="footer">
    <div class="container-fluid">
        <div class="row" style="margin: 15px 0 0;">
            <div class="col-lg-4">
                <p class="text-muted">Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.simplemvcframework.com/" target="_blank"><b>Nova Framework</b></a></p>
            </div>
            <div class="col-lg-8">
                <p class="text-muted pull-right">
                    <?php if(ENVIRONMENT == 'development') { ?>
                    <small><?= Profiler::report(); ?></small>
                    <?php } ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- JS -->
<?php
Assets::js(array(
	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
));

//Add Controller specific JS files.
Assets::js($footerJScripts);
?>


</body>
</html>
