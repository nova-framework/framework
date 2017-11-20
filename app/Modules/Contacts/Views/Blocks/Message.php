<h3><?= __d('contacts', 'Contact Form'); ?></h3>
<hr>

<form action="<?= site_url('contacts'); ?>" method="POST">
<?= csrf_field() ?>
<input type="hidden" name="path" value="<?= $path; ?>" />

<div class="col-md-6 col-md-offset-1">
    <div class="form-group<?= $errors->has('author') ? ' has-error' : ''; ?>">
        <label for="author"><?= __d('contacts', 'Name'); ?> *</label> <br/>
        <input type="text" name="author" class="form-control" value="<?= Input::old('author'); ?>" />

        <?php if ($errors->has('author')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('author'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('author_email') ? ' has-error' : ''; ?>">
        <label for="author_email"><?= __d('contacts', 'Email Address'); ?> *</label> <br/>
        <input type="text" name="author_email" class="form-control" value="<?= Input::old('author_email'); ?>" />

        <?php if ($errors->has('author_email')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('author_email'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('content') ? ' has-error' : ''; ?>">
        <label for="content"><?= __d('contacts', 'Message'); ?></label> <br/>
        <textarea cols="60" rows="6" class="form-control" name="content"><?= Input::old('content'); ?></textarea>
        <?php if ($errors->has('content')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('content'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <?php if (! Auth::check() && (Config::get('reCaptcha.active') === true)) { ?>
    <div style="width: 304px; margin: 0 auto; display: block;">
        <div id="captcha" style="width: 304px; height: 78px;"></div>
    </div>
    <div class="clearfix"></div>
    <hr style="margin-top: 15px; margin-bottom: 15px;">
    <?php } ?>
    <div class="form-group">
        <input type="submit" class="btn btn-primary pull-right" value="<?= __d('contacts', 'Submit Message'); ?>" />
    </div>
</form>

</div>

<div class="clear"></div>
