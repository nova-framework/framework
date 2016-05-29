<div class='row-responsive'>
    <h2><?= __d('users', 'User Profile : {0}', $user->username); ?></h2>
    <hr>
</div>

<div class="row">
    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('users', 'Change Password'); ?></div>
            </div>
            <div class="panel-body">
                <form method='post' role="form">

                <?= Errors::display($error); ?>
                <?= Session::message('message'); ?>

                <div class="form-group">
                    <p><input type="password" name="current_password" id="current_password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Insert the current Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Insert the new Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Verify the new Password'); ?>"><br><br></p>
                </div>
                <div class="row" style="margin-top: 22px;">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <input type="submit" name="submit" class="btn btn-success col-sm-4 pull-right" value="<?= __d('users', 'Save'); ?>">
                    </div>
                </div>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

                </form>
            </div>
        </div>
    </div>
</div>
