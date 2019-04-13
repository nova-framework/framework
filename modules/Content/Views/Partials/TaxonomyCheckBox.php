<?php dd(get_defined_vars()); ?>

<div class="checkbox">
    <label>
        <input class="<?= $type; ?>-checkbox" name="taxonomy[]" value="<?= $taxonomy->id; ?>" type="checkbox" <?= in_array($taxonomy->id, $selected) ? ' checked="checked"' : ''; ?>>
        <?= $label; ?>
    </label>
</div>
