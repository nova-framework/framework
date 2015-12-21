<?php
/**
 * Frontend Default Layout
 */

use Nova\Helpers\Assets;

?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<?php
	// Add Controller specific data.
    foreach($pageMetaData as $str) {
        echo $str;
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
    Assets::css($headerCSS);

    Assets::js(array(
        '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
    ));

    //Add Controller specific JS files.
    Assets::js($headerJScript);
	?>
</head>
<body>
<?php
// Add Controller specific data.
foreach($afterBodyArea as $str) {
    echo $str;
}
?>

<div class="container">
    <!-- Content Area -->
    <?= $content; ?>
</div>

<!-- JS -->
<?php
// Add Controller specific data.
foreach($footerArea as $str) {
    echo $str;
}

Assets::js(array(
	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
));

//Add Controller specific JS files.
Assets::js($footerJScript);
?>

</body>
</html>
