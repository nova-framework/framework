<div class="panel panel-<?= $type; ?>">
	<div class="panel-heading">
		<div class="row">
			<div class="col-xs-3">
				<i class="fa fa-<?= $icon; ?> fa-5x"></i>
			</div>
			<div class="col-xs-9 text-right">
				<div class="huge"><?= $count; ?></div>
				<div><?= $title; ?></div>
			</div>
		</div>
	</div>
	<a href="<?= isset($link) ? $link : '#'; ?>">
		<div class="panel-footer">
			<span class="pull-left"><?= __d('backend', 'View Details'); ?></span>
			<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
			<div class="clearfix"></div>
		</div>
	</a>
</div>
