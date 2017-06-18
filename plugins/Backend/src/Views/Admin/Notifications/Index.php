<div class="row">
	<h1><?= __d('backend', 'Notifications'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
		<li><?= __d('backend', 'Notifications'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<style>

.pagination {
	margin: 0;
}

</style>

<div class="row">
	<h3><?= __d('backend', 'Unread notifications'); ?></h3>
	<br>
	<?php $format = __d('backend', '%d %b %Y, %H:%M'); ?>
	<?php if (! $notifications->isEmpty()) { ?>
	<table class='table table-bordered table-striped table-hover responsive'>
		<thead>
			<tr class="bg-navy disabled">
				<th style='text-align: center; vertical-align: middle;'><?= __d('backend', 'Message'); ?></th>
				<th style='text-align: center; vertical-align: middle;'><?= __d('backend', 'Sent At'); ?></th>
				<th style='text-align: right; vertical-align: middle;'><?= __d('backend', 'Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($notifications->all() as $item) { ?>
			<?php $data = $item->data; ?>
			<tr>
				<td style="text-align: left; vertical-align: middle;" width='75%'><?= $data['message']; ?></td>
				<td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->created_at->formatLocalized($format); ?></td>
				<td style="text-align: right; vertical-align: middle; padding: 5px;" width="10%"><a class="btn btn-sm btn-success" href="<?= $data['link']; ?>" target="_blank" role='button'><i class='fa fa-search'></i></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } else { ?>

	<div class="alert alert-info">
		<h4><?= __d('backend', 'No unread notifications'); ?></h4>
		<p><?= __d('backend', 'You have no unread notifications.'); ?></p>
	</div>

	<?php } ?>
</div>

<?php if (! $notifications->isEmpty()) { ?>

<div class="row">
	<div class="pull-right">
		<?= $notifications->links(); ?>
	</div>
	<div class="clearfix"></div>
	<br>
</div>

<?php } ?>

