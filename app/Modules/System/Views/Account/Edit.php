<section class="content-header">
    <h1><?= __d('system', 'User Profile : {0}', $user->username); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('users/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __d('system', 'Users'); ?></a></li>
        <li><?= __d('system', 'User Profile'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div  class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Change Password'); ?></h3>
    </div>

    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <form method='post' role="form">

            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'Current Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="current_password" id="current_password" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Insert the current Password'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'New Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Insert the new Password'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'Confirm Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Verify the new Password'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('system', 'Required field'); ?>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('system', 'Save'); ?>">
                </div>
            </div>
            <br>

            <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

            </form>
        </div>
    </div>
</div>

</section>
