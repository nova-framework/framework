<?php

if (! is_null($taxonomy)) {
    $id   = $taxonomy->id;
    $name = $taxonomy->name;
} else {
    $id   = 0;
    $name = __d('content', 'None');
}

?>
<option value="<?= $id; ?>" <?= ($id == $parentId) ? ' selected="selected"' : ''; ?>>
    <?= trim(str_repeat('--', $level) .' ' .$name); ?>
</option>

