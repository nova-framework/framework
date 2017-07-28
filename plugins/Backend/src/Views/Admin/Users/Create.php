<?php

//
$opt_role = Input::old('role');

?>
<div class="row">
    <h1><?= __d('backend', 'Create User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('backend', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __d('backend', 'Users'); ?></a></li>
        <li><?= __d('backend', 'Create User'); ?></li>
    </ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
    <h4><?= __d('backend', 'Create a new User Account'); ?></h4>
    <hr>

    <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
        <form class="form-horizontal" action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

        <div class="form-group">
            <label class="col-sm-4 control-label" for="username"><?= __d('backend', 'Username'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <input name="username" id="username" type="text" class="form-control" value="<?= Input::old('username'); ?>" placeholder="<?= __d('backend', 'Username'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="role"><?= __d('backend', 'Role'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <select name="role" id="role" class="form-control select2">
                    <option value="" <?php if (empty($opt_role)) echo 'selected'; ?>>- <?= __d('backend', 'Choose a Role'); ?> -</option>
                    <?php foreach ($roles as $role) { ?>
                    <option value="<?= $role->id ?>" <?php if ($opt_role == $role->id) echo 'selected'; ?>><?= $role->name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="first_name"><?= __d('backend', 'First Name'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <input name="first_name" id="first_name" type="text" class="form-control" value="<?= Input::old('first_name'); ?>" placeholder="<?= __d('backend', 'First Name'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="last_name"><?= __d('backend', 'Last Name'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <input name="last_name" id="last_name" type="text" class="form-control" value="<?= Input::old('last_name'); ?>" placeholder="<?= __d('backend', 'Last Name'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="password"><?= __d('backend', 'Password'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('backend', 'Password'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="password_confirmation"><?= __d('backend', 'Confirm Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('backend', 'Password confirmation'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="email"><?= __d('backend', 'E-mail'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-8">
                <input name="email" id="email" type="text" class="form-control" value="<?= Input::old('email'); ?>" placeholder="<?= __d('backend', 'E-mail'); ?>">
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
    <a class='btn btn-primary' href='<?= site_url('admin/users'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
