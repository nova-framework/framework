<div class='row-responsive'>
    <h2 style="margin-top: 25px; padding-bottom: 10px; border-bottom: 1px solid #FFF;"><?= __d('platform', 'Password Reset'); ?></h2>
</div>

<?= View::fetch('Partials/Messages'); ?>

<div class="row">
    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('platform', 'Password Reset'); ?></div>
            </div>
            <div class="panel-body">
                <form method='post' action="<?= site_url('password/reset'); ?>" role="form">

                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('platform', 'Insert the new Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('platform', 'Verify the new Password'); ?>"><br><br></p>
                </div>
                <div class="row" style="margin-top: 22px;">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <input type="submit" name="submit" class="btn btn-success col-sm-4 pull-right" value="<?= __d('platform', 'Send'); ?>">
                    </div>
                </div>

                <?= csrf_field(); ?>

                <input type="hidden" name="email" value="<?= $email; ?>" />
                <input type="hidden" name="token" value="<?= $token; ?>" />

                </form>
            </div>
        </div>
    </div>
</div>
