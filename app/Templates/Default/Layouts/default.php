<?php
/**
 * Frontend Default Layout
 */

use Smvc\Helpers\Assets;
use Smvc\Helpers\Hooks;

// Initialise hooks
$hooks = Hooks::get();
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<?php
	//hook for plugging in meta tags
	$hooks->run('meta');
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

	// Hook for plugging in css
	$hooks->run('css');

    Assets::js(array(
        '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
    ));
	?>
</head>
<body>
<?php
// Hook for running code after body tag
$hooks->run('afterBody');
?>

<div class="container">
    <div class="row">
        <!-- Content Area -->
        <?= $content; ?>
    </div>
</div>

<!-- JS -->
<?php
Assets::js(array(
	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'
));

// Hook for plugging in javascript
$hooks->run('js');

// Hook for plugging in code into the footer
$hooks->run('footer');
?>

</body>
</html>
