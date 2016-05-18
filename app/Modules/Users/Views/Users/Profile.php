<div class='row-responsive'>
    <h2><?= __d('users', 'User Profile : {0}', $user->realname); ?></h2>
    <hr>
</div>

<div class="row">
    <?php echo Errors::display($error); ?>
    <?php echo Session::message('message'); ?>
    <div class="clearfix"></div>

    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-primary" style="margin-top:40px">
            <div class="panel-heading text-center">
                <h3 class="panel-title"><?= __d('users', 'Change Password'); ?></h3>
            </div>
            <div class="panel-body">
                <form style="margin: 0;" method="post">
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="password" placeholder="<?= __d('users', 'Insert the current Password'); ?>" title="<?= __d('users', 'Insert the current Password'); ?>">
                    </div>
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="newPassword" placeholder="<?= __d('users', 'Insert the new Password'); ?>" title="<?= __d('users', 'Insert the new Password'); ?>">
                    </div>
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="confirmPass" placeholder="<?= __d('users', 'Verify the new Password'); ?>" title="<?= __d('users', 'Verify the new Password'); ?>">
                    </div>
                    <hr>
                    <div>
                        <button type="submit" class="btn btn-success col-lg-6 pull-right"><i class='fa fa-check'></i> <?= __d('users', 'Save'); ?></button>
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                </form>
            </div>
        </div>
    </div>
</div>
