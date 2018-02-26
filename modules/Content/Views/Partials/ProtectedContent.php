<div class="container">
    <div class="row" style="margin-top: 5%; margin-bottom: 5%;">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-lock"></i> <?= __d('content', 'Enter password to unlock'); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="center-block text-center">
                        <img class="img-thumbnail img-circle" style="margin-bottom: 20px;" src="<?= resource_url('images/protected-content.jpg', 'content'); ?>" alt="">
                        <form action="<?= site_url('content/' .$post->id); ?>" method="POST" role="form">
                            <div class="input-group" style="margin-bottom: 15px;">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input name="password" id="unlock-content-input" type="password" class="form-control" placeholder="Password" required autofocus>
                            </div>
                            <?php if (! Auth::check() && (Config::get('reCaptcha.active') === true)) { ?>
                            <div style="width: 304px; margin: 0 auto; display: block;">
                                <div id="captcha" style="width: 304px; height: 78px;"></div>
                            </div>
                            <div class="clearfix"></div>
                            <hr style="margin-top: 15px; margin-bottom: 15px;">
                            <?php } ?>
                            <input name="submit" id="unlock-content-submit" type="submit" class="btn btn-success col-md-6 pull-right" value="<?= __d('content', 'Unlock'); ?>" />
                            <?= csrf_field(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (! Auth::check() && (Config::get('reCaptcha.active') === true)) { ?>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('reCaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?= Language::code(); ?>" async defer></script>

<?php } ?>
