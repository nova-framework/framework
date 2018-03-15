<form action="<?= site_url('contacts'); ?>" method="POST" enctype="multipart/form-data">

<div class="col-md-6 col-md-offset-1" style="margin-bottom: 50px;">

<?php foreach ($contact->fieldGroups->sortBy('order') as $group) { ?>
    <h3><?= $group->title; ?></h3>
    <hr>

    <?php foreach ($group->fieldItems->sortBy('order') as $item) { ?>
        <?php $type = $item->type; ?>
        <?php $name = 'contact_' .$item->name; ?>
        <?php $id = 'contact-form-' .str_replace('_', '-', $item->name); ?>
        <?php $required = Str::contains($item->rules, 'required'); ?>
        <?php $options = $item->options ?: array(); ?>

    <div class="form-group<?= $errors->has($name) ? ' has-error' : ''; ?>">
        <label class="control-label" for="<?= $name; ?>">
            <?= $item->title; ?>
            <?php if ($required) { ?>
            <span class="text-danger" title="<?= __d('contacts', 'Required field'); ?>">*</span>
            <?php } ?>
        </label>
        <div class="clearfix"></div>

        <?php if ($type == 'text') { ?>
        <input type="text" class="form-control" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= Input::old($name, array_get($options, 'default')); ?>" placeholder="<?= $item->title; ?>" />
        <?php } else if ($type == 'password') { ?>
        <input type="password" class="form-control" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= Input::old($name); ?>" placeholder="<?= $item->title; ?>" />
        <?php } else if ($type == 'textarea') { ?>
        <textarea name="<?= $name; ?>" id="<?= $id; ?>" rows="<?= array_get($options, 'rows', 10); ?>" class="form-control" style="resize: none;" placeholder="<?= $item->title; ?>"><?= Input::old($name); ?></textarea>
        <?php } else if ($type == 'select') { ?>
        <?php $selected = Input::old($name, array_get($options, 'default')); ?>
        <?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
        <select name="<?= $name; ?>" id="<?= $id; ?>" placeholder="" data-placeholder="<?= __d('requests', '- Choose an option -'); ?>" class="form-control select2">
            <option></option>
            <?php foreach($choices as $choice) { ?>
            <?php list ($value, $label) = explode(':', trim($choice)); ?>
            <option value="<?= $value = trim($value); ?>" <?= ($value == $selected) ? 'selected="selected"' : ''; ?>><?= trim($label); ?></option>
            <?php } ?>
        </select>
        <?php } else if ($type == 'checkbox') { ?>
        <?php $checked = Input::old($name, array()); ?>
        <?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
        <?php foreach($choices as $choice) { ?>
        <?php list ($value, $label) = explode(':', trim($choice)); ?>
        <?php $checkId = $id .'-' .str_replace('_', '-', $value = trim($value)); ?>
        <div class="checkbox icheck-primary">
            <input type="checkbox" name="<?= $name; ?>[]" id="<?= $checkId; ?>" value="<?= $value; ?>" <?= in_array($value, $checked) ? 'checked' : ''; ?>> <label for="<?= $checkId; ?>"><?= trim($label); ?></label>
        </div>
        <div class="clearfix"></div>
        <?php } ?>
        <?php } else if ($type == 'radio') { ?>
        <?php $checked = Input::old($name); ?>
        <?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
        <?php foreach($choices as $choice) { ?>
        <?php list ($value, $label) = explode(':', trim($choice)); ?>
        <?php $checkId = $id .'-' .str_replace('_', '-', $value = trim($value)); ?>
        <div class="radio icheck-primary">
            <input type="radio" name="<?= $name; ?>" id="<?= $checkId; ?>" value="<?= $value; ?>" <?= ($value == $checked) ? 'checked' : ''; ?>> <label for="<?= $checkId; ?>"><?= trim($label); ?></label>
        </div>
        <div class="clearfix"></div>
        <?php } ?>
        <?php } else if ($type == 'file') { ?>
        <div class="input-group">
            <input type="text" class="form-control" readonly>
            <label class="input-group-btn">
                <span class="btn btn-<?= $errors->has($name) ? 'danger' : 'default' ?>">
                    <?= __d('contacts', 'Browse ...'); ?> <input type="file" name="<?= $name; ?>" style="display: none;">
                </span>
            </label>
        </div>
        <?php } ?>
        <?php if ($errors->has($name)) { ?>
        <span class="help-block">
            <?= $errors->first($name); ?>
        </span>
        <?php } ?>
    </div>

    <?php } ?>
<?php } ?>

    <?php $captchaEnabled = ! Auth::check() && (Config::get('reCaptcha.active') === true); ?>
    <?php if ($captchaEnabled) { ?>
    <div style="width: 304px; margin: 0 auto; display: block;">
        <div id="captcha" style="width: 304px; height: 78px;"></div>
    </div>
    <div class="clearfix"></div>
    <hr style="margin-top: 15px; margin-bottom: 15px;">
    <?php } else { ?>
    <input type="hidden" name="g-recaptcha-response" value="dummy" />
    <?php } ?>
    <div class="form-group" style="margin-top: 25px;">
        <input type="submit" name="submit" class="btn btn-primary col-md-3" value="<?= __d('contacts', 'Submit'); ?>" />
    </div>
</div>

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
<input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />
<input type="hidden" name="path" value="<?= $path; ?>" />

</form>

<div class="clear"></div>

<script type="text/javascript">

$(function() {

    // We can attach the `fileselect` event to all file inputs on the page
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        if (input.get(0).files) {
            var items = [];

            var files = input.get(0).files;

            for (var i = 0, file; file = files[i]; i++) {
                items.push(file.name);
            }

            label = items.join(', ');
        }

        input.trigger('fileselect', [numFiles, label]);
    });

    // We can watch for our custom `fileselect` event like this
    $(document).ready( function() {
        $(':file').on('fileselect', function(event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = (numFiles > 1) ? sprintf("<?= __d('contacts', '%d files selected'); ?>", numFiles) : label;

            if (input.length) {
                input.val(label);
            } else {
                if (log) alert(log);
            }
      });
  });

});

</script>

<?php if ($captchaEnabled) { ?>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('reCaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?= Language::code(); ?>" async defer></script>

<?php } ?>
