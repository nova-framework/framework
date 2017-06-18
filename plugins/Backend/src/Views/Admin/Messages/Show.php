<div class="row">
	<h1><?= __d('backend', 'Show Message'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
		<li><a href='<?= site_url('admin/messages'); ?>'><?= __d('backend', 'Messages'); ?></a></li>
		<li><?= __d('backend', 'Show Message'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<h3><?= __d('backend', 'Subject : {0}', $message->subject); ?></h3>
	<br>

	<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<!-- Status -->
				<div class="media">
					<div class="pull-left">
						<img class="img-thumbnail" style="height: 60px; width: 60px" src="<?= $message->sender->picture(); ?>" alt="<?= $message->sender->fullName(); ?>" class="media-object">
					</div>
					<div class="media-body">
						<h4 class="media-heading"><?= $message->sender->fullName(); ?></h4>
						<p><?= e($message->body); ?></p>
						<ul class="list-inline text-muted">
							<li><?= $message->created_at->diffForHumans(); ?></li>
						</ul>
					</div>
				</div>
				<?php if (! $message->replies->isEmpty()) { ?>
				<hr style="margin-bottom: 0;">
				<?php } else { ?>
				<br>
				<?php } ?>
				<!-- Replies -->
				<?php foreach($message->replies as $reply) { ?>
				<div class="media comment-block">
					<a class="pull-left" href="<?= site_url('user/' .$reply->sender->username); ?>">
						<img class="img-thumbnail" style="height: 60px; width: 60px" src="<?= $message->sender->picture(); ?>" alt="<?= $reply->sender->fullName(); ?>" class="media-object">
					</a>
					<div class="media-body">
						<h4 class="media-heading"><?= $reply->sender->fullName(); ?></h4>
						<p><?= e($reply->body); ?></p>
						<ul class="list-inline text-muted">
							<li><?= $reply->created_at->diffForHumans(); ?></li>
						</ul>
					</div>
				</div>
				<?php } ?>
				<!-- Reply Form -->
				<form action="<?= site_url('admin/messages/' .$message->id); ?>" role="form" method="POST">

				<div class="form-group <?= $errors->has('reply') ? 'has-error' : ''; ?>">
					<textarea style="resize: none" name="reply" class="form-control" placeholder="<?= __d('backend', 'Reply to this {0, select, 0 {message} other {thread}}...', $message->replies->count()); ?>" rows="3"></textarea>
					<?php if ($errors->has('reply')) { ?>
					<span class="help-block"><?= $errors->first(); ?></span>
					<?php } ?>
				</div>
				<button type="submit" class="btn btn-success col-sm-3 pull-right"><i class='fa fa-reply'></i> <?= __d('backend', 'Reply'); ?></button>
				<input type="hidden" name="_token" value="<?= csrf_token(); ?>">

				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<hr style="margin-top: 0;">
	<a class='btn btn-primary' href='<?= site_url('admin/messages'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
