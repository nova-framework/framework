<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $item->key; ?>"><?= $item->name; ?> <?= Str::contains($item->validate, 'required') ? '<font color="#CC0000">*</font>' : ''; ?></label>
    <div class="col-sm-<?= $item->columns; ?>">
        <input name="<?= $item->key; ?>" id="<?= $item->key; ?>" type="number" class="form-control" value="<?= $value; ?>">
     </div>
</div>
