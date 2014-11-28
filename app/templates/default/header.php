<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>

	<!-- Site meta -->
	<meta charset="utf-8">
	<title><?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in app/core/config.php ?></title>

	<!-- CSS -->
	<?php
		helpers\assets::css(array(
			'//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
			helpers\url::template_path() . 'css/style.css',
		))
	?>

</head>
<body>

<div class="container">
