<section class="content-header">
    <h1><?= __d('roles', 'Create Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('roles', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/roles'); ?>"><?= __d('roles', 'Roles'); ?></a></li>
        <li><?= __d('roles', 'Create Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('roles', 'Create a new User Role'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

            <form class="form-horizontal" action="<?= site_url('admin/roles'); ?>" method='POST' role="form">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('roles', 'Name'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('roles', 'Name'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="slug"><?= __d('roles', 'Slug'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('roles', 'Slug'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="description"><?= __d('roles', 'Description'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="description" id="description" type="text" class="form-control" value="<?= Input::old('description'); ?>" placeholder="<?= __d('roles', 'Description'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color="#CC0000">*</font><?= __d('roles', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('roles', 'Save'); ?>">
                </div>
            </div>

            <?= csrf_field(); ?>

            </form>
        </div>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/roles'); ?>"><?= __d('roles', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
