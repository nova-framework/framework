<div class='row-responsive'>
    <h2 style="margin-top: 25px; padding-bottom: 10px; border-bottom: 1px solid #FFF;"><?= __d('system', 'Password Recovery'); ?></h2>
</div>

<div class="row">
    <div style="margin-top:50px;" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('system', 'Reset your password for <b>{0}</b>', SITETITLE); ?></div>
            </div>
            <div style="padding-top: 30px" class="panel-body" >
                <?= Session::getMessages(); ?>

                <form method='post' role="form">

                <fieldset>
                    <p><?= __d('system', 'Please enter your e-mail address to be sent a link to reset your password.'); ?></p>

                    <div class="form-group">
                        <p><input type="email" name="email" id="email" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'E-mail'); ?>"><br><br></p>
                    </div>
                    <?php if (Config::get('recaptcha.active') === true) { ?>
                    <div class="row pull-right" style="margin-top: 10px; margin-right: 0;">
                        <div id="captcha" style="width: 304px; height: 78px;"></div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <?php } ?>
                    <div class="row" style="margin-top: 22px;">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <input type="submit" name="submit" class="btn btn-success col-sm-10" value="<?= __d('system', 'Send Reset Link'); ?>">
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <a href="<?= site_url('login'); ?>" class="btn btn-link pull-right"><?= __d('system', 'Login'); ?></a>
                        </div>
                    </div>
                </fieldset>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

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
