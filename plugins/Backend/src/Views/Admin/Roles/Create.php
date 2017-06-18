<div class="row">
	<h1><?= __d('backend', 'Create Role'); ?></h1>
	<ol class="breadcrumb">
		<li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
		<li><a href='<?= site_url('admin/roles'); ?>'><?= __d('backend', 'Roles'); ?></a></li>
		<li><?= __d('backend', 'Create Role'); ?></li>
	</ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
	<h4 class="box-title"><?= __d('backend', 'Create a new User Role'); ?></h4>
	<hr>

	<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
		<form class="form-horizontal" action="<?= site_url('admin/roles'); ?>" method='POST' role="form">

		<div class="form-group">
			<label class="col-sm-4 control-label" for="name"><?= __d('backend', 'Name'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('backend', 'Name'); ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="slug"><?= __d('backend', 'Slug'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('backend', 'Slug'); ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="description"><?= __d('backend', 'Description'); ?> <font color='#CC0000'>*</font></label>
			<div class="col-sm-8">
				<input name="description" id="description" type="text" class="form-control" value="<?= Input::old('description'); ?>" placeholder="<?= __d('backend', 'Description'); ?>">
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
		<font color='#CC0000'>*</font><?= __d('backend', 'Required field'); ?>
		<hr>
		<div class="form-group">
			<div class="col-sm-12">
				<input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('backend', 'Save'); ?>">
			</div>
		</div>

		<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

		</form>
	</div>
</div>

<div class="row">
	<hr>
	<a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
