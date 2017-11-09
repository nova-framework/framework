<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $field->key; ?>"><?= $field->name; ?> <?= Str::contains($field->validate, 'required') ? '<font color="#CC0000">*</font>' : ''; ?></label>
    <div class="col-sm-<?= $field->columns; ?>">
        <div class="input-group">
            <input type="text" id="file_path_<?= $field->key; ?>" class="form-control" placeholder="<?= __d('fields', 'Browse...'); ?>">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="button" id="file_browser_<?= $field->key; ?>">
                        <i class="fa fa-search"></i> <?= __d('fields', 'Browse'); ?>
                    </button>
                </span>
            </div>
            <input type="file" class="hidden" id="<?= $field->key; ?>" name="<?= $field->key; ?>">
     </div>
</div>

<script>

$('#file_browser_<?= $field->key; ?>').click(function(e) {
    e.preventDefault();

    $('#<?= $field->key; ?>').click();
});

$('#<?= $field->key; ?>').change(function() {
    var value = $(this).val();

    $('#file_path_<?= $field->key; ?>').val(value);
});

$('#file_path_<?= $field->key; ?>').click(function() {
    $('#file_browser_<?= $field->key; ?>').click();
});

</script>
