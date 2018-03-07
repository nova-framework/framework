<h3><?= __d('contacts', 'Contact Form'); ?></h3>
<hr>

<form action="<?= site_url('contacts'); ?>" method="POST">

<div class="col-md-6 col-md-offset-1" style="margin-bottom: 50px;">
    <div class="form-group<?= $errors->has('contact_author') ? ' has-error' : ''; ?>">
        <label class="control-label" for="contact_author">
            <?= __d('contacts', 'Name'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <input type="text" class="form-control" name="contact_author" id="contact-form-author" value="<?= Input::old('contact_author'); ?>" placeholder="<?= __d('contacts', 'Name'); ?>" />
        <div class="clearfix"></div>
        <?php if ($errors->has('contact_author')) { ?>
        <span class="help-block">
            <?= $errors->first('contact_author'); ?>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('contact_author_email') ? ' has-error' : ''; ?>">
        <label class="control-label" for="contact_author_email">
            <?= __d('contacts', 'E-mail Address'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <input type="text" class="form-control" name="contact_author_email" id="contact-form-author-email" value="<?= Input::old('contact_author_email'); ?>" placeholder="<?= __d('contacts', 'E-mail Address'); ?>" />
        <div class="clearfix"></div>
        <?php if ($errors->has('contact_author_email')) { ?>
        <span class="help-block">
            <?= $errors->first('contact_author_email'); ?>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('contact_author_url') ? ' has-error' : ''; ?>">
        <label class="control-label" for="contact_author_url">
            <?= __d('contacts', 'Website'); ?>
        </label>
        <input type="text" class="form-control" name="contact_author_url" id="contact-form-author-url" value="<?= Input::old('contact_author_url'); ?>" placeholder="<?= __d('contacts', 'Website'); ?>" />
        <div class="clearfix"></div>
        <?php if ($errors->has('contact_author_url')) { ?>
        <span class="help-block">
            <?= $errors->first('contact_author_url'); ?>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('contact_content') ? ' has-error' : '' ?>">
        <label class="control-label" for="contact_content">
            <?= __d('contacts', 'Message'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <div class="clearfix"></div>
        <div class="col-sm-12" style="padding: 0;">
            <textarea name="contact_content" id="contact-form-content" rows="10" class="form-control" style="resize: none;" placeholder="<?= __d('contacts', 'Message'); ?>"><?= Input::old('contact_content'); ?></textarea>
        </div>
        <div class="clearfix"></div>
        <?php if ($errors->has('contact_content')) { ?>
        <span class="help-block">
            <?= $errors->first('contact_content'); ?>
        </span>
        <?php } ?>
    </div>
    <?php $withCaptcha = ! Auth::check() && (Config::get('reCaptcha.active') === true); ?>
    <?php if ($withCaptcha) { ?>
    <div style="width: 304px; margin: 0 auto; display: block;">
        <div id="captcha" style="width: 304px; height: 78px;"></div>
    </div>
    <div class="clearfix"></div>
    <hr style="margin-top: 15px; margin-bottom: 15px;">
    <?php } ?>
    <div class="form-group">
        <input type="submit" name="submit" class="btn btn-primary col-md-3 pull-right" value="<?= __d('contacts', 'Submit'); ?>" />
    </div>
</div>

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
<input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />
<input type="hidden" name="path" value="<?= $path; ?>" />

</form>

<div class="clear"></div>

<?php if ($withCaptcha) { ?>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('reCaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?= Language::code(); ?>" async defer></script>

<?php } ?>

