<?php if (! is_null($taxonomy)) { ?>
<option value="<?= $taxonomy->id; ?>" <?= ($taxonomy->id == $parentId) ? ' selected="selected"' : ''; ?>>
    <?= trim(str_repeat('--', $level) .' ' .$taxonomy->name); ?>
</option>
<?php } else { ?>
<option value="0" <?= ($parentId == 0) ? ' selected="selected"' : ''; ?>>
    <?= __d('content', 'None'); ?>
</option>
<?php } ?>
