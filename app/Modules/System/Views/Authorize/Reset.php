<div class='row-responsive'>
    <h2 style="margin-top: 25px; padding-bottom: 10px; border-bottom: 1px solid #FFF;"><?= __d('system', 'Password Reset'); ?></h2>
</div>

<div class="row">
    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('system', 'Password Reset'); ?></div>
            </div>
            <div class="panel-body">
                <?= Session::getMessages(); ?>

                <form method='post' action="<?= site_url('password/reset'); ?>" role="form">

                <div class="form-group">
                    <p><input type="text" name="email" id="email" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'Insert the current E-Mail'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'Insert the new Password'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'Verify the new Password'); ?>"><br><br></p>
                </div>
                <?php if (Config::get('recaptcha.active') === true) { ?>
                <div class="row">
                    <div class="row pull-right" style="margin-top: 10px; margin-right: 0;">
                        <div id="captcha" style="width: 304px; height: 78px;"></div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                </div>
                <?php } ?>
                <div class="row" style="margin-top: 22px;">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <input type="submit" name="submit" class="btn btn-success col-sm-4 pull-right" value="<?= __d('system', 'Send'); ?>">
                    </div>
                </div>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                <input type="hidden" name="token" value="<?= e($token); ?>" />

                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('recaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?php echo LANGUAGE_CODE; ?>" async defer></script>
