<option value="<?= $taxonomy->id; ?>" <?= ($taxonomy->id == $parentId) ? ' selected="selected"' : ''; ?>>
    <?= trim(str_repeat('--', $level) .' ' .$taxonomy->name); ?>
</option>
