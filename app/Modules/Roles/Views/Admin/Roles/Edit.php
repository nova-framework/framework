<section class="content-header">
    <h1><?= __d('users', 'Edit Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/roles'); ?>'><?= __d('users', 'Roles'); ?></a></li>
        <li><?= __d('users', 'Edit Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Edit the Role <b>{0}</b>', $role->name); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

            <form action="<?= site_url('admin/roles/' .$role->id); ?>" class="form-horizontal" method='POST' role="form">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('users', 'Name'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name', $role->name); ?>" placeholder="<?= __d('users', 'Name'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="slug"><?= __d('users', 'Slug'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug', $role->slug); ?>" placeholder="<?= __d('users', 'Slug'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="description"><?= __d('users', 'Description'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="description" id="description" type="text" class="form-control" value="<?= Input::old('description', $role->description); ?>" placeholder="<?= __d('users', 'Description'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('users', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('users', 'Save'); ?>">
                </div>
            </div>

            <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
            <input type="hidden" name="roleId" value="<?= $role->id; ?>" />

            </form>
        </div>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('users', '<< Previous Page'); ?></a>

</section>
