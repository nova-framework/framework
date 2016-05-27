<?php $targetUrl = site_url('password/reset/' .$token); // Calculate the target URL. ?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_CODE; ?>">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2><?= __('Password Reset'); ?></h2>

		<div>
			<?= __('To reset your password, complete this form: {0}.', $targetUrl); ?>
		</div>
	</body>
</html>
