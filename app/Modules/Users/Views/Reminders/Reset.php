<div class='row-responsive'>
    <h2><?= __d('users', 'Password Reset'); ?></h2>
    <hr>
</div>

<div class="row">
    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('users', 'Password Reset'); ?></div>
            </div>
            <div class="panel-body">
                <form method='post' action="<?= site_url('password/reset'); ?>" role="form">

                <?= Errors::display($error); ?>
                <?= Session::message('message'); ?>

                <div class="form-group">
                    <p><input type="text" name="email" id="email" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Insert the current E-Mail'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Insert the new Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Verify the new Password'); ?>"><br><br></p>
                </div>
                <div class="row" style="margin-top: 22px;">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <input type="submit" name="submit" class="btn btn-success col-sm-4 pull-right" value="<?= __d('users', 'Send'); ?>">
                    </div>
                </div>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                <input type="hidden" name="token" value="<?= $token; ?>" />

                </form>
            </div>
        </div>
    </div>
</div>
