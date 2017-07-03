<div class="row">
	<h1><?= __d('backend', 'User Login'); ?></h1>
	<hr style="margin-top: 0;">
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		<div class="panel panel-primary" >
			<div class="panel-heading">
				<div class="panel-title"><?= __d('backend', 'Login to <b>{0}</b>', Config::get('app.name')); ?></div>
			</div>
			<div class="panel-body">
				<form action="<?= site_url('auth/login'); ?>" method='POST' role="form">

				<div class="form-group">
					<p><input type="text" name="username" id="username" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('backend', 'Username'); ?>"><br><br></p>
				</div>
				<div class="form-group">
					<p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('backend', 'Password'); ?>"><br><br></p>
				</div>
				<div class="form-group" style="margin-top: 20px; margin-left: 10px;">
					<p><label><input name="remember" type="checkbox"> <?= __d('backend', 'Remember me'); ?></label></p>
				</div>
				<hr>
				<div class="form-group" style="margin-top: 22px;">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<input type="submit" name="submit" class="btn btn-success col-sm-8" value="<?= __d('backend', 'Login'); ?>">
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<a href="<?= site_url('password/remind'); ?>" class="btn btn-link pull-right"><?= __d('backend', 'Forgot Password?'); ?></a>
					</div>
				</div>

				<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

				</form>
			</div>
		</div>
	</div>
</div>
