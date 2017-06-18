<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<div class="wrapper">
		<form action="<?= site_url('auth/login'); ?>" method='POST' class="form-signin">

		<h2 class="form-signin-heading"><?= __d('backend', 'Please login'); ?></h2>

		<input type="text" class="form-control" name="username" placeholder="<?= __d('backend', 'Username'); ?>" required="" autofocus="" />
		<input type="password" class="form-control" name="password" placeholder="<?= __d('backend', 'Password'); ?>" required=""/>
		<label class="checkbox">
			<input type="checkbox" value="remember" id="remember" name="remember">&nbsp;<?= __d('backend', 'Remember me'); ?>
		</label>
		<button class="btn btn-lg btn-primary btn-block" type="submit"><?= __d('backend', 'Login'); ?></button>

		<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

		</form>
	</div>
</div>

