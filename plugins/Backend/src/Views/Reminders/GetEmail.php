<div class='row'>
    <h1><?= __d('backend', 'Password Recovery'); ?></h1>
    <hr style="margin-top: 0;">
</div>

<?= View::fetch('Partials/Messages'); ?>

<div class="row">
    <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('backend', 'Reset your password for <b>{0}</b>', Config::get('app.name')); ?></div>
            </div>
            <div style="padding-top: 30px" class="panel-body" >
                <form action="<?= site_url('password/remind'); ?>" method='POST' role="form">

                <fieldset>
                    <p><?= __d('backend', 'Please enter your e-mail address to be sent a link to reset your password.'); ?></p>

                    <div class="form-group">
                        <p><input type="email" name="email" id="email" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('backend', 'E-mail'); ?>"><br><br></p>
                    </div>
                    <div class="row" style="margin-top: 22px;">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <input type="submit" name="submit" class="btn btn-success col-sm-10" value="<?= __d('backend', 'Send Reset Link'); ?>">
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <a href="<?= site_url('login'); ?>" class="btn btn-link pull-right"><?= __d('backend', 'Login'); ?></a>
                        </div>
                    </div>
                </fieldset>

                <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

                </form>
            </div>
        </div>
    </div>
</div>
