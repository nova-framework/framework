<section class="content-header">
    <h2 style="margin-top: 25px; padding-bottom: 10px; border-bottom: 1px solid #FFF;"><?= __d('platform', 'Center Login'); ?></h2>
</section>

<!-- Main content -->
<section class="content">

<div class="row">
    <?= View::fetch('Partials/Messages'); ?>

    <div style="margin-top: 50px" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <div class="panel-title"><?= __d('platform', 'One-Time Login request for <b>{0}</b>', Config::get('app.name')); ?></div>
            </div>
            <div class="panel-body">
                <form method='POST' role="form">

                <p><?= __d('platform', 'Please enter your e-mail address to be sent a link to login one time.'); ?></p>

                <div class="form-group">
                    <p><input type="text" name="email" id="email" class="form-control input-lg col-xs-12 col-sm-12 col-md-12" placeholder="<?= __d('platform', 'Email'); ?>"><br><br></p>
                </div>
                <?php if (Config::get('reCaptcha.active') === true) { ?>
                <div class="row pull-right" style="margin-right: 0; margin-top: 15px;">
                    <div id="captcha" style="width: 304px; height: 78px;"></div>
                </div>
                <div class="clearfix"></div>
                <hr>
                <?php } else { ?>
                <input type="hidden" name="g-recaptcha-response" value="dummy" />
                <?php } ?>
                <div class="form-group" style="margin-top: 22px;">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <input type="submit" name="submit" class="btn btn-success col-sm-8" value="<?= __d('platform', 'Send'); ?>">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <a href="<?= site_url('login'); ?>" class="btn btn-link pull-right"><?= __d('platform', 'Login'); ?></a>
                    </div>
                </div>

                <?= csrf_field(); ?>

                </form>
            </div>
        </div>
    </div>
</div>

</section>

<?php if (Config::get('reCaptcha.active') === true) { ?>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('reCaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?= Language::code(); ?>" async defer></script>

<?php } ?>
