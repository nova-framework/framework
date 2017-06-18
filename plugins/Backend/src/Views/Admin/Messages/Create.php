<div class="row">
	<h1><?= __d('backend', 'Send Message'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
		<li><a href='<?= site_url('admin/messages'); ?>'><?= __d('backend', 'Messages'); ?></a></li>
		<li><?= __d('backend', 'Send Message'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<h3><?= __d('backend', 'Send a new Private Message'); ?></h3>
	<br>

	<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<form class="form-horizontal" action="<?= site_url('admin/messages'); ?>" method="POST" role="form">

				<div class="form-group <?= $errors->has('subject') ? 'has-error' : ''; ?>">
					<label class="col-sm-3 control-label" for="subject"><?= __d('backend', 'Subject'); ?> <font color='#CC0000'>*</font></label>
					<div class="col-sm-9">
						<input name="subject" id="subject" type="text" class="form-control" value="<?= Input::old('subject'); ?>" placeholder="<?= __d('backend', 'Subject'); ?>">
						<?php if ($errors->has('subject')) { ?>
						<span class="help-block"><?= $errors->first('subject'); ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="form-group <?= $errors->has('message') ? 'has-error' : ''; ?>">
					<label class="col-sm-3 control-label" for="message"><?= __d('backend', 'Message'); ?> <font color='#CC0000'>*</font></label>
					<div class="col-sm-9">
						<textarea id="message" name="message" class="form-control" style="resize: none;" placeholder="<?= __d('backend', 'Message'); ?>" rows="5" ><?= Input::old('message'); ?></textarea>
						<?php if ($errors->has('message')) { ?>
						<span class="help-block"><?= $errors->first('message'); ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="user"><?= __d('backend', 'Receiver'); ?> <font color='#CC0000'>*</font></label>
					<div class="col-sm-9">
						<?php $opt_user = Input::old('user'); ?>
						<select name="user" id="user" class="form-control select2">
							<option value="" <?php if (empty($opt_user)) echo 'selected'; ?>>- <?= __d('backend', 'Choose an User'); ?> -</option>
							<?php foreach ($users as $user) { ?>
							<option value="<?= $user->id ?>" <?php if ($opt_user == $user->id) echo 'selected'; ?>><?= $user->username; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<br>
				<font color='#CC0000'>*</font><?= __d('backend', 'Required field'); ?>

				<hr>
				<button type="submit" class="btn btn-success col-sm-3 pull-right"><i class='fa fa-send'></i> <?= __d('backend', 'Send'); ?></button>

				<input type="hidden" name="_token" value="<?= csrf_token(); ?>">

				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<hr>
	<a class='btn btn-primary col-sm-2' href='<?= site_url('admin/messages'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
