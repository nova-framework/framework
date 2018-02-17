<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $field->key; ?>"><?= $field->name; ?> <?= Str::contains($field->validate, 'required') ? '<font color="#CC0000">*</font>' : ''; ?></label>
    <div class="col-sm-<?= $field->columns; ?>">
        <input name="<?= $field->key; ?>" id="<?= $field->key; ?>" type="number" class="form-control" value="<?= $value; ?>">
     </div>
</div>
