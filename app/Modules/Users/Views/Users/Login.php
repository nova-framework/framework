<div class='row-responsive'>
    <h2><?= __d('users', 'User Login'); ?></h2>
    <hr>
</div>

<div class="row">
    <?php echo Session::message('message');?>

    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('users', 'Login to <b>{0}</b>', SITETITLE); ?></div>
            </div>
            <div class="panel-body">
                <form method='post' role="form">

                <?php echo Errors::display($error);?>

                <div class="form-group">
                    <p><input type="text" name="username" id="username" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Username'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('users', 'Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input name="remember" type="checkbox"> <?= __d('users', 'Remember me'); ?></p>
                </div>
                <hr>
                <div class="row" style="margin-top: 22px;">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <input type="submit" name="submit" class="btn btn-success col-sm-8" value="<?= __d('users', 'Login'); ?>">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <a href="<?= site_url('password/remind'); ?>" class="btn btn-link pull-right"><?= __d('users', 'Forgot Password?'); ?></a>
                    </div>
                </div>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

                </form>
            </div>
        </div>
    </div>
</div>
