<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= isset($title) ? $title : 'Page'; ?> - <?= Config::get('app.name'); ?></title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<!-- DataTables -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css">

	<!-- Local customizations -->
	<link rel="stylesheet" type="text/css" href="<?= resource_url('plugins/flags/flags.css', 'Backend'); ?>">

	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/backend.css', 'Backend'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= resource_url('css/style.css', 'Backend'); ?>">

	<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>

<div id="wrapper">
	<div class="container-fluid">
		<!-- Navigation -->
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand sidebar-toggle" href="#menu-toggle" id="menu-toggle">
					<i class='fa fa-bars'></i>
				</a>
				<a class="navbar-brand" style="margin-top: 1px;" href="<?= site_url('admin/dashboard'); ?>"> <strong><?= __d('backend', 'Control Panel'); ?></strong></a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li <?php if($baseUri == 'admin/messages') echo 'class="active"'; ?>>
						<a href="<?= site_url('admin/messages'); ?>" title="<?= __d('backend', 'Your Messages'); ?>">
							<i class='fa fa-envelope'></i>
							<?= __d('backend', 'Messages'); ?> <?php if (isset($privateMessageCount) && ($privateMessageCount > 0)) echo '<span class="label label-success">' .$privateMessageCount .'</span>'; ?>
						</a>
					</li>
					<li <?php if($baseUri == 'admin/notifications') echo 'class="active"'; ?>>
						<a href="<?= site_url('admin/notifications'); ?>" title="<?= __d('backend', 'Your Notifications'); ?>">
							<i class='fa fa-bell'></i>
							<?= __d('backend', 'Notifications'); ?> <?php if (isset($notificationCount) && ($notificationCount > 0)) echo '<span class="label label-success">' .$notificationCount .'</span>'; ?>
						</a>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="<?= Language::name() .' (' .Language::code() .')'; ?>">
							<i class='fa fa-language'></i> <?= Language::name(); ?> <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php foreach (Config::get('languages') as $code => $info) { ?>
							<li <?= ($code == Language::code()) ? 'class="active"' : ''; ?>>
								<a href='<?= site_url('language/' .$code) ?>' title='<?= $info['info'] ?>'>
									<img src="<?= resource_url('plugins/flags/blank.png', 'Backend'); ?>" class="flag flag-<?= $info['flag']; ?>" alt="<?= $info['name']; ?>" />
									<?= $info['name']; ?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</li>
					<!-- Authentication Links -->
					<li class="dropdown <?= ($baseUri == 'admin/profile') ? 'active' : ''; ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="<?= $currentUser->fullName() ?>">
							<img src="<?= $currentUser->picture(); ?>" class="user-image" alt="User Image"> <?= $currentUser->username ?> <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li <?= ($baseUri == 'admin/profile') ? 'class="active"' : ''; ?>>
								<a href="<?= site_url('admin/profile'); ?>"><i class='fa fa-circle-o'></i> <?= __d('backend', 'Profile'); ?></a>
							</li>
							<li role="separator" class="divider"></li>
							<li>
								<a href="<?= site_url('auth/logout'); ?>"
									onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
									<i class='fa fa-sign-out'></i> <?= __d('backend', 'Logout'); ?>
								</a>
								<form id="logout-form" action="<?= site_url('auth/logout'); ?>" method="POST" style="display: none;"></form>
							</li>
						</ul>
					</li>
				</ul>

				<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
				<ul class="nav navbar-nav side-nav" id="side-menu">
					<li class="header">ADMINISTRATION</li>
					<?php foreach ($menuItems as $count => $item) { ?>
					<?php $children = Arr::get($item, 'children', array()); ?>
					<?php if (! empty($children)) { ?>
					<?php $active = in_array($currentUri, Arr::pluck($children, 'uri')); ?>
					<li <?= $active ? 'class="active"' : ''; ?>>
						<a href="javascript:;" class="collapse-toggle <?= ! $active ? 'collapsed' : ''; ?>" data-toggle="collapse" data-target="#menu-children-<?= $count; ?>" aria-expanded="false" role="button">
							<i class="fa fa-<?= $item['icon'] ?>"></i> <?= $item['title']; ?>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul id="menu-children-<?= $count; ?>" class="nav nav-second-level collapse <?= $active ? 'in' : ''; ?>">
						<?php foreach ($children as $child) { ?>
							<li <?= ($currentUri == $child['uri']) ? 'class="active"' : ''; ?>>
								<a href="<?= site_url($child['uri']); ?>"><i class="fa fa-circle-o"></i> <?= $child['title']; ?> <?= $child['label']; ?></a>
							</li>
						<?php } ?>
						</ul>
					</li>
					<?php } else { ?>
					<li <?= ($baseUri == $item['uri']) ? 'class="active"' : ''; ?>>
						<a href="<?= site_url($item['uri']); ?>"><i class="fa fa-<?= $item['icon'] ?>"></i> <?= $item['title']; ?> <?= $item['label']; ?></a>
					</li>
					<?php } ?>
					<?php } ?>
				</ul>
			</div>
		<!-- /.container -->
		</nav>
	</div>

	<div id="page-wrapper">
		<div class="container-fluid">
			<?= $content; ?>
		</div>
	</div>
	<div id="footer" class="footer">
		<div class="container-fluid">
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
</div>

<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- DataTables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>

<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
	e.preventDefault();

	$(this).toggleClass("toggled");

	$("#wrapper").toggleClass("toggled");
	$("#footer").toggleClass("toggled");
	$("#side-menu").toggleClass("toggled");
});
</script>

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
