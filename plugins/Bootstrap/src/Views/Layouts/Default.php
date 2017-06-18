<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= isset($pageTitle) ? $pageTitle : $title; ?> - <?= Config::get('app.name'); ?></title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<!-- Local customizations -->
	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/bootstrap-xl-mod.min.css', 'Bootstrap'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/style.css', 'Bootstrap'); ?>">
</head>
<body>

<div class="container">
	<div class="col-sm-12">
		<?php Section::start('page-top'); ?>

		<?= Section::get(); ?>
	</div>
	<div class="col-sm-9">
		<?= Blocks::render('content-top'); ?>

		<?php Section::start('content'); ?>

		<?= Section::get(); ?>

		<?= Blocks::render('content-bottom'); ?>
	</div>
	<div class="col-sm-3">
		<?= Blocks::render('page-right'); ?>
	</div>
	<div class="col-sm-12">
		<?= Blocks::render('page-bottom'); ?>
	</div>
</div>

<footer class="footer">
	<div class="container-fluid">
		<div class="row" style="margin: 15px 0 0;">
			<div class="col-lg-4">
				Nova Framework <strong><?= VERSION; ?></strong> / Kernel <strong><?= App::version(); ?></strong>
			</div>
			<div class="col-lg-8">
				<p class="text-muted pull-right">
					<small><!-- DO NOT DELETE! - Statistics --></small>
				</p>
			</div>
		</div>
	</div>
</footer>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
