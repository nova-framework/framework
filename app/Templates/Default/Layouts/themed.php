<?php
/**
 * Frontend Themed Layout
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

<div class="container">
    <!-- Content Area -->
    <?= $content; ?>
</div>

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
