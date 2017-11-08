<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $item->key; ?>"><?= $item->name; ?> <?= Str::contains($item->validate, 'required') ? '<font color="#CC0000">*</font>' : ''; ?></label>
    <div class="col-sm-<?= $item->columns; ?>">
        <input type="checkbox" name="<?= $item->key; ?>" id="<?= $item->key; ?>" value="1" <?= $value ? 'checked="checked"' : ''; ?> />
     </div>
</div>
