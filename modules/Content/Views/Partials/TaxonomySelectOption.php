<?php $padding = str_repeat('--', $level); ?>
<?php $name = isset($taxonomy) ? e($taxonomy->name) : __d('content', 'None'); ?>
<option value="<?= $itemId = isset($taxonomy) ? $taxonomy->id : 0; ?>"<?= ($itemId == $parentId) ? ' selected="selected"' : ''; ?>><?= trim($padding .' ' .$name); ?></option>
