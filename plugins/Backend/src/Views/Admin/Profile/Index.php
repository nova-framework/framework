<div class="row">
	<h1><?= __d('backend', 'User Profile : <b>{0}</b>', $user->username); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('users/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
		<li><?= __d('backend', 'User Profile'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<h3 class="box-title"><?= __d('backend', 'Change Password'); ?></h3>
	<hr style="margin-top: 0;">

	<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
		<form method='post' role="form">

		<div class="form-group">
			<label class="col-sm-4 control-label" for="name"><?= __d('backend', 'Current Password'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="current_password" id="current_password" type="password" class="form-control" value="" placeholder="<?= __d('backend', 'Insert the current Password'); ?>">
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="name"><?= __d('backend', 'New Password'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('backend', 'Insert the new Password'); ?>">
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="name"><?= __d('backend', 'Confirm Password'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('backend', 'Verify the new Password'); ?>">
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
		<font color='#CC0000'>*</font><?= __d('backend', 'Required field'); ?>
		<hr>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('backend', 'Save'); ?>">
			</div>
		</div>

		<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

		</form>
	</div>
</div>
