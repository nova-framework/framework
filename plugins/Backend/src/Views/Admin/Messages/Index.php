<div class="row">
	<h1><?= __d('backend', 'Messages'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('backend', 'Dashboard'); ?></a></li>
		<li><?= __d('backend', 'Messages'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<h3><?= __d('backend', 'Manage the Messages'); ?></h3>
	<br>
	<a class='btn btn-success' href='<?= site_url('admin/messages/create'); ?>'><i class='fa fa-send'></i> <?= __d('backend', 'Send a new Message'); ?></a>
	<hr>
</div>

<div class="row">
	<h3><?= __d('backend', 'Conversations'); ?></h3>
	<br>

<style>

.pagination {
	margin: 0;
}

</style>

<?php if (! $messages->isEmpty()) { ?>
	<div class="list-group">

<?php
	$count = 0;

	$total = $messages->count();

	foreach($messages as $message) {
		$count++;

		// Calculate the number of unread replies on the current message.
		$unread = $message->replies->where('receiver_id', $authUser->id)->where('is_read', 0)->count();

		// If the parent message was not read yet by the receiver, count it too.
		if (($message->sender_id !== $authUser->id) && ($message->is_read === 0)) {
			$unread++;
		}
?>
		<!-- Statuses -->
		<div class="list-group-item" style="padding-right: 10px;">
			<div class="media">
				<div class="pull-left">
					<img class="img-thumbnail media-object img-responsive" style="height: 75px; width: 75px" alt="<?= $message->sender->fullName(); ?>" src="<?= $message->sender->picture(); ?>">
				</div>
				<div class="media-body">
					<div class="col-md-8">
						<h4 class="media-heading"><a href="<?= site_url('admin/messages/' .$message->id); ?>"><?= e($message->subject); ?></a> <?php if ($unread >  0) echo '<small class="label label-warning">' .$unread .'</small>'; ?></h4>
						<p class="no-margin"><?= __d('backend', 'From <b>{0}</b>, to <b>{1}</b>', $message->sender->fullName(), $message->receiver->fullName()); ?></p>
						<ul class="list-inline text-muted no-margin">
							<li><?= __d('backend', '{0, plural, one{# reply} other{# replies}}', $message->replies->count()); ?></li>
							<li><?= $message->created_at->diffForHumans(); ?></li>
						</ul>
					</div>
					<div class="col-md-4 no-padding">
						<a class="btn btn-sm btn-warning pull-right" title="<?= __d('backend', 'View this message and its replies'); ?>" href="<?= site_url('admin/messages/' .$message->id); ?>"><i class='fa fa-search'></i></a>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>

<?php } ?>
	</div>

<?php } else { ?>

	<div class="alert alert-info">
		<h4><?= __d('backend', 'No messages'); ?></h4>
		<p><?= __d('backend', 'You have no messages sent or received.'); ?></p>
	</div>

<?php } ?>

</div>

<div class="row">
	<div class="pull-right">
		<?= $messages->links(); ?>
	</div>
	<div class="clearfix"></div>
</div>
