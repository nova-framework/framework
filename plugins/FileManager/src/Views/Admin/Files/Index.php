<div class="row">
	<h1><?= __d('file_manager', 'Files'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('file_manager', 'Dashboard'); ?></a></li>
		<li><?= __d('file_manager', 'Files'); ?></li>
	</ol>
</div>

<!-- Main content -->
<div class="row">
	<div class="elfinder"></div>
</div>

<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?= resource_url('css/elfinder.min.css', 'FileManager'); ?>">
<link rel="stylesheet" type="text/css" href="<?= resource_url('css/theme.css', 'FileManager'); ?>">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= resource_url('js/elfinder.min.js', 'FileManager'); ?>"></script>
<script type="text/javascript" src="<?= resource_url('js/i18n/elfinder.' .($language = Language::code()) .'.js', 'FileManager'); ?>"></script>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		var beeper = $(document.createElement('audio')).hide().appendTo('body')[0];

		$('div.elfinder').elfinder({
			url : '<?= site_url('admin/files/connector'); ?>',
			dateFormat: 'M d, Y h:i A',
			fancyDateFormat: '$1 H:m:i',
			lang: '<?= $language; ?>',
			height: 600,
			cookie : {
				expires: 30,
				domain: '',
				path: '/',
				secure: false,
			},
		}).elfinder('instance');
	});
</script>
