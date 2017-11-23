<?php $label = $shortcode->getParameter('label'); ?>
<?php $class = $shortcode->getParameter('class'); ?>

<?php $required = Str::is('*required*', $shortcode->getParameter('validation')); ?>

<div class="form-group<?= $errors->has($name = $shortcode->getParameter('name')) ? ' has-error' : ''; ?>">
    <label class="control-label" for="<?= $name; ?>"><?= $label .($required ? ' <span class="text-danger" title=" ' .__d('contacts', 'Required field') .'">*</span>' : ''); ?></label>
    <div class="clearfix"></div>
    <div class="col-sm-<?= $shortcode->getParameter('columns') ?: 12; ?>" style="padding: 0;">
        <select name="<?= $name ?>" id="contact-form-<?= $name; ?>" class="form-control select2<?= ! empty($class) ? ' ' .$class : ''; ?>" placeholder="" data-placeholder="<?= $shortcode->getParameter('placeholder'); ?>" style="width: 100%;" autocomplete="off">
            <?= $shortcode->getContent(); ?>
        </select>
        <?php if ($errors->has($name)) { ?>
        <span class="help-block">
            <strong><?= $errors->first($name); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>
