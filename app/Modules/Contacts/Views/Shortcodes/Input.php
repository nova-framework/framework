<?php $type  = $shortcode->getParameter('type'); ?>
<?php $label = $shortcode->getParameter('label'); ?>

<?php $required = Str::is('*required*', $shortcode->getParameter('validation')); ?>

<div class="form-group<?= $errors->has($name = $shortcode->getParameter('name')) ? ' has-error' : ''; ?>">
    <?php if (($type != 'submit') && ($type != 'checkbox')) { ?>
    <label class="control-label" for="<?= $name; ?>"><?= $label .($required ? ' <span class="text-danger" title=" ' .__d('contacts', 'Required field') .'">*</span>' : ''); ?></label>
    <div class="clearfix"></div>
    <?php } ?>
    <div class="col-sm-<?= $shortcode->getParameter('columns') ?: 12; ?>" style="padding: 0;">
        <?php if ($type == 'file') { ?>
        <div class="input-group">
            <input type="text" id="file_path_<?= $name; ?>" class="form-control" placeholder="<?= __d('contacts', 'Browse...'); ?>" autocomplete="off">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button" id="file_browser_<?= $name; ?>"><i class="fa fa-search"></i><?= __d('contacts', 'Browse'); ?></button>
            </span>
            <input type="file" class="hidden" id="contact-form-<?= $name; ?>" name="<?= $name; ?>">
        </div>
        <?php } else if ($type == 'checkbox') { ?>
        <div class="col-md-1" style="padding: 0;">
            <input type="checkbox" name="<?= $name; ?>" id="contact-form-<?= $name; ?>" value="1" />
        </div>
        <div class="col-md-11" style="padding: 2px;">
            <label class="control-label" for="block-title" style="margin-right: 10px;"><?= $label; ?></label>
        </div>
        <div class="clearfix"></div>
        <?php } else if ($type == 'number') { ?>
        <input type="number" class="form-control" name="<?= $name; ?>" id="contact-form-<?= $name; ?>"  min="<?= $shortcode->getParameter('min'); ?>" max="<?= $shortcode->getParameter('max'); ?>" placeholder="<?= $label; ?>" />
        <?php } else if ($type == 'submit') { ?>
        <input type="submit" name="submit" class="btn btn-primary pull-right" value="<?= $label; ?>" />
        <?php } else { ?>
        <input type="<?= $type; ?>" class="form-control" name="<?= $name; ?>" id="contact-form-<?= $name; ?>" placeholder="<?= $label; ?>" />
        <?php } ?>

        <?php if ($errors->has($name)) { ?>
        <span class="help-block">
            <strong><?= $errors->first($name); ?></strong>
        </span>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php if ($type == 'file') { ?>

<script>

$("#file_browser_<?= $name; ?>").click(function(e) {
    e.preventDefault();

    $("#contact-form-<?= $name; ?>").click();
});

$("#contact-form-<?= $name; ?>").change(function() {
    var value = $(this).val();

    $("#file_path_<?= $name; ?>").val(value);
});

$("#file_path_<?= $name; ?>").click(function() {
    $("#file_browser_<?= $name; ?>").click();
});

</script>

<?php } ?>
