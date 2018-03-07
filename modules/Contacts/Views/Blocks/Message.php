<h3><?= __d('contacts', 'Contact Form'); ?></h3>
<hr>

<form action="<?= site_url('contacts'); ?>" method="POST">

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
<input type="hidden" name="cid" value="<?= $contact->id; ?>" />
<input type="hidden" name="path" value="<?= $path; ?>" />

<div class="col-md-6 col-md-offset-1" style="margin-bottom: 50px;">
    <div class="form-group<?= $errors->has('author') ? ' has-error' : ''; ?>">
        <label class="control-label" for="author">
            <?= __d('contacts', 'Name'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <input type="text" class="form-control" name="author" id="contact-form-author" placeholder="<?= __d('contacts', 'Name'); ?>" />
        <div class="clearfix"></div>
        <?php if ($errors->has('message')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('author'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('author_email') ? ' has-error' : ''; ?>">
        <label class="control-label" for="author_email">
            <?= __d('contacts', 'E-mail Address'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <input type="text" class="form-control" name="author_email" id="contact-form-author-email" placeholder="<?= __d('contacts', 'E-mail Address'); ?>" />
        <div class="clearfix"></div>
        <?php if ($errors->has('author_email')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('author_email'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('message') ? ' has-error' : '' ?>">
        <label class="control-label" for="message">
            <?= __d('contacts', 'Message'); ?> <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
        </label>
        <div class="clearfix"></div>
        <div class="col-sm-12" style="padding: 0;">
            <textarea name="message" id="contact-form-message" rows="10" class="form-control" style="resize: none;" placeholder="<?= __d('contacts', 'Message'); ?>"><?= Input::old('message'); ?></textarea>
        </div>
        <div class="clearfix"></div>
        <?php if ($errors->has('message')) { ?>
        <span class="help-block">
            <strong><?= $errors->first('message'); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="form-group">
        <input type="submit" name="submit" class="btn btn-primary col-md-3 pull-right" value="<?= __d('contacts', 'Submit'); ?>" />
    </div>
</div>

</form>

<div class="clear"></div>
