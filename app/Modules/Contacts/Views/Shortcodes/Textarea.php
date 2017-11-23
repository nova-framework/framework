<?php $name  = $shortcode->getParameter('name'); ?>
<?php $label = $shortcode->getParameter('label'); ?>
<?php $class = $shortcode->getParameter('class'); ?>

<?php $required = Str::is('*required*', $shortcode->getParameter('validation')); ?>

<div class="form-group<?= $errors->has($name) ? ' has-error' : '' ?>">
    <label class="control-label" for="<?= $name; ?>"><?= $label .($required ? ' <span class="text-danger" title=" ' .__d('contacts', 'Required field') .'">*</span>' : ''); ?></label>
    <div class="clearfix"></div>
    <div class="col-sm-<?= $shortcode->getParameter('columns') ?: 12; ?>" style="padding: 0;">
        <textarea name="<?= $name; ?>" id="contact-form-<?= $name; ?>" rows="<?= $shortcode->getParameter('rows'); ?>" class="form-control<?= ! empty($class) ? ' ' .$class : ''; ?>" style="resize: none;" placeholder="<?= $shortcode->getParameter('placeholder') ?: $label; ?>"><?= Input::old($name); ?></textarea>
    </div>
    <div class="clearfix"></div>
    <?php if ($errors->has($name)) { ?>
    <span class="help-block">
        <strong>' .$errors->first($name) .'</strong>
    </span>
    <?php } ?>
</div>
