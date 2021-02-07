<section class="content-header">
    <h1><?= __d('users', 'Create User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Create User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<form class="form-horizontal" action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Create a new User Account'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <h4><?= __d('users', 'Account'); ?></h4>
            <hr>

            <div class="form-group">
                <label class="col-sm-4 control-label" for="username"><?= __d('users', 'Username'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="username" id="username" type="text" class="form-control" value="<?= Input::old('username'); ?>" placeholder="<?= __d('users', 'Username'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password"><?= __d('users', 'Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password_confirmation"><?= __d('users', 'Confirm Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password confirmation'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="role"><?= __d('users','Roles'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <?php $optRoles = Input::old('roles', array()); ?>
                    <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" data-placeholder="<?= __d('users', 'Select a Role'); ?>" style="width: 100%;">
                        <?php foreach ($roles as $role) { ?>
                        <option value="<?= $role->id ?>" <?= in_array($role->id, $optRoles) ? 'selected' : ''; ?>><?= $role->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="realname"><?= __d('users', 'Name and Surname'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="realname" id="first-name" type="text" class="form-control" value="<?= Input::old('realname'); ?>" placeholder="<?= __d('users', 'Name and Surname'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="email"><?= __d('users', 'E-mail'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="email" id="email" type="text" class="form-control" value="<?= Input::old('email'); ?>" placeholder="<?= __d('users', 'E-mail'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <h4><?= __d('users', 'Profile'); ?></h4>
            <hr>
            <?= View::fetch('Modules/Users::Partials/Fields/Edit', compact('items')); ?>

            <div class="clearfix"></div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="realname"><?= __d('users', 'Profile Picture'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control" readonly>
                        <label class="input-group-btn">
                            <span class="btn btn-primary">
                                <?= __d('users', 'Browse ...'); ?> <input type="file" name="image" style="display: none;">
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color="#CC0000">*</font><?= __d('users', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('users', 'Save'); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<?= csrf_field(); ?>

</form>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/users'); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
