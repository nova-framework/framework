<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= isset($title) ? $title : 'Page'; ?> - <?= Config::get('app.name'); ?></title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<!-- Local customizations -->
	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/style.css', 'Backend'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/login.css', 'Backend'); ?>">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#"> <?= __d('backend', 'Control Panel'); ?></a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul class="nav navbar-nav navbar-right" style="margin-right: 0;">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<i class='fa fa-language'></i> <?= Language::name() ?>
					</a>
					<ul class="dropdown-menu">
					<?php foreach (Config::get('languages') as $code => $info) { ?>
						<li <?= ($code == Language::code()) ? 'class="active"' : ''; ?>>
							<a href='<?= site_url('language/' .$code) ?>' title='<?= $info['info'] ?>'>
								<img src="<?= resource_url('plugins/flags/blank.png', 'Backend'); ?>" class="flag flag-<?= $info['flag']; ?>" alt="<?= $info['name']; ?>" />
								<?= $info['name'] ?>
							</a>
						</li>
					<?php } ?>
					</ul>
				</li>
				<!-- Authentication Links -->
				<li <?= ($baseUri == 'auth/login') ? 'class="active"' : ''; ?>>
					<a href="<?= site_url('auth/login'); ?>"><?= __d('backend', 'User Login'); ?></a>
				</li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>

<div class="container">
	<?= $content; ?>
</div>

<footer class="footer">
	<div class="container-fluid">
		<div class="row" style="margin: 15px 0 0;">
			<div class="col-lg-6">
				Copyright &copy; <?= date('Y') ?> <a href="http://www.novaframework.com/" target="_blank"><strong>Nova Framework <?= VERSION; ?> / Kernel <?= App::version(); ?></strong></a> - All rights reserved.
			</div>
			<div class="col-lg-6">
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
