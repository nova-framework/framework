<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $item->key; ?>"><?= $item->name; ?> <?= Str::contains($item->validate, 'required') ? '<font color="#CC0000">*</font>' : ''; ?></label>
    <div class="col-sm-8">
        <input name="<?= $item->key; ?>" id="<?= $item->key; ?>" type="text" class="form-control" value="<?= $value; ?>" placeholder="<?= $item->name; ?>">
     </div>
</div>
