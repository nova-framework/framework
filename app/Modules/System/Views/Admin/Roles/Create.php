<section class="content-header">
    <h1><?= __d('system', 'Create Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/roles'); ?>'><?= __d('system', 'Roles'); ?></a></li>
        <li><?= __d('system', 'Create Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Create a new User Role'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

            <form class="form-horizontal" action="<?= site_url('admin/roles'); ?>" method='POST' role="form">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'Name'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('system', 'Name'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="slug"><?= __d('system', 'Slug'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('system', 'Slug'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="description"><?= __d('system', 'Description'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="description" id="description" type="text" class="form-control" value="<?= Input::old('description'); ?>" placeholder="<?= __d('system', 'Description'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('system', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('system', 'Save'); ?>">
                </div>
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

            </form>
        </div>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('system', '<< Previous Page'); ?></a>

</section>
