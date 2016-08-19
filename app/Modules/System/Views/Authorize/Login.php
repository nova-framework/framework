<section class="content-header">
    <h2 style="margin-top: 25px; padding-bottom: 10px; border-bottom: 1px solid #FFF;"><?= __d('system', 'User Login'); ?></h2>
</section>

<!-- Main content -->
<section class="content">

<div class="row">
    <?php echo Session::getMessages();?>

    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('system', 'Login to <b>{0}</b>', SITETITLE); ?></div>
            </div>
            <div class="panel-body">
                <form method='post' role="form">

                <div class="form-group">
                    <p><input type="text" name="username" id="username" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'Username'); ?>"><br><br></p>
                </div>
                <div class="form-group">
                    <p><input type="password" name="password" id="password" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('system', 'Password'); ?>"><br><br></p>
                </div>
                <div class="form-group" style="margin-top: 20px; margin-left: 10px;">
                    <p><label><input name="remember" type="checkbox"> <?= __d('system', 'Remember me'); ?></label></p>
                </div>
                <hr>
                <?php if (Config::get('recaptcha.active') === true) { ?>
                <div class="row pull-right" style="margin-right: 0;">
                    <div id="captcha" style="width: 304px; height: 78px;"></div>
                </div>
                <div class="clearfix"></div>
                <hr>
                <?php } ?>
                <div class="form-group" style="margin-top: 22px;">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <input type="submit" name="submit" class="btn btn-success col-sm-8" value="<?= __d('system', 'Login'); ?>">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <a href="<?= site_url('password/remind'); ?>" class="btn btn-link pull-right"><?= __d('system', 'Forgot Password?'); ?></a>
                    </div>
                </div>

                <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

                </form>
            </div>
        </div>
    </div>
</div>

</section>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('recaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?php echo LANGUAGE_CODE; ?>" async defer></script>
