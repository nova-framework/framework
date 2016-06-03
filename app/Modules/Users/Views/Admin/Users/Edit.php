<?php

//
$opt_username = Input::old('username');
$opt_realname = Input::old('realname');
$opt_email    = Input::old('email');

//
$opt_username = ! empty($opt_username) ? $opt_username : $user->username;
$opt_realname = ! empty($opt_realname) ? $opt_realname : $user->realname;
$opt_email    = ! empty($opt_email)    ? $opt_email    : $user->email;

?>

<section class="content-header">
    <h1><?= __d('users', 'Edit User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Edit User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::message('status'); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Edit the User <b>{0}</b>', $user->username); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

            <form action="<?= site_url('admin/users/' .$user->id); ?>" class="form-horizontal" method='POST' role="form">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="username"><?= __d('users', 'Username'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="username" id="username" type="text" class="form-control" value="<?= $opt_username; ?>" placeholder="<?= __d('users', 'Username'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="realname"><?= __d('users', 'Name and Surname'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="realname" id="realname" type="text" class="form-control" value="<?= $opt_realname; ?>" placeholder="<?= __d('users', 'Name and Surname'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password"><?= __d('users', 'Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password_confirmation"><?= __d('users', 'Confirm Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password confirmation'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="email"><?= __d('users', 'E-mail'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="email" id="email" type="text" class="form-control" value="<?= $opt_email; ?>" placeholder="<?= __d('users', 'Email'); ?>">
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
            <input type="hidden" name="userId" value="<?= $user->id; ?>" />

            </form>
        </div>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/users'); ?>'><?= __d('users', '<< Previous Page'); ?></a>

</section>
