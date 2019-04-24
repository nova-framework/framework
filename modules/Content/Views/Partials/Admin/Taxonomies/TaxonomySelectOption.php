<?php $name = isset($taxonomy) ? trim(str_repeat('--', $level) .' ' .e($taxonomy->name)) : __d('content', 'None'); ?>
<option value="<?= $itemId = isset($taxonomy) ? $taxonomy->id : 0; ?>"<?= ($itemId == $parentId) ? ' selected="selected"' : ''; ?>><?= $name; ?></option>
